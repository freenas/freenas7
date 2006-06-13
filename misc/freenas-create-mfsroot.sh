#!/bin/sh
# Generate the mfs ROOT file system

#Setting directory

WORKINGDIR="/usr/local/freenas"
FREENAS="/usr/local/freenas/rootfs"
CDROOT="/usr/local/freenas/cdroot"
BOOTDIR="/usr/local/freenas/bootloader"

#lauching the script

# Remove old file
if [ $WORKINGDIR/mfsroot.gz ] ; then
        echo ;
        echo "mfsroot.gz already exists.  Removing this file" ;
        echo ;
	    rm $WORKINGDIR/mfsroot.gz;
	    #exit ;
fi ;

# umount the /mnt directory
umount /mnt
# Remove the memory file
mdconfig -d -u 0
# Create a 32Mb empty file
dd if=/dev/zero of=$WORKINGDIR/mfsroot bs=1k count=32768
# Configure this file as a virtual disk
mdconfig -a -t vnode -f $WORKINGDIR/mfsroot -u 0
# Create Label on this disk
bsdlabel -w md0 auto
# format it
newfs -b 8192 -f 1024 -o space -m 0 /dev/md0c
# Mount it
mount /dev/md0c /mnt
# Copy the file on it
cd /mnt
tar -cf - -C $FREENAS ./ | tar -xvpf -
cd $WORKINGDIR
# umount the /mnt directory
umount /mnt
# Remove the memory file
mdconfig -d -u 0
echo Compresing....
gzip -9 mfsroot
