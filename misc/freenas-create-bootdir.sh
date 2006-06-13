#!/bin/sh
#
# This script was written by David Courtney of Ultradesic
# http://www.ultradesic.com
# E-Mail Contact: minibsd@ultradesic.com
#
# Adapted for m0n0wall on FreeBSD 6.1 by Olivier Cochard-Labbé (http://www.freenas.org)

MINIBSD_DIR=/usr/local/freenas/bootloader ;

if [ -d $MINIBSD_DIR ] ; then
	echo ;
	echo "$MINIBSD_DIR already exists.  Remove the directory" ;
	echo "before running this script." ;
	echo ;
	echo "Exiting..." ;
	echo ;
	exit ;
fi ;
# Create the boot directory that will contain boot, and kernel
mkdir $MINIBSD_DIR ;
mkdir $MINIBSD_DIR/defaults
mkdir $MINIBSD_DIR/kernel
# Copy the file in this directory:
cp /boot/defaults/loader.conf $MINIBSD_DIR/defaults
cp /boot/loader $MINIBSD_DIR
cp /boot/boot $MINIBSD_DIR
cp /boot/mbr $MINIBSD_DIR
cp /boot/cdboot $MINIBSD_DIR
cp /boot/loader.rc $MINIBSD_DIR
cp /boot/loader.4th $MINIBSD_DIR
cp /boot/support.4th $MINIBSD_DIR
cp /boot/device.hints $MINIBSD_DIR
# Generate the loader.conf file using by bootloader
echo "mfsroot_load=\"YES\"" > $MINIBSD_DIR/loader.conf
echo "mfsroot_type=\"mfs_root\"" >> $MINIBSD_DIR/loader.conf
echo "mfsroot_name=\"/mfsroot\"" >> $MINIBSD_DIR/loader.conf
echo "autoboot_delay=\"-1\"" >> $MINIBSD_DIR/loader.conf
cp /sys/i386/compile/FREENAS/kernel.gz $MINIBSD_DIR/kernel
