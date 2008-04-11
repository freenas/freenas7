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
FREENAS_MFSROOT_SIZE="45"
# Size in MB f the IMG file, that include zipped MFS Root filesystem image plus
# bootloader and kernel.
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

echo "===> Creating an empty IMG file"
dd if=/dev/zero of=$FREENAS_WORKINGDIR/image.bin bs=1M count=${FREENAS_IMG_SIZE}
echo "===> Use IMG as a memory disk"
md=`mdconfig -a -t vnode -f $FREENAS_WORKINGDIR/image.bin`
diskinfo -v ${md}
echo "===> Creating partition on this memory disk"
fdisk -BI -b $FREENAS_BOOTDIR/mbr ${md}
echo "===> Configuring FreeBSD label on this memory disk"
bsdlabel -w -B -b $FREENAS_BOOTDIR/boot ${md} auto
bsdlabel ${md} >/tmp/label.$$
# Replace the a: unuset by a a:4.2BSD
#Replacing c: with a: is a trick, when this file is apply, this line is ignored
bsdlabel ${md} |
	 sed "s/c:/a:/" |
	 sed "s/unused/4.2BSD/" >/tmp/label.$$
bsdlabel -R -B -b $FREENAS_BOOTDIR/boot ${md} /tmp/label.$$
rm -f /tmp/label.$$
bsdlabel ${md}
echo "===> Formatting this memory disk using UFS"
newfs ${FREENAS_NEWFS} /dev/${md}a

# ToDo: Install grub boot loader here.

echo "===> Unmount memory disk"
#umount $FREENAS_TMPDIR
echo "===> Detach memory disk"
#mdconfig -d -u ${md}
