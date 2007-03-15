#!/usr/bin/env bash
# This is a script designed to automate the assembly of
# a FreeNAS box.
# Created: 2/12/2006 by Scott Zahn
# Modified: 11/2006 by Volker Theile (votdev@gmx.de)

# Global variables
export WORKINGDIR="/usr/local/freenas"
export FREENASDISTFILES="$WORKINGDIR/distfiles"
export FREENAS="$WORKINGDIR/rootfs"
export SVNDIR="$WORKINGDIR/svn"
export PRODUCTNAME=`cat $SVNDIR/etc/prd.name`

# Local variables
BOOTDIR="/usr/local/freenas/bootloader"
TMPDIR="/tmp/freenastmp"
VERSION=`cat $SVNDIR/etc/prd.version`

# Path where to find Makefile includes
MKINCLUDESDIR="$SVNDIR/misc/mk"

# Dialog command
DIALOG="dialog"

#Size in MB of the MFS Root filesystem that will include all FreeBSD binary and FreeNAS WEbGUI/Scripts
#Keep this file very small! This file is unzipped to a RAM disk at FreeNAS startup
MFSROOT_SIZE="44"
#Size in MB f the IMG file, that include zipped MFS Root filesystem image plus bootlaoder and kernel.
IMG_SIZE="21"

# URL's:
URL_FREENASROOTFS="http://www.freenas.org/downloads/freenas-rootfs.tgz"
URL_FREENASBOOT="http://www.freenas.org/downloads/freenas-boot.tgz"

# Return filename of URL
urlbasename() {
  echo $1 | awk '{n=split($0,v,"/");print v[n]}'
}

# Copying required files
copy_files() {
  cd $WORKINGDIR

	echo
	echo "Adding required files:"

	[ -f freenas.files ] && rm -f freenas.files
	cp $SVNDIR/misc/freenas.files $WORKINGDIR

	# Add custom binaries
	if [ -f freenas.custfiles ]; then
		cat freenas.custfiles >> freenas.files
	fi

	for i in $(cat freenas.files | grep -v "^#"); do
		file=$(echo "$i" | cut -d ":" -f 1)
		# Deal with directories
		dir=$(echo "$i" | cut -d "*" -f 1)	
		if [ -d /$dir ]; then
		  mkdir -pv $FREENAS/$dir
		fi
		
		# Copy files
		cp -v -p /$file $FREENAS/$(echo $file | rev | cut -d "/" -f 2- | rev)
		
		# Deal with protected files
		if [ "$file" == "usr/bin/su" ] || [ "$file" == "libexec/ld-elf.so.1" ] || [ "$file" == "usr/bin/passwd" ] || [ "$file" == "sbin/init" ]; then
			if [ -f $FREENAS/$file ]; then
				chflags -RH noschg $FREENAS/$file
			fi
		fi
		
		# Deal with links
		if [ $(echo "$i" | grep -c ":") -gt 0 ]; then
			for j in $(echo $i | cut -d ":" -f 2- | sed "s/:/ /g"); do
				ln $FREENAS/$file $FREENAS/$j
			done
		fi
	done

	rm -f $WORKINGDIR/freenas.files

	# Setting right permission to su binary
	chmod 4755 $FREENAS/usr/bin/su

	return 0
}

# Create rootfs
create_rootfs() {
	$SVNDIR/misc/freenas-create-dirs.sh -f $FREENAS

  # Configuring platform variable
	echo $VERSION > $FREENAS/etc/prd.version
	date > $FREENAS/etc/prd.version.buildtime

  # Config file: config.xml
  cd $FREENAS/conf.default/
  cp -v $SVNDIR/conf/config.xml .

  # Compress zoneinfo data, exclude some useless files.
  mkdir $TMPDIR
  echo "Factory" > $TMPDIR/zoneinfo.exlude
	echo "posixrules" >> $TMPDIR/zoneinfo.exlude
	echo "zone.tab" >> $TMPDIR/zoneinfo.exlude
	tar -c -v -f - -X $TMPDIR/zoneinfo.exlude -C /usr/share/zoneinfo/ . | gzip -cv > $FREENAS/usr/share/zoneinfo.tgz
	rm $TMPDIR/zoneinfo.exlude

  return 0
}

# Actions before building kernel (e.g. install spezial drivers).
pre_build_kernel() {
	tempfile=$WORKINGDIR/tmp$$
	drivers=$WORKINGDIR/drivers$$

	# Create list of available packages.
	echo "#! /bin/sh
$DIALOG --title \"$PRODUCTNAME - Drivers\" \\
--checklist \"Select the drivers you want to add.\" 21 75 14 \\" > $tempfile

	for s in $SVNDIR/misc/drivers/*; do
		[ ! -d "$s" ] && continue
		package=`basename $s`
		desc=`cat $s/pkg-descr`
		state=`cat $s/pkg-state`
		echo "\"$package\" \"$desc\" $state \\" >> $tempfile
	done

	# Display list of available drivers.
	sh $tempfile 2> $drivers
	[ 0 != $? ] && return 1 # successful?
	rm $tempfile

	for driver in $(cat $drivers | tr -d '"'); do
		echo "======================================================================"
		cd $SVNDIR/misc/drivers/$driver
		make -I $MKINCLUDESDIR install
		[ 0 != $? ] && return 1 # successful?
	done
	rm $drivers
}

# Building the kernel
build_kernel() {
	# Adding specials drivers.
	pre_build_kernel;
	[ 0 != $? ] && return 1 # successful?

	# Copy kernel configuration.
	cd /sys/i386/conf
	if [ -f FREENAS ]; then
		rm -f FREENAS
	fi
	cp $SVNDIR/misc/kernel-config/FREENAS .

	# Compiling and compressing the kernel.
	cd /usr/src
	make buildkernel KERNCONF=FREENAS
	gzip -v -f -9 /usr/obj/usr/src/sys/FREENAS/kernel

	# Installing the modules.
	cd /usr/obj/usr/src/sys/FREENAS/modules/usr/src/sys/modules
	cp -v -p ./geom/geom_vinum/geom_vinum.ko $FREENAS/boot/kernel
	cp -v -p ./geom/geom_stripe/geom_stripe.ko $FREENAS/boot/kernel
	cp -v -p ./geom/geom_concat/geom_concat.ko $FREENAS/boot/kernel
	cp -v -p ./geom/geom_mirror/geom_mirror.ko $FREENAS/boot/kernel
	cp -v -p ./geom/geom_gpt/geom_gpt.ko $FREENAS/boot/kernel
	cp -v -p ./geom/geom_eli/geom_eli.ko $FREENAS/boot/kernel
	cp -v -p ./ext2fs/ext2fs.ko $FREENAS/boot/kernel

	# Installing the mbr.
	cp -v -p /boot/mbr $FREENAS/boot
	cp -v -p /boot/boot $FREENAS/boot
	cp -v -p /boot/boot0 $FREENAS/boot

	return 0
}

# Adding the libraries
add_libs() {
	echo
	echo "Adding required libs:"

	# Identify required libs.
	[ -f /tmp/lib.list ] && rm -f /tmp/lib.list
	dirs=($FREENAS/bin $FREENAS/sbin $FREENAS/usr/bin $FREENAS/usr/sbin $FREENAS/usr/local/bin $FREENAS/usr/local/sbin $FREENAS/usr/lib $FREENAS/usr/local/lib)
	for i in ${dirs[@]}; do
		for file in $(ls $i); do
			ldd -f "%p\n" $i/$file 2> /dev/null >> /tmp/lib.list
		done
	done

	# Copy identified libs.
	for i in $(sort -u /tmp/lib.list); do
	  
		if [ "$i" == "/lib/libc.so.6" ] || [ "$i" == "/lib/libcrypt.so.3" ] || [ "$i" == "/lib/libpthread.so.2" ]; then
			if [ -f ${FREENAS}$i ]; then
				echo "Remove flag for special libs"
				chflags -RH noschg ${FREENAS}$i
			fi
		fi
		cp -vp $i ${FREENAS}$(echo $i | rev | cut -d '/' -f 2- | rev)
	done
	rm -f /tmp/lib.list

  return 0
}

# Creating msfroot
create_mfsroot() {
	echo "Generating the MFSROOT filesystem"
	cd $WORKINGDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	[ -d $WORKINGDIR/svn ] && use_svn ;

	# Setting Version type and date
	date > $FREENAS/etc/prd.version.buildtime
	
	# Make mfsroot to have the size of the MFSROOT_SIZE variable
	dd if=/dev/zero of=$WORKINGDIR/mfsroot bs=1M count=$MFSROOT_SIZE
	# Configure this file as a memory disk
	mdconfig -a -t vnode -f $WORKINGDIR/mfsroot -u 0
	# Create Label on this disk
	bsdlabel -w md0 auto
	# format it as UFS
	newfs -b 8192 -f 1024 -o space -m 0 /dev/md0c
	# umount the /mnt directory if allready used
	umount $TMPDIR
	mount /dev/md0c $TMPDIR
	cd $TMPDIR
	tar -cf - -C $FREENAS ./ | tar -xvpf -
	cd $WORKINGDIR
	umount $TMPDIR
	mdconfig -d -u 0
	gzip -9 $WORKINGDIR/mfsroot
	return 0
}

create_image() {
	echo "IMG: Generating FreeNAS IMG File (to be rawrite on CF/USB/HD)"
	[ -f image.bin ] && rm -f image.bin
	PLATFORM="generic-pc"
	echo $PLATFORM > $FREENAS/etc/platform
	IMGFILENAME="$PRODUCTNAME-$PLATFORM-$VERSION.img"
	
	echo "IMG: Generating tempory $TMPDIR folder"
	mkdir $TMPDIR
	create_mfsroot;
	
	echo "IMG: Creating an empty destination IMG file"
	dd if=/dev/zero of=$WORKINGDIR/image.bin bs=1M count=$IMG_SIZE
	echo "IMG: using this file as a memory disk"
	mdconfig -a -t vnode -f $WORKINGDIR/image.bin -u 0
	echo "IMG: Creating partition on this memory disk"
	fdisk -BI -b $BOOTDIR/mbr /dev/md0
	echo "IMG: Configuring FreeBSD label on this memory disk"
	bsdlabel -B -w -b $BOOTDIR/boot /dev/md0 auto
	bsdlabel md0 >/tmp/label.$$
	bsdlabel md0 |
		 egrep unused |
		 sed "s/c:/a:/" |
		 sed "s/unused/4.2BSD/" >>/tmp/label.$$
	bsdlabel -R -B md0 /tmp/label.$$
	rm -f /tmp/label.$$
	echo "IMG: Formatting this memory disk on UFS"
	newfs -b 8192 -f 1024 -o space -m 0 /dev/md0a
	echo "IMG: Mount this virtual disk on $TMPDIR"
	mount /dev/md0a $TMPDIR
	echo "IMG: Copying previously generated MFSROOT file on memory disk"
	cp $WORKINGDIR/mfsroot.gz $TMPDIR
	echo "Copying bootloader file on memory disk"
	mkdir $TMPDIR/boot
	mkdir $TMPDIR/boot/kernel $TMPDIR/boot/defaults
	mkdir $TMPDIR/conf
	cp $FREENAS/conf.default/config.xml $TMPDIR/conf
	cp $BOOTDIR/kernel/kernel.gz $TMPDIR/boot/kernel
	cp $BOOTDIR/boot $TMPDIR/boot
	cp $BOOTDIR/loader $TMPDIR/boot
	cp $BOOTDIR/loader.conf $TMPDIR/boot
	cp $BOOTDIR/loader.rc $TMPDIR/boot
	cp $BOOTDIR/loader.4th $TMPDIR/boot
	cp $BOOTDIR/support.4th $TMPDIR/boot
	cp $BOOTDIR/defaults/loader.conf $TMPDIR/boot/defaults/
	cp $BOOTDIR/device.hints $TMPDIR/boot
	
	#Special for enabling serial port if no keyboard
	#cp $BOOTDIR/boot.config $TMPDIR/
	
	echo "IMG: unmount memory disk"
	umount $TMPDIR
	echo "IMG: Deconfigure memory disk"
	mdconfig -d -u 0
	echo "IMG: Compress the IMG file"
	gzip -9 $WORKINGDIR/image.bin
	mv $WORKINGDIR/image.bin.gz $IMGFILENAME
	
	echo "Cleaning tempo file"
	[ -d $TMPDIR ] && rm -rf $TMPDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	[ -f $WORKINGDIR/image.bin ] && rm -f $WORKINGDIR/image.bin

	return 0
}

create_iso () {
	echo "ISO: Remove old directory and file if exist"
	[ -d $TMPDIR ] && rm -rf $TMPDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	
	ISOFILENAME="$PRODUCTNAME-$VERSION.iso"
	
	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Generating the FreeNAS Image file:"
		create_image;
	fi
	
	#Setting the variable for ISO image:
	PLATFORM="generic-pc-cdrom"
	echo "$PLATFORM" > $FREENAS/etc/platform
	date > $FREENAS/etc/prd.version.buildtime
	
	echo "ISO: Generating tempory $TMPDIR folder"
	mkdir $TMPDIR
	create_mfsroot;
	
	echo "ISO: Copying previously generated MFSROOT file on $TMPDIR folder"
	cp $WORKINGDIR/mfsroot.gz $TMPDIR
	
	echo "ISO: Copying bootloader file on $TMPDIR folder"
	mkdir $TMPDIR/boot
	mkdir $TMPDIR/boot/kernel $TMPDIR/boot/defaults
	cp $BOOTDIR/kernel/kernel.gz $TMPDIR/boot/kernel
	cp $BOOTDIR/cdboot $TMPDIR/boot
	cp $BOOTDIR/loader $TMPDIR/boot
	cp $BOOTDIR/loader.conf $TMPDIR/boot
	cp $BOOTDIR/loader.rc $TMPDIR/boot
	cp $BOOTDIR/loader.4th $TMPDIR/boot
	cp $BOOTDIR/support.4th $TMPDIR/boot
	cp $BOOTDIR/defaults/loader.conf $TMPDIR/boot/defaults/
	cp $BOOTDIR/device.hints $TMPDIR/boot
	
	#Special test for enabling serial port if no keyboard
	#Removed because meet some problem with some hardware (no keyboard detected)
	#cp $BOOTDIR/boot.config $TMPDIR/
	
	if [ ! $LIGHT_ISO ]; then
		echo "ISO: Copying IMG file on $TMPDIR folder"
		cp $WORKINGDIR/$PRODUCTNAME-generic-pc-$VERSION.img $TMPDIR/$PRODUCTNAME-generic-pc.gz
	fi

	echo "ISO: Generating the ISO file"
	cp -p $SVNDIR/misc/.mkisofsrc $HOME
	mkisofs -b "boot/cdboot" -no-emul-boot -c "boot/boot.catalog" -d -r -o "$ISOFILENAME" $TMPDIR
	[ 0 != $? ] && return 1 # successful?
	
	echo "ISO: Cleaning tempo file"
	[ -d $TMPDIR ] && rm -rf $TMPDIR
	[ -f $WORKINGDIR/mfsroot.gz ] && rm -f $WORKINGDIR/mfsroot.gz
	
	return 0
}

create_iso_light() {
	LIGHT_ISO=1
	create_iso;
	return 0
}

download_rootfs() {
  # Ensure we are in $WORKINGDIR
	[ ! -d "$WORKINGDIR" ] && mkdir $WORKINGDIR
	cd $WORKINGDIR

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
	tar -xzf freenas-rootfs.tgz -C $WORKINGDIR/
	tar -xzf freenas-boot.tgz -C $WORKINGDIR/

	return 0
}

update_sources() {
	cd $WORKINGDIR
	svn co https://svn.sourceforge.net/svnroot/freenas/trunk svn

	return 0
}

use_svn() {
	echo "Replacing old code with SVN code"

	cp -v -p $SVNDIR/etc/*.* $FREENAS/etc
	cp -v -p $SVNDIR/etc/* $FREENAS/etc
	cp -v -p $SVNDIR/etc/inc/*.* $FREENAS/etc/inc
	cp -v -p $SVNDIR/etc/defaults/*.* $FREENAS/etc/defaults
	cp -v -p $SVNDIR/www/*.* $FREENAS/usr/local/www
	cp -v -p $SVNDIR/www/syntaxhighlighter/*.* $FREENAS/usr/local/www/syntaxhighlighter
	cp -v -p $SVNDIR/conf/*.* $FREENAS/conf.default

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
4 - Software package
5 - Build bootloader
6 - Add necessary libraries
* - Quit
> '
  	read choice
  	case $choice in
  		1) create_rootfs;;
  		2) copy_files;;
  		3) build_kernel;;
  		4) build_softpkg;;
  		5) $SVNDIR/misc/freenas-create-bootdir.sh -f $BOOTDIR;;
  		6) add_libs;;
  		*) main;;
  	esac
  	[ 0 == $? ] && echo "=> Successful" || echo "=> Failed"
  	sleep 1
  done
}

build_softpkg() {
	tempfile=$WORKINGDIR/tmp$$
	packages=$WORKINGDIR/packages$$

	# Choose what to do.
	$DIALOG --title "$PRODUCTNAME - Software packages" --menu "Please select whether you want to build or install packages." 10 45 2 \
		"Build" "Build software packages" \
		"Install" "Install software packages" 2> $tempfile
	[ 0 != $? ] && return 1 # successful?
	choice=`cat $tempfile`
	rm $tempfile

	# Create list of available packages.
	echo "#! /bin/sh
$DIALOG --title \"$PRODUCTNAME - Software packages\" \\
--checklist \"Select the packages you want to process.\" 21 75 14 \\" > $tempfile

	for s in $SVNDIR/misc/software/*; do
		[ ! -d "$s" ] && continue
		package=`basename $s`
		desc=`cat $s/pkg-descr`
		state=`cat $s/pkg-state`
		echo "\"$package\" \"$desc\" $state \\" >> $tempfile
	done

	# Display list of available packages.
	sh $tempfile 2> $packages
	[ 0 != $? ] && return 1 # successful?
	rm $tempfile

	for package in $(cat $packages | tr -d '"'); do
		echo "======================================================================"
		cd $SVNDIR/misc/software/$package
		if [ "$choice" == "Build" ]; then
			make -I $MKINCLUDESDIR
		elif [ "$choice" == "Install" ]; then
			make -I $MKINCLUDESDIR install
		fi
		[ 0 != $? ] && return 1 # successful?
	done
	rm $packages

  return 0
}

main() {
	# Ensure $FREENASDISTFILES exists
	[ ! -d "$FREENASDISTFILES" ] && mkdir $FREENASDISTFILES

	# Ensure we are in $WORKINGDIR
	[ ! -d "$WORKINGDIR" ] && mkdir $WORKINGDIR
	cd $WORKINGDIR

	echo -n '
Welcome to the FreeNAS build environment.
Menu:
1  - Download and decompress FreeNAS root filesystem 
2  - Update the source to latest (need SVN)
10 - Create FreeNAS IMG file (rawrite to CF/USB/DD)
11 - Create FreeNAS ISO file (need cdrtools installed)
12 - Create FreeNAS ISO file without IMG image (need cdrtools installed)
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
