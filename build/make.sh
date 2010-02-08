#!/usr/bin/env bash
# This is a script designed to automate the assembly of the FreeNAS OS.
# Created: 2/12/2006 by Scott Zahn
# Modified by Volker Theile (votdev@gmx.de)

# Debug script
#set -x

# Exit if not managed error
# http://www.davidpashley.com/articles/writing-robust-shell-scripts.html
# "This tells bash that it should exit the script if any statement returns a non-true return value. 
# The benefit of using -e is that it prevents errors snowballing into serious issues when they could have been caught earlier"
set -e

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
FREENAS_REVISION=$(svnversion -n ${FREENAS_SVNDIR})
FREENAS_ARCH=$(uname -p)
FREENAS_KERNCONF="$(echo ${FREENAS_PRODUCTNAME} | tr '[:lower:]' '[:upper:]')-${FREENAS_ARCH}"
FREENAS_OBJDIRPREFIX="/usr/obj/$(echo ${FREENAS_PRODUCTNAME} | tr '[:upper:]' '[:lower:]')"
FREENAS_BOOTDIR="$FREENAS_ROOTDIR/bootloader"
FREENAS_TMPDIR="/tmp/freenastmp"

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
export FREENAS_BOOTDIR
export FREENAS_REVISION
export FREENAS_TMPDIR

# Local variables
FREENAS_URL=$(cat $FREENAS_SVNDIR/etc/prd.url)
FREENAS_SVNURL="https://freenas.svn.sourceforge.net/svnroot/freenas/branches/0.7"

# Size in MB of the MFS Root filesystem that will include all FreeBSD binary
# and FreeNAS WEbGUI/Scripts. Keep this file very small! This file is unzipped
# to a RAM disk at FreeNAS startup.
FREENAS_MFSROOT_SIZE=96
FREENAS_IMG_SIZE=42

# Media geometry, only relevant if bios doesn't understand LBA.
FREENAS_IMG_SIZE_SEC=`expr ${FREENAS_IMG_SIZE} \* 2048`
FREENAS_IMG_SECTS=32
FREENAS_IMG_HEADS=16

# Options:
# Support bootmenu
OPT_BOOTMENU=1
# Support bootsplash
OPT_BOOTSPLASH=0
# Support serial console
OPT_SERIALCONSOLE=0

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
		"portsnap" "Update ports collection" OFF \
		"portupgrade" "Upgrade ports on host" OFF 2> $tempfile
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
			portupgrade)
				portupgrade -aFP;;
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
		for xfile in ${FREENAS_WORLD}/$file; do
		    _file="${xfile#${FREENAS_WORLD}/}"
		    if [ -e "$xfile" ]; then
			if ! cp -fRpv $xfile $(echo $_file | rev | cut -d "/" -f 2- | rev) ; then
			    echo "can't copy $xfile"
			fi
		    else
			echo "WARNING, Missing file: $xfile"
		    fi
		done

		# Deal with links
		if [ $(echo "$i" | grep -c ":") -gt 0 ]; then
			for j in $(echo $i | cut -d ":" -f 2- | sed "s/:/ /g"); do
				ln -fsv /$file $j
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
	echo ${FREENAS_VERSION} > ${FREENAS_ROOTFS}/etc/prd.version

	# Config file: config.xml
	cd $FREENAS_ROOTFS/conf.default/
	cp -v $FREENAS_SVNDIR/conf/config.xml .

	# Compress zoneinfo data, exclude some useless files.
	if [ ! -d $FREENAS_TMPDIR ] ; then
		mkdir $FREENAS_TMPDIR
	fi
	echo "Factory" > $FREENAS_TMPDIR/zoneinfo.exlude
	echo "posixrules" >> $FREENAS_TMPDIR/zoneinfo.exlude
	echo "zone.tab" >> $FREENAS_TMPDIR/zoneinfo.exlude
	tar -c -v -f - -X $FREENAS_TMPDIR/zoneinfo.exlude -C /usr/share/zoneinfo/ . | gzip -cv > $FREENAS_ROOTFS/usr/share/zoneinfo.tgz
	rm $FREENAS_TMPDIR/zoneinfo.exlude

	return 0
}

# Actions before building kernel (e.g. install special/additional kernel patches).
pre_build_kernel() {
	tempfile=$FREENAS_WORKINGDIR/tmp$$
	patches=$FREENAS_WORKINGDIR/patches$$

	# Create list of available packages.
	echo "#! /bin/sh
$DIALOG --title \"$FREENAS_PRODUCTNAME - Kernel patches\" \\
--checklist \"Select the patches you want to add. Make sure you have clean/origin kernel sources (via cvsup) to apply patches successful.\" 22 75 14 \\" > $tempfile

	for s in $FREENAS_SVNDIR/build/kernel-patches/*; do
		[ ! -d "$s" ] && continue
		package=`basename $s`
		desc=`cat $s/pkg-descr`
		state=`cat $s/pkg-state`
		echo "\"$package\" \"$desc\" $state \\" >> $tempfile
	done

	# Display list of available kernel patches.
	sh $tempfile 2> $patches
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi
	rm $tempfile

	echo "Remove old patched files..."
	for file in $(find /usr/src -name "*.orig"); do
		rm -rv ${file}
	done

	for patch in $(cat $patches | tr -d '"'); do
    echo
		echo "--------------------------------------------------------------"
		echo ">>> Adding kernel patch: ${patch}"
		echo "--------------------------------------------------------------"
		cd $FREENAS_SVNDIR/build/kernel-patches/$patch
		make install
		[ 0 != $? ] && return 1 # successful?
	done
	rm $patches
}

# Building the kernel
build_kernel() {
	tempfile=$FREENAS_WORKINGDIR/tmp$$

	# Make sure kernel directory exists.
	[ ! -d "${FREENAS_ROOTFS}/boot/kernel" ] && mkdir -p ${FREENAS_ROOTFS}/boot/kernel

	# Choose what to do.
	$DIALOG --title "$FREENAS_PRODUCTNAME - Build/Install kernel" --checklist "Please select whether you want to build or install the kernel." 10 75 3 \
		"prebuild" "Apply kernel patches" OFF \
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
				# Apply kernel patches.
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
				gzip -9cnv ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/kernel > ${FREENAS_WORKINGDIR}/kernel.gz;;
			install)
				# Installing the modules.
				echo "--------------------------------------------------------------";
				echo ">>> Install kernel modules";
				echo "--------------------------------------------------------------";

				[ -f ${FREENAS_WORKINGDIR}/modules.files ] && rm -f ${FREENAS_WORKINGDIR}/modules.files;
				cp ${FREENAS_SVNDIR}/build/kernel-config/modules.files ${FREENAS_WORKINGDIR};

				modulesdir=${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules;
				for module in $(cat ${FREENAS_WORKINGDIR}/modules.files | grep -v "^#"); do
					install -v -o root -g wheel -m 555 ${modulesdir}/${module} ${FREENAS_ROOTFS}/boot/kernel
				done;;
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
	dirs=(${FREENAS_ROOTFS}/bin ${FREENAS_ROOTFS}/sbin ${FREENAS_ROOTFS}/usr/bin ${FREENAS_ROOTFS}/usr/sbin ${FREENAS_ROOTFS}/usr/local/bin ${FREENAS_ROOTFS}/usr/local/sbin ${FREENAS_ROOTFS}/usr/lib ${FREENAS_ROOTFS}/usr/local/lib ${FREENAS_ROOTFS}/usr/libexec ${FREENAS_ROOTFS}/usr/local/libexec)
	for i in ${dirs[@]}; do
		for file in $(find -L ${i} -type f -print); do
            set +e
			ldd -f "%p\n" ${file} 2> /dev/null >> /tmp/lib.list
            set -e
		done
	done

	# Copy identified libs.
	for i in $(sort -u /tmp/lib.list); do
		if [ -e "${FREENAS_WORLD}${i}" ]; then
			install -c -s -v ${FREENAS_WORLD}${i} ${FREENAS_ROOTFS}$(echo $i | rev | cut -d '/' -f 2- | rev)
		fi
	done

	# Cleanup.
	rm -f /tmp/lib.list || { echo "Can't rm /tmp/lib.list"; exit 1; } 

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
	dd if=/dev/zero of=$FREENAS_WORKINGDIR/mfsroot bs=1k count=$(expr ${FREENAS_MFSROOT_SIZE} \* 1024)
	# Configure this file as a memory disk
	md=`mdconfig -a -t vnode -f $FREENAS_WORKINGDIR/mfsroot`
	# Create label on memory disk
	bsdlabel -m ${FREENAS_ARCH} -w ${md} auto || { echo "Can't bsdlabel the MFSroot md"; exit 1; } 
	# Format memory disk using UFS
	newfs -O2 -o space -m 0 /dev/${md} || { echo "Can't newfs the /dev/$(md)"; exit 1; } 
	# Umount memory disk (if already used)
    if [ `mount | grep -q $FREENAS_TMPDIR` ]; then
        echo "Detected allready mounted workdir $FREENAS_TMPDIR"
        umount $FREENAS_TMPDIR >/dev/null 2>&1
    fi
	# Mount memory disk
	mount /dev/${md} ${FREENAS_TMPDIR} || { echo "Can't mount /dev/$(md)";
 exit 1; }
	cd $FREENAS_TMPDIR
	tar -cf - -C $FREENAS_ROOTFS ./ | tar -xvpf -

	cd $FREENAS_WORKINGDIR
	# Umount memory disk
	umount $FREENAS_TMPDIR || { echo "Can't umount $FREENAS_TMPDIR"; exit 1; }
	
    # Detach memory disk
	mdconfig -d -u ${md} || { echo "Cant' destroy ${md}"; exit 1; }

	gzip -9fnv $FREENAS_WORKINGDIR/mfsroot || { echo "Can't gzip mfsroot"; exit 1; }

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
	[ -f ${FREENAS_WORKINGDIR}/image.bin ] && rm -f image.bin
	[ -f ${FREENAS_WORKINGDIR}/image.bin.gz ] && rm -f image.bin.gz

	# Set platform information.
	PLATFORM="${FREENAS_ARCH}-embedded"
	echo $PLATFORM > ${FREENAS_ROOTFS}/etc/platform

	# Set build time.
	date > ${FREENAS_ROOTFS}/etc/prd.version.buildtime

	# Set revision.
	echo ${FREENAS_REVISION} > ${FREENAS_ROOTFS}/etc/prd.revision

	IMGFILENAME="${FREENAS_PRODUCTNAME}-${PLATFORM}-${FREENAS_VERSION}.${FREENAS_REVISION}.img"

	echo "===> Generating tempory $FREENAS_TMPDIR folder"
    if [ ! -d $FREENAS_TMPDIR ]; then
	    mkdir $FREENAS_TMPDIR
    fi
	create_mfsroot;

	echo "===> Creating an empty IMG file"
	dd if=/dev/zero of=${FREENAS_WORKINGDIR}/image.bin bs=${FREENAS_IMG_SECTS}b count=`expr ${FREENAS_IMG_SIZE_SEC} / ${FREENAS_IMG_SECTS}`
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
  a:    ${FREENAS_IMG_SIZE_SEC}        0    4.2BSD        0     0
  c:    *            *    unused        0     0         # "raw" part, don't edit
" > ${FREENAS_WORKINGDIR}/bsdlabel.$$
	bsdlabel -m ${FREENAS_ARCH} -R -B -b ${FREENAS_BOOTDIR}/boot ${md} ${FREENAS_WORKINGDIR}/bsdlabel.$$
	bsdlabel ${md}
	echo "===> Formatting this memory disk using UFS"
	newfs -U -o space -m 0 /dev/${md}a
	echo "===> Mount this virtual disk on $FREENAS_TMPDIR"
	mount /dev/${md}a $FREENAS_TMPDIR
	echo "===> Copying previously generated MFSROOT file to memory disk"
	cp $FREENAS_WORKINGDIR/mfsroot.gz $FREENAS_TMPDIR

	echo "===> Copying bootloader file(s) to memory disk"
	mkdir -p $FREENAS_TMPDIR/boot
	mkdir -p $FREENAS_TMPDIR/boot/kernel $FREENAS_TMPDIR/boot/defaults $FREENAS_TMPDIR/boot/zfs
	mkdir -p $FREENAS_TMPDIR/conf
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
		install -v -o root -g wheel -m 555 ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $FREENAS_TMPDIR/boot/kernel
	fi
	if [ "amd64" != ${FREENAS_ARCH} ]; then
		cd ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 apm/apm.ko acpi/acpi/acpi.ko $FREENAS_TMPDIR/boot/kernel
	fi

	echo "===> Unmount memory disk"
	umount $FREENAS_TMPDIR
	echo "===> Detach memory disk"
	mdconfig -d -u ${md}
	echo "===> Compress the IMG file"
	gzip -9n $FREENAS_WORKINGDIR/image.bin
	cp $FREENAS_WORKINGDIR/image.bin.gz $FREENAS_ROOTDIR/$IMGFILENAME

	# Cleanup.
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

	if [ ! $LIGHT_ISO ]; then
		LABEL="${FREENAS_PRODUCTNAME}-${FREENAS_ARCH}-LiveCD-${FREENAS_VERSION}.${FREENAS_REVISION}"
		VOLUMEID="${FREENAS_PRODUCTNAME}-${FREENAS_ARCH}-LiveCD-${FREENAS_VERSION}"
		echo "ISO: Generating the $FREENAS_PRODUCTNAME Image file:"
		create_image;
	else
		LABEL="${FREENAS_PRODUCTNAME}-${FREENAS_ARCH}-LiveCD-light-${FREENAS_VERSION}.${FREENAS_REVISION}"
		VOLUMEID="${FREENAS_PRODUCTNAME}-${FREENAS_ARCH}-LiveCD-light-${FREENAS_VERSION}"
	fi

	# Set platform information.
	PLATFORM="${FREENAS_ARCH}-liveCD"
	echo $PLATFORM > ${FREENAS_ROOTFS}/etc/platform

	# Set revision.
	echo ${FREENAS_REVISION} > ${FREENAS_ROOTFS}/etc/prd.revision

	echo "ISO: Generating temporary folder '$FREENAS_TMPDIR'"
	mkdir $FREENAS_TMPDIR
	create_mfsroot;

	echo "ISO: Copying previously generated MFSROOT file to $FREENAS_TMPDIR"
	cp $FREENAS_WORKINGDIR/mfsroot.gz $FREENAS_TMPDIR

	echo "ISO: Copying bootloader file(s) to $FREENAS_TMPDIR"
	mkdir -p $FREENAS_TMPDIR/boot
	mkdir -p $FREENAS_TMPDIR/boot/kernel $FREENAS_TMPDIR/boot/defaults $FREENAS_TMPDIR/boot/zfs
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
		install -v -o root -g wheel -m 555 ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $FREENAS_TMPDIR/boot/kernel
	fi
	if [ "amd64" != ${FREENAS_ARCH} ]; then
		cd ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules && install -v -o root -g wheel -m 555 apm/apm.ko acpi/acpi/acpi.ko $FREENAS_TMPDIR/boot/kernel
	fi

	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Copying IMG file to $FREENAS_TMPDIR"
		cp ${FREENAS_WORKINGDIR}/image.bin.gz ${FREENAS_TMPDIR}/${FREENAS_PRODUCTNAME}-${FREENAS_ARCH}-embedded.gz
	fi

	echo "ISO: Generating the ISO file"
	mkisofs -b "boot/cdboot" -no-emul-boot -r -J -A "${FREENAS_PRODUCTNAME} CD-ROM image" -publisher "${FREENAS_URL}" -V "${VOLUMEID}" -o "${FREENAS_ROOTDIR}/${LABEL}.iso" ${FREENAS_TMPDIR}
	[ 0 != $? ] && return 1 # successful?

	echo "Generating MD5 and SHA256 sums..."
	FREENAS_CHECKSUMFILENAME="${FREENAS_PRODUCTNAME}-${FREENAS_ARCH}-${FREENAS_VERSION}.${FREENAS_REVISION}.checksum"
	cd ${FREENAS_ROOTDIR} && md5 *.img *.iso > ${FREENAS_ROOTDIR}/${FREENAS_CHECKSUMFILENAME}
	cd ${FREENAS_ROOTDIR} && sha256 *.img *.iso >> ${FREENAS_ROOTDIR}/${FREENAS_CHECKSUMFILENAME}

	# Cleanup.
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	[ -f $FREENAS_WORKINGDIR/mfsroot.gz ] && rm -f $FREENAS_WORKINGDIR/mfsroot.gz

	return 0
}

create_iso_light() {
	LIGHT_ISO=1
	create_iso;
	unset LIGHT_ISO
	return 0
}

create_full() {
	[ -d $FREENAS_SVNDIR ] && use_svn ;

	echo "FULL: Generating $FREENAS_PRODUCTNAME tgz update file"

	# Set platform information.
	PLATFORM="${FREENAS_ARCH}-full"
	echo $PLATFORM > ${FREENAS_ROOTFS}/etc/platform

	# Set revision.
	echo ${FREENAS_REVISION} > ${FREENAS_ROOTFS}/etc/prd.revision

	FULLFILENAME="${FREENAS_PRODUCTNAME}-${PLATFORM}-${FREENAS_VERSION}.${FREENAS_REVISION}.tgz"

	echo "FULL: Generating tempory $FREENAS_TMPDIR folder"
	#Clean TMP dir:
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	mkdir $FREENAS_TMPDIR

	#Copying all FreeNAS rootfilesystem (including symlink) on this folder
	cd $FREENAS_TMPDIR
	tar -cf - -C $FREENAS_ROOTFS ./ | tar -xvpf -
	#tar -cf - -C $FREENAS_ROOTFS ./ | tar -xvpf - -C $FREENAS_TMPDIR

	echo "Copying bootloader file(s) to root filesystem"
	mkdir -p $FREENAS_TMPDIR/boot/kernel $FREENAS_TMPDIR/boot/defaults $FREENAS_TMPDIR/boot/zfs
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
	if [ "amd64" != ${FREENAS_ARCH} ]; then
		cd ${FREENAS_OBJDIRPREFIX}/usr/src/sys/${FREENAS_KERNCONF}/modules/usr/src/sys/modules && cp apm/apm.ko acpi/acpi/acpi.ko $FREENAS_TMPDIR/boot/kernel
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
	# Update sources from repository.
	cd $FREENAS_ROOTDIR
	svn co $FREENAS_SVNURL svn

	# Update revision number.
	FREENAS_REVISION=$(svn info ${FREENAS_SVNDIR} | grep Revision | awk '{print $2}')

	return 0
}

use_svn() {
	echo "===> Replacing old code with SVN code"

	cd ${FREENAS_SVNDIR}/build && cp -pv CHANGES ${FREENAS_ROOTFS}/usr/local/www
	cd ${FREENAS_SVNDIR}/root && find . \! -iregex ".*/\.svn.*" -print | cpio -pdumv ${FREENAS_ROOTFS}/root
	cd ${FREENAS_SVNDIR}/etc && find . \! -iregex ".*/\.svn.*" -print | cpio -pdumv ${FREENAS_ROOTFS}/etc
	cd ${FREENAS_SVNDIR}/www && find . \! -iregex ".*/\.svn.*" -print | cpio -pdumv ${FREENAS_ROOTFS}/usr/local/www
	cd ${FREENAS_SVNDIR}/conf && find . \! -iregex ".*/\.svn.*" -print | cpio -pdumv ${FREENAS_ROOTFS}/conf.default

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
					if [ 0 != $OPT_SERIALCONSOLE ]; then
						opt="$opt -s"
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
		state=`cat $s/pkg-state`
		case ${state} in
			[hH][iI][dD][eE])
				;;
			*)
				desc=`cat $s/pkg-descr`;
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

	case ${choice} in
		build)
			# Set ports options
			echo;
			echo "--------------------------------------------------------------";
			echo ">>> Set ports options.";
			echo "--------------------------------------------------------------";
			cd ${FREENAS_SVNDIR}/build/ports/options && make
			# Clean ports.
			echo;
			echo "--------------------------------------------------------------";
			echo ">>> Cleaning ports.";
			echo "--------------------------------------------------------------";
			for port in $(cat ${ports} | tr -d '"'); do
				cd ${FREENAS_SVNDIR}/build/ports/${port};
				make clean;
			done;
			# Build ports.
			for port in $(cat $ports | tr -d '"'); do
				echo;
				echo "--------------------------------------------------------------";
				echo ">>> Building port: ${port}";
				echo "--------------------------------------------------------------";
				cd ${FREENAS_SVNDIR}/build/ports/${port};
				make build;
				[ 0 != $? ] && return 1; # successful?
			done;
			;;
		install)
			for port in $(cat ${ports} | tr -d '"'); do
				echo;
				echo "--------------------------------------------------------------";
				echo ">>> Installing port: ${port}";
				echo "--------------------------------------------------------------";
				cd ${FREENAS_SVNDIR}/build/ports/${port};
				# Delete cookie first, otherwise Makefile will skip this step.
				rm -f ./work/.install_done.*;
				env NO_PKG_REGISTER=1 make install;
				[ 0 != $? ] && return 1; # successful?
			done;
			;;
	esac
	rm ${ports}

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
