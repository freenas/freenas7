#!/bin/sh
# FreeNAS script for generating iso file
FREENAS="/usr/local/freenas/rootfs"
WORKINGDIR="/usr/local/freenas"
CDROOT="/usr/local/freenas/cdroot"
BOOTDIR="/usr/local/freenas/bootloader"
PLATFORM="generic-pc-cdrom"
VERSION=`cat $FREENAS/etc/version`
ISOFILENAME="FreeNAS-$VERSION.iso"

#remove old directory
if [ -d $CDROOT ] ; then
        echo ;
        echo "$CDROOT already exists.  Removing this directory" ;
        echo "before running this script." ;
        echo ;
        #echo "Exiting..." ;
        echo ;
	    rm -rf $CDROOT;
	    #exit ;
fi ;

#Setting the variable for CF image:
echo "$PLATFORM" > $FREENAS/etc/platform
date > $FREENAS/etc/version.buildtime

#Generating the mfsroot file
$WORKINGDIR/freenas-create-mfsroot.sh
rm $WORKINGDIR/$ISOFILENAME

echo Generating $CDROOT folder
mkdir $CDROOT
cp $WORKINGDIR/mfsroot.gz $CDROOT
mkdir $CDROOT/boot
mkdir $CDROOT/boot/kernel $CDROOT/boot/defaults
cp $BOOTDIR/kernel/kernel.gz $CDROOT/boot/kernel
#cp $BOOTDIR/kernel/acpi.ko $CDROOT/boot/kernel
cp $BOOTDIR/cdboot $CDROOT/boot
cp $BOOTDIR/loader $CDROOT/boot
cp $BOOTDIR/loader.conf $CDROOT/boot
cp $BOOTDIR/loader.rc $CDROOT/boot
cp $BOOTDIR/loader.4th $CDROOT/boot
cp $BOOTDIR/support.4th $CDROOT/boot
cp $BOOTDIR/defaults/loader.conf $CDROOT/boot/defaults/
cp $BOOTDIR/device.hints $CDROOT/boot

#Generating the Image file release:
$WORKINGDIR/freenas-create-image.sh
cp $WORKINGDIR/FreeNAS-generic-pc-$VERSION.img $CDROOT/FreeNAS-generic-pc.gz


#Generating the ISO file
mkisofs -b "boot/cdboot" -no-emul-boot -A "FreeNAS CD-ROM image" -c "boot/boot.catalog" -d -r -publisher "freenas.org" -p "Olivier Cochard" -V "freenas_cd" -o "$ISOFILENAME" $CDROOT
if [ -d $CDROOT ] ; then
        echo ;
        echo "Cleaning directory" ;
        echo ;
        echo ;
            rm -rf $CDROOT;
		rm mfsroot.gz;
            #exit ;
fi ;

