#!/usr/bin/env bash
# This is a script designed to automate the assembly of
# a FreeNAS box.
# Created: 2/12/2006 by Scott Zahn
# Modified: 11/2006 by Volker Theile (votdev@gmx.de)

# Global Variables:
PRODUCTNAME="FreeNAS"
WORKINGDIR="/usr/local/freenas"
FREENAS="/usr/local/freenas/rootfs"
BOOTDIR="/usr/local/freenas/bootloader"
SVNDIR="/usr/local/freenas/svn"
TMPDIR="/tmp/freenastmp"
VERSION=`cat $SVNDIR/etc/version`

#Size in MB of the MFS Root filesystem that will include all FreeBSD binary and FreeNAS WEbGUI/Scripts
#Keep this file very small! This file is unzipped to a RAM disk at FreeNAS startup
MFSROOT_SIZE="42"
#Size in MB f the IMG file, that include zipped MFS Root filesystem image plus bootlaoder and kernel.
IMG_SIZE="21"

# URL's:
URL_FREENASROOTFS="http://www.freenas.org/downloads/freenas-rootfs.tgz"
URL_FREENASBOOT="http://www.freenas.org/downloads/freenas-boot.tgz"
URL_GEOMRAID5="http://home.tiscali.de/cmdr_faako/geom_raid5.tbz"

# Check if needed packages are installed.
check_packages() {
  result=0
  echo "Check if all needed packages are installed to compile properly:"
	for pkg in $@; do
		echo -n "checking for $pkg... "
		installed=$(pkg_info | grep $pkg)
		if [ -z "$installed" ]; then
			echo "no"
			result=1
		else
			echo "yes"
		fi
	done
	return $result
}

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
		  mkdir -v $FREENAS/$dir
		fi
		# Copy files
		cp -v -p /$file $FREENAS/$(echo $file | rev | cut -d "/" -f 2- | rev)
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
	echo $VERSION > $FREENAS/etc/version
	date > $FREENAS/etc/version.buildtime

  # Config file: config.xml
  cd $FREENAS/conf.default/
  cp -v $SVNDIR/conf/config.xml .

  # Zone Info.
  cp -v $SVNDIR/misc/zoneinfo.tgz $FREENAS/usr/share

  return 0
}

# Building the kernel
build_kernel() {
  # Adding specials drivers:
  # A100U2 U2W-SCSI-Controller
  mkdir -p $TMPDIR/a100
  cd $TMPDIR/a100
  tar -zxvf $SVNDIR/misc/drivers/bsd4a100.zip
  cp a100.* /usr/src/sys/pci
  echo "pci/a100.c optional ihb device-driver" >> /usr/src/sys/conf/files

  # Compiling and compressing the kernel
	cd /sys/i386/conf
	if [ -f FREENAS ]; then
		rm -f FREENAS
	fi
	cp $SVNDIR/misc/kernel-config/FREENAS .
	config FREENAS
	cd ../compile/FREENAS/
	make cleandepend; make depend
	make
	gzip -f -9 kernel

  # Installing the modules.
	cp -v -p modules/usr/src/sys/modules/geom/geom_vinum/geom_vinum.ko $FREENAS/boot/kernel
	cp -v -p modules/usr/src/sys/modules/geom/geom_stripe/geom_stripe.ko $FREENAS/boot/kernel
	cp -v -p modules/usr/src/sys/modules/geom/geom_concat/geom_concat.ko $FREENAS/boot/kernel
	cp -v -p modules/usr/src/sys/modules/geom/geom_mirror/geom_mirror.ko $FREENAS/boot/kernel
	cp -v -p modules/usr/src/sys/modules/geom/geom_gpt/geom_gpt.ko $FREENAS/boot/kernel
	cp -v -p modules/usr/src/sys/modules/geom/geom_eli/geom_eli.ko $FREENAS/boot/kernel
	cp -v -p modules/usr/src/sys/modules/ntfs/ntfs.ko $FREENAS/boot/kernel
	cp -v -p modules/usr/src/sys/modules/ext2fs/ext2fs.ko $FREENAS/boot/kernel/

  # Adding experimental geom RAID 5 module
  cd /usr/src
  geomraid5_tarball=$(urlbasename $URL_GEOMRAID5)
  if [ ! -f "$geomraid5_tarball" ]; then
    fetch $URL_GEOMRAID5
    if [ 1 == $? ]; then
      echo "==> Failed to fetch $geomraid5_tarball."
      return 1
    fi
  fi
  tar zxvf $geomraid5_tarball
  cd /usr/src/sys/modules/geom/geom_raid5/
  make depend
  make
  cp -v geom_raid5.ko $FREENAS/boot/kernel/
  cd /usr/src/sbin/geom/class/raid5/
  mkdir /usr/include/geom/raid5
  cp -v /usr/src/sys/geom/raid5/g_raid5.h /usr/include/geom/raid5/
  make depend
  make
  make install
  cp -v -p /sbin/graid5 $FREENAS/sbin/
  cp -v geom_raid5.so $FREENAS/lib/geom/

  # Installing the mbr.
	cp -v -p /boot/mbr $FREENAS/boot/
	cp -v -p /boot/boot $FREENAS/boot/
  cp -v -p /boot/boot0 $FREENAS/boot/

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
	date > $FREENAS/etc/version.buildtime
	
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
	date > $FREENAS/etc/version.buildtime
	
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
		cp $WORKINGDIR/$PRODUCTNAME-generic-pc-$VERSION.img $TMPDIR/FreeNAS-generic-pc.gz
	fi

	echo "ISO: Generating the ISO file"
	mkisofs -b "boot/cdboot" -no-emul-boot -A "FreeNAS CD-ROM image" -c "boot/boot.catalog" -d -r -publisher "freenas.org" -p "Olivier Cochard-Labbe" -V "freenas_cd" -o "$ISOFILENAME" $TMPDIR
	
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

	# Translate *po files to *.mo.
	for i in $(ls $SVNDIR/locale/*.po); do
		filename=$(basename $i)
		language=${filename%*.po}
		filename=$(echo $PRODUCTNAME | tr '[A-Z]' '[a-z]') # make filename lower case.
		mkdir -v -p $FREENAS/usr/local/share/locale/$language/LC_MESSAGES
		msgfmt -v --output-file="$FREENAS/usr/local/share/locale/$language/LC_MESSAGES/$filename.mo" $i
	done

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
  echo "Software Package"
  echo "Menu:"

  count=0

	for s in $SVNDIR/misc/software/*; do
		package=`basename $s`
		let count=$count+1
		echo "$count - Build $package" | awk '{printf("%-35s",$0)}'
		script[$count]="$s/build.sh"
		function[$count]="build_$package"
		let count=$count+1
		echo "$count - Install $package"
		script[$count]="$s/build.sh"
		function[$count]="install_$package"
	done

  let buildall=$count+1
  let installall=$count+2

  echo "$buildall - Build all" | awk '{printf("%-35s",$0)}'
	echo -n "$installall - Install all
* - Quit
> "

  read choice

  case "$choice" in
    [0-9]*)   
      if [ "$choice" == "$buildall" ]; then
        for s in $SVNDIR/misc/software/*; do
          package=`basename $s`
          source $s/build.sh
          build_$package
          [ 0 != $? ] && break
        done
      elif [ "$choice" == "$installall" ]; then
        for s in $SVNDIR/misc/software/*; do
          package=`basename $s`
          source $s/build.sh
          install_$package
          [ 0 != $? ] && break
        done           
      elif [ "$choice" -le "$count" ]; then
        echo "Sourcing script ${script[$choice]}"
        source ${script[$choice]}
        echo "Running ${function[$choice]}"
        ${function[$choice]}
      fi;;
    *) fromscratch;;
  esac

  sleep 1
}

main() {
	# Ensure we are in $WORKINGDIR
	[ ! -d "$WORKINGDIR" ] && mkdir $WORKINGDIR
	cd $WORKINGDIR

	echo -n '
Welcome to the FreeNAS build environment.
Menu:
1  - Download and decompress FreeNAS root filesystem 
2  - Update the source to latest (need SVN)
10 - Create FreeNAS IMG file (rawrite to CF/USB/DD)
11 - Create FreeNAS ISO file (need cdrtool installed)
12 - Create FreeNAS ISO file without IMG image (need cdrtool installed)
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
