#!/usr/bin/env bash
# This is a script designed to automate the assembly of the FreeNAS OS.
# Created: 2/12/2006 by Scott Zahn
# Modified by Volker Theile (votdev@gmx.de)

# Debug script
#set -x

################################################################################
# Settings
################################################################################

# Global variables
FREENAS_ROOTDIR="/usr/local/freenas"
FREENAS_WORKINGDIR="$FREENAS_ROOTDIR/work"
FREENAS_ROOTFS="$FREENAS_ROOTDIR/rootfs"
FREENAS_SVNDIR="$FREENAS_ROOTDIR/svn"
FREENAS_WORLD=""
FREENAS_PRODUCTNAME=$(cat $FREENAS_SVNDIR/etc/prd.name)
FREENAS_VERSION=$(cat $FREENAS_SVNDIR/etc/prd.version)
FREENAS_ARCH=$(uname -p)
FREENAS_KERNCONF="$(echo ${FREENAS_PRODUCTNAME} | tr '[:lower:]' '[:upper:]')-${FREENAS_ARCH}"
FREENAS_OBJDIRPREFIX="/usr/obj/$(echo ${FREENAS_PRODUCTNAME} | tr '[:upper:]' '[:lower:]')"

export FREENAS_ROOTDIR
export FREENAS_WORKINGDIR
export FREENAS_ROOTFS
export FREENAS_SVNDIR
export FREENAS_WORLD
export FREENAS_PRODUCTNAME
export FREENAS_VERSION
export FREENAS_ARCH
export FREENAS_KERNCONF
export FREENAS_OBJDIRPREFIX

# Local variables
FREENAS_URL=$(cat $FREENAS_SVNDIR/etc/prd.url)
FREENAS_BOOTDIR="$FREENAS_ROOTDIR/bootloader"
FREENAS_TMPDIR="/tmp/freenastmp"
FREENAS_SVNURL="https://freenas.svn.sourceforge.net/svnroot/freenas/trunk"

# Path where to find Makefile includes
FREENAS_MKINCLUDESDIR="$FREENAS_SVNDIR/build/mk"

# Size in MB of the MFS Root filesystem that will include all FreeBSD binary
# and FreeNAS WEbGUI/Scripts. Keep this file very small! This file is unzipped
# to a RAM disk at FreeNAS startup.
if [ $FREENAS_ARCH == "amd64" ]; then
	echo "AMD arch detected, increasing the Size of MFS Root file"
	FREENAS_MFSROOT_SIZE="54"
else
	FREENAS_MFSROOT_SIZE="50"
fi

# IMG media size in 512 bytes sectors. It includes the zipped MFS root
# filesystem image plus bootloader and kernel. ( 49254400 bytes => 25MB)
if [ $FREENAS_ARCH == "amd64" ]; then
	echo "AMD arch detected, increasing the Size of MFS Root file"
	FREENAS_IMG_SIZE=57344
else
	FREENAS_IMG_SIZE=51200
fi
# Media geometry, only relevant if bios doesn't understand LBA.
FREENAS_IMG_SECTS=32
FREENAS_IMG_HEADS=16
# 'newfs' parameters.
FREENAS_NEWFS="-U -o space -m 0"

# Options:
# Support bootmenu
OPT_BOOTMENU=1
# Support bootsplash
OPT_BOOTSPLASH=1

# Dialog command
DIALOG="dialog"

################################################################################
# Functions
################################################################################

# Update source tree and ports collection.
update_sources() {
	tempfile=$FREENAS_WORKINGDIR/tmp$$

	# Choose what to do.
	$DIALOG --title "$FREENAS_PRODUCTNAME - Update sources" --checklist "Please select what to update." 10 60 3 \
		"cvsup" "Update source tree" OFF \
		"freebsd-update" "Fetch and install binary updates" OFF \
		"portsnap" "Update ports collection" OFF 2> $tempfile
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi

	choices=`cat $tempfile`
	rm $tempfile

	for choice in $(echo $choices | tr -d '"'); do
		case $choice in
			freebsd-update)
				freebsd-update fetch install;;
			portsnap)
				portsnap fetch update;;
			cvsup)
				csup -L 2 ${FREENAS_SVNDIR}/build/source-supfile;;
  	esac
  done

	return $?
}

# Build world. Copying required files defined in 'build/freenas.files'.
build_world() {
	# Make a pseudo 'chroot' to FreeNAS root.
  cd $FREENAS_ROOTFS

	echo
	echo "Building world:"

	[ -f $FREENAS_WORKINGDIR/freenas.files ] && rm -f $FREENAS_WORKINGDIR/freenas.files
	cp $FREENAS_SVNDIR/build/freenas.files $FREENAS_WORKINGDIR

	# Add custom binaries
	if [ -f $FREENAS_WORKINGDIR/freenas.custfiles ]; then
		cat $FREENAS_WORKINGDIR/freenas.custfiles >> $FREENAS_WORKINGDIR/freenas.files
	fi

	for i in $(cat $FREENAS_WORKINGDIR/freenas.files | grep -v "^#"); do
		file=$(echo "$i" | cut -d ":" -f 1)

		# Deal with directories
		dir=$(dirname $file)
		if [ ! -d $dir ]; then
		  mkdir -pv $dir
		fi

		# Copy files from world.
		cp -pv ${FREENAS_WORLD}/$file $(echo $file | rev | cut -d "/" -f 2- | rev)

		# Deal with links
		if [ $(echo "$i" | grep -c ":") -gt 0 ]; then
			for j in $(echo $i | cut -d ":" -f 2- | sed "s/:/ /g"); do
				ln -sv /$file $j
			done
		fi
	done

	# Cleanup
	rm -f $FREENAS_WORKINGDIR/freenas.files

	return 0
}

# Create rootfs
create_rootfs() {
	$FREENAS_SVNDIR/build/freenas-create-rootfs.sh -f $FREENAS_ROOTFS

	# Configuring platform variable
	echo $FREENAS_VERSION > $FREENAS_ROOTFS/etc/prd.version

	# Config file: config.xml
	cd $FREENAS_ROOTFS/conf.default/
	cp -v $FREENAS_SVNDIR/conf/config.xml .

	# Compress zoneinfo data, exclude some useless files.
	mkdir $FREENAS_TMPDIR
	echo "Factory" > $FREENAS_TMPDIR/zoneinfo.exlude
	echo "posixrules" >> $FREENAS_TMPDIR/zoneinfo.exlude
	echo "zone.tab" >> $FREENAS_TMPDIR/zoneinfo.exlude
	tar -c -v -f - -X $FREENAS_TMPDIR/zoneinfo.exlude -C /usr/share/zoneinfo/ . | gzip -cv > $FREENAS_ROOTFS/usr/share/zoneinfo.tgz
	rm $FREENAS_TMPDIR/zoneinfo.exlude

	return 0
}

# Actions before building kernel (e.g. install special/additional drivers).
pre_build_kernel() {
	tempfile=$FREENAS_WORKINGDIR/tmp$$
	drivers=$FREENAS_WORKINGDIR/drivers$$

	# Create list of available packages.
	echo "#! /bin/sh
$DIALOG --title \"$FREENAS_PRODUCTNAME - Drivers\" \\
--checklist \"Select the drivers you want to add. Make sure you have clean/origin kernel sources (via cvsup) to apply patches successful.\" 22 75 14 \\" > $tempfile

	for s in $FREENAS_SVNDIR/build/drivers/*; do
		[ ! -d "$s" ] && continue
		package=`basename $s`
		desc=`cat $s/pkg-descr`
		state=`cat $s/pkg-state`
		echo "\"$package\" \"$desc\" $state \\" >> $tempfile
	done

	# Display list of available drivers.
	sh $tempfile 2> $drivers
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi
	rm $tempfile

	for driver in $(cat $drivers | tr -d '"'); do
    echo
		echo "--------------------------------------------------------------"
		echo ">>> Adding driver: ${driver}"
		echo "--------------------------------------------------------------"
		cd $FREENAS_SVNDIR/build/drivers/$driver
		make -I ${FREENAS_MKINCLUDESDIR} install
		[ 0 != $? ] && return 1 # successful?
	done
	rm $drivers
}

# Building the kernel
build_kernel() {
	tempfile=$FREENAS_WORKINGDIR/tmp$$

	# Make sure kernel directory exists.
	[ ! -d "${FREENAS_ROOTFS}/boot/kernel" ] && mkdir -p ${FREENAS_ROOTFS}/boot/kernel

	# Choose what to do.
	$DIALOG --title "$FREENAS_PRODUCTNAME - Build/Install kernel" --checklist "Please select whether you want to build or install the kernel." 10 75 3 \
		"prebuild" "Install additional drivers" OFF \
		"build" "Build kernel" OFF \
		"install" "Install kernel + modules" ON 2> $tempfile
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi

	choices=`cat $tempfile`
	rm $tempfile

	for choice in $(echo $choices | tr -d '"'); do
		case $choice in
			prebuild)
				# Adding specials drivers.
				pre_build_kernel;
				[ 0 != $? ] && return 1;; # successful?
			build)
				# Copy kernel configuration.
				cd /sys/${FREENAS_ARCH}/conf;
				cp -f $FREENAS_SVNDIR/build/kernel-config/${FREENAS_KERNCONF} .;
				# Clean object directory.
				rm -f -r ${FREENAS_OBJDIRPREFIX};
				# Compiling and compressing the kernel.
				cd /usr/src;
				env MAKEOBJDIRPREFIX=${FREENAS_OBJDIRPREFIX} make buildkernel KERNCONF=${FREENAS_KERNCONF};
				gzip -c -v -f -9 ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/kernel > ${FREENAS_WORKINGDIR}/kernel.gz;;
			install)
				# Installing the modules.
				cd ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules;
				cp -v -p ./geom/geom_vinum/geom_vinum.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_stripe/geom_stripe.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_concat/geom_concat.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_mirror/geom_mirror.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_nop/geom_nop.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./ext2fs/ext2fs.ko $FREENAS_ROOTFS/boot/kernel;;
  	esac
  done

	return 0
}

# Adding the libraries
add_libs() {
	echo
	echo "Adding required libs:"

	# Identify required libs.
	[ -f /tmp/lib.list ] && rm -f /tmp/lib.list
	dirs=($FREENAS_ROOTFS/bin $FREENAS_ROOTFS/sbin $FREENAS_ROOTFS/usr/bin $FREENAS_ROOTFS/usr/sbin $FREENAS_ROOTFS/usr/local/bin $FREENAS_ROOTFS/usr/local/sbin $FREENAS_ROOTFS/usr/lib $FREENAS_ROOTFS/usr/local/lib)
	for i in ${dirs[@]}; do
		for file in $(ls $i); do
			ldd -f "%p\n" $i/$file 2> /dev/null >> /tmp/lib.list
		done
	done

	# Copy identified libs.
	for i in $(sort -u /tmp/lib.list); do
		cp -vp ${FREENAS_WORLD}${i} ${FREENAS_ROOTFS}$(echo $i | rev | cut -d '/' -f 2- | rev)
	done

	# Cleanup.
	rm -f /tmp/lib.list

  return 0
}

# Creating msfroot
create_mfsroot() {
	echo "--------------------------------------------------------------"
	echo ">>> Generating the MFSROOT filesystem"
	echo "--------------------------------------------------------------"

	cd $FREENAS_WORKINGDIR

	[ -f $FREENAS_WORKINGDIR/mfsroot.gz ] && rm -f $FREENAS_WORKINGDIR/mfsroot.gz
	[ -d $FREENAS_SVNDIR ] && use_svn ;

	# Make mfsroot to have the size of the FREENAS_MFSROOT_SIZE variable
	dd if=/dev/zero of=$FREENAS_WORKINGDIR/mfsroot bs=1M count=${FREENAS_MFSROOT_SIZE}
	# Configure this file as a memory disk
	md=`mdconfig -a -t vnode -f $FREENAS_WORKINGDIR/mfsroot`
	# Create label on memory disk
	bsdlabel -m ${FREENAS_ARCH} -w ${md} auto
	# Format memory disk using UFS
	newfs ${FREENAS_NEWFS} /dev/${md}c
	# Umount memory disk (if already used)
	umount $FREENAS_TMPDIR >/dev/null 2>&1
	# Mount memory disk
	mount /dev/${md} ${FREENAS_TMPDIR}
	cd $FREENAS_TMPDIR
	tar -cf - -C $FREENAS_ROOTFS ./ | tar -xvpf -

	cd $FREENAS_WORKINGDIR
	# Umount memory disk
	umount $FREENAS_TMPDIR
	# Detach memory disk
	mdconfig -d -u ${md}

	gzip -9 $FREENAS_WORKINGDIR/mfsroot

	return 0
}

create_image() {
	echo "--------------------------------------------------------------"
	echo ">>> Generating ${FREENAS_PRODUCTNAME} IMG File (to be rawrite on CF/USB/HD)"
	echo "--------------------------------------------------------------"

	# Check if rootfs (contining OS image) exists.
	if [ ! -d "$FREENAS_ROOTFS" ]; then
		echo "==> Error: ${FREENAS_ROOTFS} does not exist."
		return 1
	fi

	# Cleanup.
	[ -f image.bin ] && rm -f image.bin

	# Set platform information.
	PLATFORM="${FREENAS_ARCH}-embedded"
	echo $PLATFORM > ${FREENAS_ROOTFS}/etc/platform

	# Set build time.
	date > ${FREENAS_ROOTFS}/etc/prd.version.buildtime

	IMGFILENAME="${FREENAS_PRODUCTNAME}-${PLATFORM}-${FREENAS_VERSION}.img"

	echo "===> Generating tempory $FREENAS_TMPDIR folder"
	mkdir $FREENAS_TMPDIR
	create_mfsroot;

	echo "===> Creating an empty IMG file"
	dd if=/dev/zero of=${FREENAS_WORKINGDIR}/image.bin bs=${FREENAS_IMG_SECTS}b count=`expr ${FREENAS_IMG_SIZE} / ${FREENAS_IMG_SECTS}`
	echo "===> Use IMG as a memory disk"
	md=`mdconfig -a -t vnode -f ${FREENAS_WORKINGDIR}/image.bin -x ${FREENAS_IMG_SECTS} -y ${FREENAS_IMG_HEADS}`
	diskinfo -v ${md}
	echo "===> Creating partition on this memory disk"
	fdisk -BI -b $FREENAS_BOOTDIR/mbr ${md}
	echo "===> Configuring FreeBSD label on this memory disk"
	echo "
# /dev/${md}:
8 partitions:
#        size   offset    fstype   [fsize bsize bps/cpg]
  a:    ${FREENAS_IMG_SIZE}        0    4.2BSD        0     0
  c:    *            *    unused        0     0         # "raw" part, don't edit
" > ${FREENAS_WORKINGDIR}/bsdlabel.$$
	bsdlabel -m ${FREENAS_ARCH} -R -B -b ${FREENAS_BOOTDIR}/boot ${md} ${FREENAS_WORKINGDIR}/bsdlabel.$$
	bsdlabel ${md}
	echo "===> Formatting this memory disk using UFS"
	newfs ${FREENAS_NEWFS} /dev/${md}a
	echo "===> Mount this virtual disk on $FREENAS_TMPDIR"
	mount /dev/${md}a $FREENAS_TMPDIR
	echo "===> Copying previously generated MFSROOT file to memory disk"
	cp $FREENAS_WORKINGDIR/mfsroot.gz $FREENAS_TMPDIR

	echo "===> Copying bootloader file(s) to memory disk"
	mkdir $FREENAS_TMPDIR/boot
	mkdir $FREENAS_TMPDIR/boot/kernel $FREENAS_TMPDIR/boot/defaults
	mkdir $FREENAS_TMPDIR/conf
	cp $FREENAS_ROOTFS/conf.default/config.xml $FREENAS_TMPDIR/conf
	cp $FREENAS_BOOTDIR/kernel/kernel.gz $FREENAS_TMPDIR/boot/kernel
	cp $FREENAS_BOOTDIR/boot $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader.conf $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader.rc $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader.4th $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/support.4th $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/defaults/loader.conf $FREENAS_TMPDIR/boot/defaults/
	cp $FREENAS_BOOTDIR/device.hints $FREENAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $FREENAS_SVNDIR/boot/menu.4th $FREENAS_TMPDIR/boot
		cp $FREENAS_BOOTDIR/screen.4th $FREENAS_TMPDIR/boot
		cp $FREENAS_BOOTDIR/frames.4th $FREENAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $FREENAS_SVNDIR/boot/splash.bmp $FREENAS_TMPDIR/boot
		cp ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $FREENAS_TMPDIR/boot/kernel
	fi

	echo "===> Unmount memory disk"
	umount $FREENAS_TMPDIR
	echo "===> Detach memory disk"
	mdconfig -d -u ${md}
	echo "===> Compress the IMG file"
	gzip -9 $FREENAS_WORKINGDIR/image.bin
	mv $FREENAS_WORKINGDIR/image.bin.gz $FREENAS_ROOTDIR/$IMGFILENAME

	# Cleanup.
	echo "===> Cleaning temporary files"
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	[ -f $FREENAS_WORKINGDIR/mfsroot.gz ] && rm -f $FREENAS_WORKINGDIR/mfsroot.gz
	[ -f $FREENAS_WORKINGDIR/image.bin ] && rm -f $FREENAS_WORKINGDIR/image.bin
	[ -f $FREENAS_WORKINGDIR/bsdlabel.$$ ] && rm -f $FREENAS_WORKINGDIR/bsdlabel.$$

	return 0
}

create_iso () {
	# Check if rootfs (contining OS image) exists.
	if [ ! -d "$FREENAS_ROOTFS" ]; then
		echo "==> Error: ${FREENAS_ROOTFS} does not exist."
		return 1
	fi

	# Cleanup.
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	[ -f $FREENAS_WORKINGDIR/mfsroot.gz ] && rm -f $FREENAS_WORKINGDIR/mfsroot.gz

	ISOFILENAME="${FREENAS_PRODUCTNAME}-${FREENAS_ARCH}-liveCD-${FREENAS_VERSION}.iso"

	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Generating the $FREENAS_PRODUCTNAME Image file:"
		create_image;
	fi

	# Set platform information.
	PLATFORM="${FREENAS_ARCH}-liveCD"
	echo $PLATFORM > ${FREENAS_ROOTFS}/etc/platform

	echo "ISO: Generating temporary folder '$FREENAS_TMPDIR'"
	mkdir $FREENAS_TMPDIR
	create_mfsroot;

	echo "ISO: Copying previously generated MFSROOT file to $FREENAS_TMPDIR"
	cp $FREENAS_WORKINGDIR/mfsroot.gz $FREENAS_TMPDIR

	echo "ISO: Copying bootloader file(s) to $FREENAS_TMPDIR"
	mkdir $FREENAS_TMPDIR/boot
	mkdir $FREENAS_TMPDIR/boot/kernel $FREENAS_TMPDIR/boot/defaults
	cp $FREENAS_BOOTDIR/kernel/kernel.gz $FREENAS_TMPDIR/boot/kernel
	cp $FREENAS_BOOTDIR/cdboot $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader.conf $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader.rc $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader.4th $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/support.4th $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/defaults/loader.conf $FREENAS_TMPDIR/boot/defaults/
	cp $FREENAS_BOOTDIR/device.hints $FREENAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $FREENAS_SVNDIR/boot/menu.4th $FREENAS_TMPDIR/boot
		cp $FREENAS_BOOTDIR/screen.4th $FREENAS_TMPDIR/boot
		cp $FREENAS_BOOTDIR/frames.4th $FREENAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $FREENAS_SVNDIR/boot/splash.bmp $FREENAS_TMPDIR/boot
		cp ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $FREENAS_TMPDIR/boot/kernel
	fi

	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Copying IMG file to $FREENAS_TMPDIR"
		cp $FREENAS_ROOTDIR/$FREENAS_PRODUCTNAME-$FREENAS_ARCH-embedded-$FREENAS_VERSION.img $FREENAS_TMPDIR/$FREENAS_PRODUCTNAME-$FREENAS_ARCH-embedded.gz
	fi

	echo "ISO: Generating the ISO file"
	mkisofs -b "boot/cdboot" -no-emul-boot -boot-load-size 4 -c "boot/boot.catalog" -d -r -A "${FREENAS_PRODUCTNAME} CD-ROM image" -publisher "${FREENAS_URL}" -p "Olivier Cochard-Labbe" -V "${FREENAS_PRODUCTNAME}_cd" -o "${FREENAS_ROOTDIR}/${ISOFILENAME}" ${FREENAS_TMPDIR}
	[ 0 != $? ] && return 1 # successful?

	echo "ISO: Cleaning tempo file"
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	[ -f $FREENAS_WORKINGDIR/mfsroot.gz ] && rm -f $FREENAS_WORKINGDIR/mfsroot.gz

	return 0
}

create_iso_light() {
	LIGHT_ISO=1
	create_iso;
	return 0
}

create_full() {
	[ -d $FREENAS_SVNDIR ] && use_svn ;

	echo "FULL: Generating $FREENAS_PRODUCTNAME tgz update file"

	# Set platform information.
	PLATFORM="${FREENAS_ARCH}-full"
	echo $PLATFORM > ${FREENAS_ROOTFS}/etc/platform

	FULLFILENAME="${FREENAS_PRODUCTNAME}-${PLATFORM}-${FREENAS_VERSION}.tgz"

	echo "FULL: Generating tempory $FREENAS_TMPDIR folder"
	#Clean TMP dir:
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	mkdir $FREENAS_TMPDIR

	#Copying all FreeNAS rootfilesystem (including symlink) on this folder
	cd $FREENAS_TMPDIR
	tar -cf - -C $FREENAS_ROOTFS ./ | tar -xvpf -
	#tar -cf - -C $FREENAS_ROOTFS ./ | tar -xvpf - -C $FREENAS_TMPDIR

	echo "Copying bootloader file(s) to root filesystem"
	mkdir $FREENAS_TMPDIR/boot/defaults
	#mkdir $FREENAS_TMPDIR/conf
	cp $FREENAS_ROOTFS/conf.default/config.xml $FREENAS_TMPDIR/conf
	cp $FREENAS_BOOTDIR/kernel/kernel.gz $FREENAS_TMPDIR/boot/kernel
	gunzip $FREENAS_TMPDIR/boot/kernel/kernel.gz
	cp $FREENAS_BOOTDIR/boot $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader.rc $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/loader.4th $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/support.4th $FREENAS_TMPDIR/boot
	cp $FREENAS_BOOTDIR/defaults/loader.conf $FREENAS_TMPDIR/boot/defaults/
	cp $FREENAS_BOOTDIR/device.hints $FREENAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $FREENAS_SVNDIR/boot/menu.4th $FREENAS_TMPDIR/boot
		cp $FREENAS_BOOTDIR/screen.4th $FREENAS_TMPDIR/boot
		cp $FREENAS_BOOTDIR/frames.4th $FREENAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $FREENAS_SVNDIR/boot/splash.bmp $FREENAS_TMPDIR/boot
		cp ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $FREENAS_TMPDIR/boot/kernel
	fi

	#Generate a loader.conf for full mode:
	echo 'kernel="kernel"' >> $FREENAS_TMPDIR/boot/loader.conf
	echo 'bootfile="kernel"' >> $FREENAS_TMPDIR/boot/loader.conf
	echo 'kernel_options=""' >> $FREENAS_TMPDIR/boot/loader.conf
	echo 'kern.hz="100"' >> $FREENAS_TMPDIR/boot/loader.conf
	echo 'splash_bmp_load="YES"' >> $FREENAS_TMPDIR/boot/loader.conf
	echo 'bitmap_load="YES"' >> $FREENAS_TMPDIR/boot/loader.conf
	echo 'bitmap_name="/boot/splash.bmp"' >> $FREENAS_TMPDIR/boot/loader.conf

	#Check that there is no /etc/fstab file! This file can be generated only during install, and must be kept
	[ -f $FREENAS_TMPDIR/etc/fstab ] && rm -f $FREENAS_TMPDIR/etc/fstab

	#Check that there is no /etc/cfdevice file! This file can be generated only during install, and must be kept
	[ -f $FREENAS_TMPDIR/etc/cfdevice ] && rm -f $FREENAS_TMPDIR/etc/cfdevice

	echo "FULL: tgz the directory"
	cd $FREENAS_ROOTDIR
	tar cvfz $FULLFILENAME -C $FREENAS_TMPDIR ./

	# Cleanup.
	echo "Cleaning tempo file"
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR

	return 0
}

# Update subversion sources.
update_svn() {
	cd $FREENAS_ROOTDIR
	svn co $FREENAS_SVNURL svn

	return 0
}

use_svn() {
	echo "===> Replacing old code with SVN code"

	cp -pv ${FREENAS_SVNDIR}/root/.cshrc ${FREENAS_ROOTFS}/root
	cp -pv ${FREENAS_SVNDIR}/root/.profile ${FREENAS_ROOTFS}/root

	cd $FREENAS_SVNDIR/etc
	cp -v -p * $FREENAS_ROOTFS/etc

	cd $FREENAS_SVNDIR/etc/inc
	cp -v -p * $FREENAS_ROOTFS/etc/inc

	cd $FREENAS_SVNDIR/etc/inc/phpmailer
	cp -v -p * $FREENAS_ROOTFS/etc/inc/phpmailer

	cd $FREENAS_SVNDIR/etc/defaults
	cp -v -p * $FREENAS_ROOTFS/etc/defaults

	cd $FREENAS_SVNDIR/etc/mail
	cp -v -p * $FREENAS_ROOTFS/etc/mail

	cd $FREENAS_SVNDIR/etc/rc.d
	cp -v -p * $FREENAS_ROOTFS/etc/rc.d

	cd $FREENAS_SVNDIR/etc/rc.d.php
	cp -v -p * $FREENAS_ROOTFS/etc/rc.d.php

	cd $FREENAS_SVNDIR/etc/pam.d
	cp -v -p * $FREENAS_ROOTFS/etc/pam.d

	cd $FREENAS_SVNDIR/www
	cp -v -p * $FREENAS_ROOTFS/usr/local/www

	cd $FREENAS_SVNDIR/www/syntaxhighlighter
	cp -v -p * $FREENAS_ROOTFS/usr/local/www/syntaxhighlighter

	cd $FREENAS_SVNDIR/conf
	cp -v -p * $FREENAS_ROOTFS/conf.default

	return 0
}

build_system() {
  while true; do
echo -n '
Bulding system from scratch
Menu:
1 - Update source tree and ports collection
2 - Create filesystem structure
3 - Build kernel
4 - Build world
5 - Build ports
6 - Build bootloader
7 - Add necessary libraries
8 - Modify file permissions
* - Quit
> '
		read choice
		case $choice in
			1)	update_sources;;
			2)	create_rootfs;;
			3)	build_kernel;;
			4)	build_world;;
			5)	build_ports;;
			6)	opt="-f";
					if [ 0 != $OPT_BOOTMENU ]; then
						opt="$opt -m"
					fi;
					if [ 0 != $OPT_BOOTSPLASH ]; then
						opt="$opt -b"
					fi;
					$FREENAS_SVNDIR/build/freenas-create-bootdir.sh $opt $FREENAS_BOOTDIR;;
			7)	add_libs;;
			8)	$FREENAS_SVNDIR/build/freenas-modify-permissions.sh $FREENAS_ROOTFS;;
			*)	main;;
		esac
		[ 0 == $? ] && echo "=> Successful" || echo "=> Failed"
		sleep 1
  done
}

build_ports() {
	tempfile=$FREENAS_WORKINGDIR/tmp$$
	ports=$FREENAS_WORKINGDIR/ports$$

	# Choose what to do.
	$DIALOG --title "$FREENAS_PRODUCTNAME - Build/Install Ports" --menu "Please select whether you want to build or install ports." 10 45 2 \
		"build" "Build ports" \
		"install" "Install ports" 2> $tempfile
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi

	choice=`cat $tempfile`
	rm $tempfile

	# Create list of available ports.
	echo "#! /bin/sh
$DIALOG --title \"$FREENAS_PRODUCTNAME - Ports\" \\
--checklist \"Select the ports you want to process.\" 21 75 14 \\" > $tempfile

	for s in $FREENAS_SVNDIR/build/ports/*; do
		[ ! -d "$s" ] && continue
		port=`basename $s`
		desc=`cat $s/pkg-descr`
		state=`cat $s/pkg-state`
		case ${state} in
			[hH][iI][dD][eE])
				;;
			*)
				echo "\"$port\" \"$desc\" $state \\" >> $tempfile;
				;;
		esac
	done

	# Display list of available ports.
	sh $tempfile 2> $ports
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		rm $ports
		return 1
	fi
	rm $tempfile

	for port in $(cat $ports | tr -d '"'); do
		echo
		echo "--------------------------------------------------------------"
		echo ">>> ${choice}ing port: ${port}"
		echo "--------------------------------------------------------------"
		cd $FREENAS_SVNDIR/build/ports/$port
		if [ "$choice" == "build" ]; then
			# Build port.
			make clean build
		elif [ "$choice" == "install" ]; then
			# Delete cookie first, otherwise Makefile will skip this step.
			rm -f ./work/.install_done.*
			# Install port.
			env NO_PKG_REGISTER=1 make install
		fi
		[ 0 != $? ] && return 1 # successful?
	done
	rm $ports

  return 0
}

main() {
	# Ensure we are in $FREENAS_WORKINGDIR
	[ ! -d "$FREENAS_WORKINGDIR" ] && mkdir $FREENAS_WORKINGDIR
	cd $FREENAS_WORKINGDIR

	echo -n "
Welcome to the ${FREENAS_PRODUCTNAME} build environment.
Menu:
1  - Update the sources to CURRENT
2  - Build system from scratch
10 - Create 'Embedded' (IMG) file (rawrite to CF/USB/DD)
11 - Create 'LiveCD' (ISO) file
12 - Create 'LiveCD' (ISO) file without 'Embedded' file
13 - Create 'Full' (TGZ) update file
*  - Quit
> "
	read choice
	case $choice in
		1)	update_svn;;
		2)	build_system;;
		10)	create_image;;
		11)	create_iso;;
		12)	create_iso_light;;
		13)	create_full;;
		*)	exit 0;;
	esac

	[ 0 == $? ] && echo "=> Successful" || echo "=> Failed"
	sleep 1

	return 0
}

while true; do
	main
done
exit 0
