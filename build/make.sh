#!/usr/bin/env bash
# This is a script designed to automate the assembly of
# a FreeNAS box.
# Created: 2/12/2006 by Scott Zahn
# Modified: 11/2006 by Volker Theile (votdev@gmx.de)

# Debug script
#set -x

# Global variables
export FREENAS_ROOTDIR="/usr/local/freenas"
export FREENAS_WORKINGDIR="$FREENAS_ROOTDIR/work"
export FREENAS_ROOTFS="$FREENAS_ROOTDIR/rootfs"
export FREENAS_SVNDIR="$FREENAS_ROOTDIR/svn"
export FREENAS_PRODUCTNAME=`cat $FREENAS_SVNDIR/etc/prd.name`
export FREENAS_VERSION=`cat $FREENAS_SVNDIR/etc/prd.version`

# Local variables
FREENAS_URL=`cat $FREENAS_SVNDIR/etc/prd.url`
FREENAS_BOOTDIR="$FREENAS_ROOTDIR/bootloader"
FREENAS_TMPDIR="/tmp/freenastmp"
FREENAS_ARCH=$(uname -p)

# Path where to find Makefile includes
FREENAS_MKINCLUDESDIR="$FREENAS_SVNDIR/build/mk"

# Size in MB of the MFS Root filesystem that will include all FreeBSD binary and FreeNAS WEbGUI/Scripts
# Keep this file very small! This file is unzipped to a RAM disk at FreeNAS startup
FREENAS_MFSROOT_SIZE="45"
# Size in MB f the IMG file, that include zipped MFS Root filesystem image plus bootloader and kernel.
FREENAS_IMG_SIZE="23"
# 'newfs' parameters.
FREENAS_NEWFS="-b 4096 -f 512 -i 8192 -U -o space -m 0"
# FREENAS_NEWFS="-b 8192 -f 1024 -o space -m 0"

# Options:
# Support bootmenu
OPT_BOOTMENU=1
# Support bootsplash
OPT_BOOTSPLASH=1

# Dialog command
DIALOG="dialog"

# URL's:
URL_FREENASROOTFS="http://www.freenas.org/downloads/freenas-rootfs.tgz"
URL_FREENASBOOT="http://www.freenas.org/downloads/freenas-boot.tgz"

# Copying required files
copy_files() {
	# Make a pseudo 'chroot' to FreeNAS root.
  cd $FREENAS_ROOTFS

	echo
	echo "Adding required files:"

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

		# Copy files
		cp -pv /$file $(echo $file | rev | cut -d "/" -f 2- | rev)

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
	date > $FREENAS_ROOTFS/etc/prd.version.buildtime

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
--checklist \"Select the drivers you want to add.\" 21 75 14 \\" > $tempfile

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
		echo "======================================================================"
		cd $FREENAS_SVNDIR/build/drivers/$driver
		make -I ${FREENAS_MKINCLUDESDIR} install
		[ 0 != $? ] && return 1 # successful?
	done
	rm $drivers
}

# Building the kernel
build_kernel() {
	tempfile=$FREENAS_WORKINGDIR/tmp$$

	# Choose what to do.
	$DIALOG --title "$FREENAS_PRODUCTNAME - Build kernel" --checklist "Please select whether you want to build or install the kernel." 10 75 3 \
		"prebuild" "Install additional drivers" ON \
		"build" "Build kernel" ON \
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
				if [ -f FREENAS-${FREENAS_ARCH} ]; then
					rm -f FREENAS-${FREENAS_ARCH};
				fi;
				cp $FREENAS_SVNDIR/build/kernel-config/FREENAS-${FREENAS_ARCH} .;
				# Compiling and compressing the kernel.
				cd /usr/src;
				make buildkernel KERNCONF=FREENAS-${FREENAS_ARCH};
				gzip -v -f -9 /usr/obj/usr/src/sys/FREENAS-${FREENAS_ARCH}/kernel;;
  		install)
				# Installing the modules.
				cd /usr/obj/usr/src/sys/FREENAS-${FREENAS_ARCH}/modules/usr/src/sys/modules;
				cp -v -p ./geom/geom_vinum/geom_vinum.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_stripe/geom_stripe.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_concat/geom_concat.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_mirror/geom_mirror.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_nop/geom_nop.ko $FREENAS_ROOTFS/boot/kernel;

				cp -v -p ./geom/geom_gpt/geom_gpt.ko $FREENAS_ROOTFS/boot/kernel;
				cp -v -p ./geom/geom_eli/geom_eli.ko $FREENAS_ROOTFS/boot/kernel;
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
		cp -vp $i ${FREENAS_ROOTFS}$(echo $i | rev | cut -d '/' -f 2- | rev)
	done

	# Cleanup.
	rm -f /tmp/lib.list

  return 0
}

# Build packages/plugins.
build_packages() {
	# Make sure packages directory exists.
	[ ! -d "$FREENAS_ROOTDIR/packages" ] && mkdir -p $FREENAS_ROOTDIR/packages
	
	tempfile=$FREENAS_WORKINGDIR/tmp$$
	packages=$FREENAS_WORKINGDIR/packages$$

	# Create list of available packages.
	echo "#! /bin/sh
$DIALOG --title \"$FREENAS_PRODUCTNAME - Packages/Plugins\" \\
--checklist \"Select the packages/plugins you want to build.\" 21 75 14 \\" > $tempfile

	for s in $FREENAS_SVNDIR/build/packages/*; do
		[ ! -d "$s" ] && continue
		package=`basename $s`
		desc=`cat $s/pkg-descr`
		state=`cat $s/pkg-state`
		echo "\"$package\" \"$desc\" $state \\" >> $tempfile
	done

	# Display list of available packages.
	sh $tempfile 2> $packages
	if [ 0 != $? ]; then # successful?
		rm $tempfile
		return 1
	fi
	rm $tempfile

	for package in $(cat $packages | tr -d '"'); do
		echo "======================================================================"
		cd $FREENAS_SVNDIR/build/packages/$package
		make -I ${FREENAS_MKINCLUDESDIR} clean package
		[ 0 != $? ] && return 1 # successful?
	done
	rm $packages

	return 0;
}

# Creating msfroot
create_mfsroot() {
	echo "Generating the MFSROOT filesystem"
	cd $FREENAS_WORKINGDIR

	[ -f $FREENAS_WORKINGDIR/mfsroot.gz ] && rm -f $FREENAS_WORKINGDIR/mfsroot.gz
	[ -d $FREENAS_SVNDIR ] && use_svn ;

	# Setting Version type and date
	date > $FREENAS_ROOTFS/etc/prd.version.buildtime
	
	# Make mfsroot to have the size of the FREENAS_MFSROOT_SIZE variable
	dd if=/dev/zero of=$FREENAS_WORKINGDIR/mfsroot bs=1M count=${FREENAS_MFSROOT_SIZE}
	# Configure this file as a memory disk
	mdconfig -a -t vnode -f $FREENAS_WORKINGDIR/mfsroot -u 0
	# Create Label on this disk
	bsdlabel -w md0 auto
	# format it as UFS
	newfs ${FREENAS_NEWFS} /dev/md0c
	# umount the /mnt directory if already used
	umount $FREENAS_TMPDIR
	mount /dev/md0c $FREENAS_TMPDIR
	cd $FREENAS_TMPDIR
	tar -cf - -C $FREENAS_ROOTFS ./ | tar -xvpf -
	cd $FREENAS_WORKINGDIR
	umount $FREENAS_TMPDIR
	mdconfig -d -u 0
	gzip -9 $FREENAS_WORKINGDIR/mfsroot
	return 0
}

create_image() {
	echo "IMG: Generating $FREENAS_PRODUCTNAME IMG File (to be rawrite on CF/USB/HD)"
	[ -f image.bin ] && rm -f image.bin
	PLATFORM="$FREENAS_ARCH-embedded"
	echo $PLATFORM > $FREENAS_ROOTFS/etc/platform
	IMGFILENAME="$FREENAS_PRODUCTNAME-$PLATFORM-$FREENAS_VERSION.img"
	
	echo "IMG: Generating tempory $FREENAS_TMPDIR folder"
	mkdir $FREENAS_TMPDIR
	create_mfsroot;
	
	echo "IMG: Creating an empty destination IMG file"
	dd if=/dev/zero of=$FREENAS_WORKINGDIR/image.bin bs=1M count=${FREENAS_IMG_SIZE}
	echo "IMG: using this file as a memory disk"
	mdconfig -a -t vnode -f $FREENAS_WORKINGDIR/image.bin -u 0
	echo "IMG: Creating partition on this memory disk"
	fdisk -BI -b $FREENAS_BOOTDIR/mbr /dev/md0
	echo "IMG: Configuring FreeBSD label on this memory disk"
	bsdlabel -B -w -b $FREENAS_BOOTDIR/boot /dev/md0 auto
	bsdlabel md0 >/tmp/label.$$
	# Replace the a: unuset by a a:4.2BSD
	#Replacing c: with a: is a trick, when this file is apply, this line is ignored
	bsdlabel md0 |
		 egrep unused |
		 sed "s/c:/a:/" |
		 sed "s/unused/4.2BSD/" >>/tmp/label.$$
	bsdlabel -R -B md0 /tmp/label.$$
	rm -f /tmp/label.$$
	echo "IMG: Formatting this memory disk using UFS"
	newfs ${FREENAS_NEWFS} /dev/md0a
	echo "IMG: Mount this virtual disk on $FREENAS_TMPDIR"
	mount /dev/md0a $FREENAS_TMPDIR
	echo "IMG: Copying previously generated MFSROOT file to memory disk"
	cp $FREENAS_WORKINGDIR/mfsroot.gz $FREENAS_TMPDIR

	echo "Copying bootloader file(s) to memory disk"
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
		cp /usr/obj/usr/src/sys/FREENAS-${FREENAS_ARCH}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $FREENAS_TMPDIR/boot/kernel
	fi

	#Special for enabling serial port if no keyboard
	#cp $FREENAS_BOOTDIR/boot.config $FREENAS_TMPDIR/
	
	echo "IMG: unmount memory disk"
	umount $FREENAS_TMPDIR
	echo "IMG: Deconfigure memory disk"
	mdconfig -d -u 0
	echo "IMG: Compress the IMG file"
	gzip -9 $FREENAS_WORKINGDIR/image.bin
	mv $FREENAS_WORKINGDIR/image.bin.gz $FREENAS_ROOTDIR/$IMGFILENAME

	# Cleanup.
	echo "Cleaning tempo file"
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	[ -f $FREENAS_WORKINGDIR/mfsroot.gz ] && rm -f $FREENAS_WORKINGDIR/mfsroot.gz
	[ -f $FREENAS_WORKINGDIR/image.bin ] && rm -f $FREENAS_WORKINGDIR/image.bin

	return 0
}

create_iso () {
	echo "ISO: Remove old directory and file if exist"
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	[ -f $FREENAS_WORKINGDIR/mfsroot.gz ] && rm -f $FREENAS_WORKINGDIR/mfsroot.gz
	
	ISOFILENAME="$FREENAS_PRODUCTNAME-$FREENAS_ARCH-liveCD-$FREENAS_VERSION.iso"
	
	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Generating the $FREENAS_PRODUCTNAME Image file:"
		create_image;
	fi
	
	#Setting the variable for ISO image:
	PLATFORM="$FREENAS_ARCH-liveCD"
	echo "$PLATFORM" > $FREENAS_ROOTFS/etc/platform
	date > $FREENAS_ROOTFS/etc/prd.version.buildtime
	
	echo "ISO: Generating tempory $FREENAS_TMPDIR folder"
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
		cp /usr/obj/usr/src/sys/FREENAS-${FREENAS_ARCH}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $FREENAS_TMPDIR/boot/kernel
	fi

	#Special test for enabling serial port if no keyboard
	#Removed because meet some problem with some hardware (no keyboard detected)
	#cp $FREENAS_BOOTDIR/boot.config $FREENAS_TMPDIR/
	
	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Copying IMG file to $FREENAS_TMPDIR"
		cp $FREENAS_ROOTDIR/$FREENAS_PRODUCTNAME-$FREENAS_ARCH-embedded-$FREENAS_VERSION.img $FREENAS_TMPDIR/$FREENAS_PRODUCTNAME-$FREENAS_ARCH-embedded.gz
	fi

	echo "ISO: Generating the ISO file"
	mkisofs -b "boot/cdboot" -no-emul-boot -c "boot/boot.catalog" -d -r -A "${FREENAS_PRODUCTNAME} CD-ROM image" -publisher "${FREENAS_URL}" -p "Olivier Cochard-Labbe" -V "${FREENAS_PRODUCTNAME}_cd" -o "${FREENAS_ROOTDIR}/${ISOFILENAME}" ${FREENAS_TMPDIR}
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
	#### CODE NOT FINISH ######
	echo "FULL: Generating $FREENAS_PRODUCTNAME tgz update file"	
	
	PLATFORM="$FREENAS_ARCH-full"
	echo $PLATFORM > $FREENAS_ROOTFS/etc/platform
	FULLFILENAME="$FREENAS_PRODUCTNAME-$PLATFORM-$FREENAS_VERSION.tgz"
	
	echo "FULL: Generating tempory $FREENAS_TMPDIR folder"
	#Clean TMP dir:
	[ -d $FREENAS_TMPDIR ] && rm -rf $FREENAS_TMPDIR
	mkdir $FREENAS_TMPDIR
	
	# Setting Version type and date
	date > $FREENAS_ROOTFS/etc/prd.version.buildtime
	
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
	#cp $FREENAS_BOOTDIR/defaults/loader.conf $FREENAS_TMPDIR/boot/defaults/
	cp $FREENAS_BOOTDIR/device.hints $FREENAS_TMPDIR/boot
	if [ 0 != $OPT_BOOTMENU ]; then
		cp $FREENAS_SVNDIR/boot/menu.4th $FREENAS_TMPDIR/boot
		cp $FREENAS_BOOTDIR/screen.4th $FREENAS_TMPDIR/boot
		cp $FREENAS_BOOTDIR/frames.4th $FREENAS_TMPDIR/boot
	fi
	if [ 0 != $OPT_BOOTSPLASH ]; then
		cp $FREENAS_SVNDIR/boot/splash.bmp $FREENAS_TMPDIR/boot
		cp /usr/obj/usr/src/sys/FREENAS-${FREENAS_ARCH}/modules/usr/src/sys/modules/splash/bmp/splash_bmp.ko $FREENAS_TMPDIR/boot/kernel
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

download_rootfs() {
  # Ensure we are in $FREENAS_WORKINGDIR
	[ ! -d "$FREENAS_WORKINGDIR" ] && mkdir $FREENAS_WORKINGDIR
	cd $FREENAS_WORKINGDIR

	update=y
	if [ -e freenas-rootfs.tgz -a -e freenas-boot.tgz ]; then
    echo -n "Update existing archives [y/n]? "
    read update
	fi

  if [ $update = 'y' ]; then
    echo "Deleting old archives"
    [ -f "freenas-rootfs.tgz" ] && rm -f freenas-rootfs.tgz
    [ -f "freenas-boot.tgz" ] && rm -f freenas-boot.tgz

    echo "Downloading latest archives..."
    fetch $URL_FREENASROOTFS
    if [ 1 == $? ]; then
      echo "==> Failed to fetch freenas-rootfs.tgz."
      return 1
    fi
    fetch $URL_FREENASBOOT
    if [ 1 == $? ]; then
      echo "==> Failed to fetch freenas-boot.tgz."
      return 1
    fi
	fi

  # Remove old data.
  delete=n
	if [ -e "./bootloader" -o -e "./rootfs" ]; then
    echo -n "Delete existing directory structure (./bootloader/ and ./rootfs/) [y/n]? "
    read delete
	fi

  if [ $delete = 'y' ]; then
    if [ -e "./bootloader" ]; then
      rm -r ./bootloader
    fi
    if [ -e "./rootfs" ]; then
      rm -r ./rootfs
    fi
  fi

  # Extracting bootloader and rootfs data.
	echo "De-taring archives..."
	tar -xzf freenas-rootfs.tgz -C $FREENAS_WORKINGDIR/
	tar -xzf freenas-boot.tgz -C $FREENAS_WORKINGDIR/

	return 0
}

update_sources() {
	cd $FREENAS_ROOTDIR
	svn co https://freenas.svn.sourceforge.net/svnroot/freenas/trunk svn

	return 0
}

use_svn() {
	echo "Replacing old code with SVN code"

	cp -v -p $FREENAS_SVNDIR/etc/*.* $FREENAS_ROOTFS/etc
	cp -v -p $FREENAS_SVNDIR/etc/* $FREENAS_ROOTFS/etc
	cp -v -p $FREENAS_SVNDIR/etc/inc/*.* $FREENAS_ROOTFS/etc/inc
	cp -v -p $FREENAS_SVNDIR/etc/defaults/*.* $FREENAS_ROOTFS/etc/defaults
	cp -v -p $FREENAS_SVNDIR/www/*.* $FREENAS_ROOTFS/usr/local/www
	cp -v -p $FREENAS_SVNDIR/www/syntaxhighlighter/*.* $FREENAS_ROOTFS/usr/local/www/syntaxhighlighter
	cp -v -p $FREENAS_SVNDIR/conf/*.* $FREENAS_ROOTFS/conf.default

	return 0
}

fromscratch() {
  while true; do
echo -n '
Rebulding FreeNAS from Scratch
Menu:
1 - Create FreeNAS filesystem structure 
2 - Copy required files to FreeNAS filesystem
3 - Build kernel
4 - Build ports
5 - Build bootloader
6 - Add necessary libraries
7 - Modify file permissions
8 - Build packages
* - Quit
> '
		read choice
		case $choice in
			1)	create_rootfs;;
			2)	copy_files;;
			3)	build_kernel;;
			4)	build_ports;;
			5)	opt="-f";
					if [ 0 != $OPT_BOOTMENU ]; then
						opt="$opt -m"
					fi;
					if [ 0 != $OPT_BOOTSPLASH ]; then
						opt="$opt -b"
					fi;
					$FREENAS_SVNDIR/build/freenas-create-bootdir.sh $opt $FREENAS_BOOTDIR;;
			6)	add_libs;;
			7)	$FREENAS_SVNDIR/build/freenas-modify-permissions.sh $FREENAS_ROOTFS;;
			8)	build_packages;;
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
	$DIALOG --title "$FREENAS_PRODUCTNAME - Ports" --menu "Please select whether you want to build or install ports." 10 45 2 \
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
		echo "\"$port\" \"$desc\" $state \\" >> $tempfile
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
		echo "======================================================================"
		cd $FREENAS_SVNDIR/build/ports/$port
		if [ "$choice" == "build" ]; then
			# Build port.
			make -I ${FREENAS_MKINCLUDESDIR} clean build
		elif [ "$choice" == "install" ]; then
			# Delete cookie first, otherwise Makefile will skip this step.
			rm -f ./work/.install_done.*
			# Install port.
			make -I ${FREENAS_MKINCLUDESDIR} install
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

	echo -n '
Welcome to the FreeNAS build environment.
Menu:
1  - Download and decompress FreeNAS root filesystem 
2  - Update the source to latest (need SVN)
10 - Create FreeNAS "embedded" (IMG) file (rawrite to CF/USB/DD)
11 - Create FreeNAS "liveCD" (ISO) file (need cdrtools)
12 - Create FreeNAS "LiveCD" (ISO) file without 'embedded' file (need cdrtools)
13 - Create FreeNAS "full" (TGZ) update file
20 - Build FreeNAS from scratch advanced menu
*  - Quit
> '
	read choice
	case $choice in
		1)  download_rootfs;;
		2)  update_sources;;
		10) create_image;;
		11) create_iso;;
		12) create_iso_light;;
		13) create_full;;
		20) fromscratch;;
		*)  exit 0;;
	esac

	[ 0 == $? ] && echo "=> Successful" || echo "=> Failed"
	sleep 1

	return 0
}

while true; do
	main
done
exit 0
