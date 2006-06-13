#!/bin/sh
# FreeNAS script for generating .img

# setting variables
FREENAS="/usr/local/freenas/rootfs"
WORKINGDIR="/usr/local/freenas"
CDROOT="/usr/local/freenas/imgroot"
BOOTDIR="/usr/local/freenas/bootloader"
PLATFORM="generic-pc"
VERSION=`cat $FREENAS/etc/version`
ISOFILENAME="FreeNAS-$PLATFORM-$VERSION.img"

# Remove old directory
if [ -d $CDROOT ] ; then
        echo ;
        echo "$CDROOT already exists.  Removing this directory" ;
        echo ;
        echo ;
	    rm -rf $CDROOT;
	    #exit ;
fi ;

if [ $WORKINGDIR/$ISOFILENAME ] ; then
        echo ;
        echo "$ISOFILENAME already exists.  Removing this directory" ;
        echo ;
        echo ;
	    rm $WORKINGDIR/$ISOFILENAME;
	    #exit ;
fi ;

echo $PLATFORM > $FREENAS/etc/platform
date > $FREENAS/etc/version.buildtime
$WORKINGDIR/freenas-create-mfsroot.sh
echo Generating $CDROOT folder
mkdir $CDROOT
#Creating a 16Mb empty file
dd if=/dev/zero of=image.bin bs=1k count=18432
#use this file as a virtual RAM disk
mdconfig -a -t vnode -f image.bin -u 0
#Create partition on this disk
fdisk -BI -b $BOOTDIR/mbr /dev/md0
#Create label on this disk
bsdlabel -B -w -b $BOOTDIR/boot /dev/md0 auto
bsdlabel md0 >/tmp/label.$$
bsdlabel md0 |
     egrep unused |
     sed "s/c:/a:/" |
     sed "s/unused/4.2BSD/" >>/tmp/label.$$
bsdlabel -R -B md0 /tmp/label.$$
rm -f /tmp/label.$$
#Create filesystem on this disk
newfs -b 8192 -f 1024 -o space -m 0 /dev/md0a
#Mount this disk
mount /dev/md0a $CDROOT
cp $WORKINGDIR/mfsroot.gz $CDROOT
mkdir $CDROOT/boot
mkdir $CDROOT/boot/kernel $CDROOT/boot/defaults
mkdir $CDROOT/conf
cp $FREENAS/conf.default/config.xml $CDROOT/conf
cp $BOOTDIR/kernel/kernel.gz $CDROOT/boot/kernel
#cp $BOOTDIR/kernel/acpi.ko $CDROOT/boot/kernel
cp $BOOTDIR/boot $CDROOT/boot
cp $BOOTDIR/loader $CDROOT/boot
cp $BOOTDIR/loader.conf $CDROOT/boot
cp $BOOTDIR/loader.rc $CDROOT/boot
cp $BOOTDIR/loader.4th $CDROOT/boot
cp $BOOTDIR/support.4th $CDROOT/boot
cp $BOOTDIR/defaults/loader.conf $CDROOT/boot/defaults/
cp $BOOTDIR/device.hints $CDROOT/boot
umount $CDROOT
mdconfig -d -u 0
gzip -9 image.bin
mv image.bin.gz $ISOFILENAME
# Cleaning directory and temp file
if [ $WORKINGDIR/mfsroot.gz ] ; then
		echo ;
        echo "cleaning mfsroot.gz" ;
        echo ;
		rm $WORKINGDIR/mfsroot.gz 
fi;

if [ -d $CDROOT ] ; then
        echo ;
        echo "cleaning $CDROOT by Removing this directory" ;
        echo ;
        echo ;
	    rm -rf $CDROOT;
fi ;
