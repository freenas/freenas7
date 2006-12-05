#!/usr/bin/env bash
#
# This script was written by David Courtney of Ultradesic
# http://www.ultradesic.com
# E-Mail Contact: minibsd@ultradesic.com
#
# Adapted for m0n0wall on FreeBSD 6.1 by Olivier Cochard-Labbé (http://www.freenas.org)
# Modified by Volker Theile (votdev@gmx.de)

MINIBSD_DIR=/usr/local/freenas/bootloader;

echo "Building the boot loader..."

# Initialize variables.
opt_a=''
opt_d=''
opt_s=''

# Parse the command-line options.
while getopts 'ads' option
do
	case "$option" in
    "a")  opt_a="1";;
    "d")  opt_d="1";;
    "s")  opt_s="1";;
    ?)    echo "$0: Bad option specified. Exiting..."
          exit 1;;
  esac
done

shift `expr $OPTIND - 1`

if [ ! -z "$1" ]; then
  MINIBSD_DIR=$1;
  echo "Using directory $1.";
fi

if [ -d $MINIBSD_DIR ] ; then
  echo ;
  echo "$MINIBSD_DIR already exists.  Remove the directory" ;
  echo "before running this script." ;
  echo ;
  echo "Exiting..." ;
  echo ;
  exit 1 ;
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
if [ "$opt_a" != "" ]; then
  echo "hint.acpi.0.disabled=\"1\"" >> $MINIBSD_DIR/device.hints
fi
if [ "$opt_d" != "" ]; then
  echo "verbose_loading=\"YES\"" >> $MINIBSD_DIR/loader.conf
  echo "boot_verbose=\"\"" >> $MINIBSD_DIR/loader.conf
fi
if [ "$opt_s" != "" ]; then
  echo "console=\"comconsole\"" >> $MINIBSD_DIR/loader.conf
fi

cp /sys/i386/compile/FREENAS/kernel.gz $MINIBSD_DIR/kernel
