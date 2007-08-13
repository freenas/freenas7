#!/bin/sh
#
# This script was written by David Courtney of Ultradesic
# http://www.ultradesic.com
# E-Mail Contact: minibsd@ultradesic.com
#
# Adapted for m0n0wall on FreeBSD 6.1 by Olivier Cochard-Labbé (http://www.freenas.org)
# Modified by Volker Theile (votdev@gmx.de)

MINIBSD_DIR=${FREENAS_ROOTDIR}/bootloader;
ARCH=$(uname -p)

# Initialize variables.
opt_a=0
opt_b=0
opt_d=0
opt_m=0
opt_s=0
opt_f=0

# Parse the command-line options.
while getopts 'abdfhms' option
do
	case "$option" in
    "a")  opt_a=1;;
    "b")  opt_b=1;;
    "d")  opt_d=1;;
    "m")  opt_m=1;;
    "f")  opt_f=1;;
    "s")  opt_s=1;;
    "h")  echo "$(basename $0): Build boot loader";
          echo "Common Options:";
          echo "  -a    Disable ACPI"
          echo "  -b    Enable bootsplash";
          echo "  -d    Enable debug"
          echo "  -m    Enable bootmenu";
          echo "  -s    Enable serial console";
          echo "  -f    Force executing this script";
          exit 1;;
    ?)    echo "$0: Bad option specified. Exiting...";
          exit 1;;
  esac
done

shift `expr $OPTIND - 1`

echo "Building the boot loader..."

if [ -n "$1" ]; then
  MINIBSD_DIR=$1
  echo "Using directory $1."
fi

if [ 1 != $opt_f -a -d "$MINIBSD_DIR" ]; then
  echo
  echo "=> $MINIBSD_DIR already exists. Remove the directory"
  echo "=> before running this script."
  echo
  echo "=> Exiting..."
  echo
  exit 1
fi

# Create the boot directory that will contain boot, and kernel
mkdir $MINIBSD_DIR
mkdir $MINIBSD_DIR/defaults
mkdir $MINIBSD_DIR/kernel

# Copy required files
cp -v /boot/defaults/loader.conf $MINIBSD_DIR/defaults
cp -v /boot/loader $MINIBSD_DIR
cp -v /boot/boot $MINIBSD_DIR
cp -v /boot/mbr $MINIBSD_DIR
cp -v /boot/cdboot $MINIBSD_DIR
cp -v /boot/loader.4th $MINIBSD_DIR
cp -v /boot/support.4th $MINIBSD_DIR
cp -v /boot/device.hints $MINIBSD_DIR
# Copy files required by bootmenu
if [ 0 != $opt_m ]; then
	cp -v /boot/screen.4th $MINIBSD_DIR
	cp -v /boot/frames.4th $MINIBSD_DIR
fi

# Generate the loader.rc file using by bootloader
echo "Generate $MINIBSD_DIR/loader.rc"
echo 'include /boot/loader.4th
start
check-password' > $MINIBSD_DIR/loader.rc
# Enable bootmenu
if [ 0 != $opt_m ]; then
	echo 'include /boot/menu.4th' >> $MINIBSD_DIR/loader.rc
	echo 'menu-start' >> $MINIBSD_DIR/loader.rc
fi

# Generate the loader.conf file using by bootloader
echo "Generate $MINIBSD_DIR/loader.conf"
echo 'mfsroot_load="YES"
mfsroot_type="mfs_root"
mfsroot_name="/mfsroot"
#Reduce Kernel timer frequency for better performace in Virtual environement
#explanation here: http://ivoras.sharanet.org/freebsd/vmware.html
kern.hz="100"' > $MINIBSD_DIR/loader.conf
# Enable bootsplash?
if [ 0 != $opt_b ]; then
	echo 'splash_bmp_load="YES"' >> $MINIBSD_DIR/loader.conf
	echo 'bitmap_load="YES"' >> $MINIBSD_DIR/loader.conf
	echo 'bitmap_name="/boot/splash.bmp"' >> $MINIBSD_DIR/loader.conf
fi
# Enable bootmenu?
if [ 0 != $opt_m ]; then
	echo 'autoboot_delay="5"' >> $MINIBSD_DIR/loader.conf
else
	echo 'autoboot_delay="-1"' >> $MINIBSD_DIR/loader.conf
fi
# Enable debug?
if [ 0 != $opt_d ]; then
  echo 'verbose_loading="YES"' >> $MINIBSD_DIR/loader.conf
  echo 'boot_verbose=""' >> $MINIBSD_DIR/loader.conf
fi
# Enable serial console?
if [ 0 != $opt_s ]; then
  echo 'console="comconsole"' >> $MINIBSD_DIR/loader.conf
fi
# Disable ACPI?
if [ 0 != $opt_a ]; then
  echo 'hint.acpi.0.disabled="1"' >> $MINIBSD_DIR/device.hints
fi

# Copy kernel.
if [ -e "${MAKEOBJDIRPREFIX}/usr/src/sys/FREENAS-$ARCH/kernel.gz" ] ; then
  cp ${MAKEOBJDIRPREFIX}/usr/src/sys/FREENAS-$ARCH/kernel.gz $MINIBSD_DIR/kernel
else
  echo "=> ERROR: File kernel.gz does not exist!";
  exit 1;
fi
