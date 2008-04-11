sysctl kern.geom.debugflags=16
grub-install /dev/ad0s1


MBR sichern

# dd if=/dev/ad0 of=/boot/bootgrub bs=512 count=1

Zur Wiederherstellung des MBR muss von einem Notfallbootmedium gestartet werden!

# dd if=/root/bootgrub of=/dev/ad0 bs=512 count=1


grub --batch --device-map=/tmp/freenastmp/boot/grub/device.map


hd0,0,a
/usr/local/sbin/grub --batch --device-map=/mnt/ad0/boot/grub/device.map


grub --batch --device-map=/boot/grub/device.map < /etc/grub.conf



# grub-install /dev/md0
+ prefix=/usr/local
+ exec_prefix=/usr/local
+ sbindir=/usr/local/sbin
+ libdir=/usr/local/share
+ PACKAGE=grub
+ VERSION=0.97
+ host_cpu=i386
+ host_os=freebsd6.3
+ host_vendor=freebsd
+ pkglibdir=/usr/local/share/grub/i386-freebsd
+ grub_shell=/usr/local/sbin/grub
+ grub_set_default=/usr/local/sbin/grub-set-default
+ log_file=/tmp/grub-install.log.1195
+ img_file=/tmp/grub-install.img.1195
+ rootdir=
+ grub_prefix=/boot/grub
+ install_device=
+ no_floppy=
+ force_lba=
+ recheck=no
+ debug=no
+ test -x /bin/tempfile
+ test -x /bin/mktemp
+ mklog=
+ mkimg=
+ test x != x
+ install_device=/dev/md0
+ test x/dev/md0 = x
+ test no = yes
+ bootdir=/boot
+ grubdir=/boot/grub
+ device_map=/boot/grub/device.map
+ set /usr/local/sbin/grub dummy
+ test -f /usr/local/sbin/grub
+ :
+ test -f /usr/local/share/grub/i386-freebsd/stage1
+ :
+ test -f /usr/local/share/grub/i386-freebsd/stage2
+ :
+ test -d /boot
+ test -d /boot/grub
+ test no = yes
+ test -f /boot/grub/device.map
+ :
+ sed -n /^([fh]d[0-9]*)/s/\(^(.*)\).*/\1/p /boot/grub/device.map
+ sort
+ uniq -d
+ sed -n 1p
+ tmp=
+ test -n
+ resolve_symlink /dev/md0
+ tmp_fname=/dev/md0
+ test -L /dev/md0
+ echo /dev/md0
+ install_device=/dev/md0
+ convert /dev/md0
+ test -e /dev/md0
+ :
+ echo /dev/md0
+ sed s%r\{0,1\}\([saw]d[0-9]*\).*$%\1%
+ sed s%r\{0,1\}\(da[0-9]*\).*$%\1%
+ tmp_disk=/dev/md0
+ sed s%.*/r\{0,1\}[saw]d[0-9]\(s[0-9]*[a-h]\)%\1%
+ sed s%.*/r\{0,1\}da[0-9]\(s[0-9]*[a-h]\)%\1%
+ echo /dev/md0
+ tmp_part=/dev/md0
+ grep -v ^#+ grep /dev/md0 *$
+ sed s%.*\(([hf]d[0-9][a-g0-9,]*)\).*%\1%
 /boot/grub/device.map
+ tmp_drive=(hd1)
+ test x(hd1) = x
+ test x/dev/md0 != x
+ echo /dev/md0+ grep ^s

+ echo /dev/md0
+ grep [a-h]$
+ echo (hd1)
+ install_drive=(hd1)
+ test x(hd1) = x
+ find_device
+ df /
+ sed -n s%.*\(/dev/[^  ]*\).*%\1%p
+ tmp_fname=/dev/ad0s1a
+ test -z /dev/ad0s1a
+ resolve_symlink /dev/ad0s1a
+ tmp_fname=/dev/ad0s1a
+ test -L /dev/ad0s1a
+ echo /dev/ad0s1a
+ tmp_fname=/dev/ad0s1a
+ echo /dev/ad0s1a
+ root_device=/dev/ad0s1a
+ find_device /boot
+ df /boot/
+ sed -n s%.*\(/dev/[^  ]*\).*%\1%p
+ tmp_fname=/dev/ad0s1a
+ test -z /dev/ad0s1a
+ resolve_symlink /dev/ad0s1a
+ tmp_fname=/dev/ad0s1a
+ test -L /dev/ad0s1a
+ echo /dev/ad0s1a
+ tmp_fname=/dev/ad0s1a
+ echo /dev/ad0s1a
+ bootdir_device=/dev/ad0s1a
+ test x/dev/ad0s1a != x/dev/ad0s1a
+ convert /dev/ad0s1a
+ test -e /dev/ad0s1a
+ :
+ echo /dev/ad0s1a
+ sed s%r\{0,1\}\([saw]d[0-9]*\).*$%\1%
+ sed s%r\{0,1\}\(da[0-9]*\).*$%\1%
+ tmp_disk=/dev/ad0
+ echo /dev/ad0s1a
+ + sed s%.*/r\{0,1\}[saw]d[0-9]\(s[0-9]*[a-h]\)%\1%
sed s%.*/r\{0,1\}da[0-9]\(s[0-9]*[a-h]\)%\1%
+ tmp_part=s1a
+ grep -v ^# /boot/grub/device.map
+ grep /dev/ad0 *$
+ sed s%.*\(([hf]d[0-9][a-g0-9,]*)\).*%\1%
+ tmp_drive=(hd0)
+ test x(hd0) = x
+ test xs1a != x
+ echo s1a
+ grep ^s
+ echo s1a
+ sed s%s\([0-9]*\)[a-h]*$%\1%
+ tmp_pc_slice=1
+ echo (hd0)
+ expr 1 - 1
+ sed s%)%,0)%
+ tmp_drive=(hd0,0)
+ echo s1a
+ grep [a-h]$
+ sed s%s\{0,1\}[0-9]*\([a-h]\)$%\1%
+ echo s1a
+ tmp_bsd_partition=a
+ echo (hd0,0)
+ sed s%)%,a)%
+ tmp_drive=(hd0,0,a)
+ echo (hd0,0,a)
+ root_drive=(hd0,0,a)
+ test x(hd0,0,a) = x
+ find_device /boot/grub
+ df /boot/grub/
+ sed -n s%.*\(/dev/[^  ]*\).*%\1%p
+ tmp_fname=/dev/ad0s1a
+ test -z /dev/ad0s1a
+ resolve_symlink /dev/ad0s1a
+ tmp_fname=/dev/ad0s1a
+ test -L /dev/ad0s1a
+ echo /dev/ad0s1a
+ tmp_fname=/dev/ad0s1a
+ echo /dev/ad0s1a
+ grubdir_device=/dev/ad0s1a
+ test x/dev/ad0s1a != x/dev/ad0s1a
+ rm -f /boot/grub/stage1
+ rm -f /boot/grub/stage2
+ rm -f /boot/grub/e2fs_stage1_5
+ rm -f /boot/grub/fat_stage1_5
+ rm -f /boot/grub/ffs_stage1_5
+ rm -f /boot/grub/iso9660_stage1_5
+ rm -f /boot/grub/jfs_stage1_5
+ rm -f /boot/grub/minix_stage1_5
+ rm -f /boot/grub/reiserfs_stage1_5
+ rm -f /boot/grub/ufs2_stage1_5
+ rm -f /boot/grub/vstafs_stage1_5
+ rm -f /boot/grub/xfs_stage1_5
+ cp -f /usr/local/share/grub/i386-freebsd/stage1 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/stage2 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/e2fs_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/fat_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/ffs_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/iso9660_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/jfs_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/minix_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/reiserfs_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/ufs2_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/vstafs_stage1_5 /boot/grub
+ cp -f /usr/local/share/grub/i386-freebsd/xfs_stage1_5 /boot/grub
+ /usr/local/sbin/grub-set-default --root-directory= default
+ test -n
+ test -n
+ count=5
+ echo /boot/grub/stage1
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/stage1
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/stage1 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ echo /boot/grub/stage2
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/stage2
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/stage2 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ echo /boot/grub/e2fs_stage1_5
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/e2fs_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/e2fs_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ echo /boot/grub/fat_stage1_5
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/fat_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/fat_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ echo /boot/grub/ffs_stage1_5
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/ffs_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/ffs_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ echo /boot/grub/iso9660_stage1_5
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/iso9660_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/iso9660_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ echo /boot/grub/jfs_stage1_5
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/jfs_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/jfs_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ echo /boot/grub/minix_stage1_5
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/minix_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/minix_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ echo /boot/grub/reiserfs_stage1_5
+ sed s|^/boot/grub|/boot/grub|
+ tmp=/boot/grub/reiserfs_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/reiserfs_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ + echosed /boot/grub/ufs2_stage1_5 s|^/boot/grub|/boot/grub|

+ tmp=/boot/grub/ufs2_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/ufs2_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ + echosed /boot/grub/vstafs_stage1_5 s|^/boot/grub|/boot/grub|

+ tmp=/boot/grub/vstafs_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/vstafs_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ count=5
+ + echosed /boot/grub/xfs_stage1_5 s|^/boot/grub|/boot/grub|

+ tmp=/boot/grub/xfs_stage1_5
+ test 5 -gt 0
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ cmp /boot/grub/xfs_stage1_5 /tmp/grub-install.img.1195
+ break
+ test 5 -eq 0
+ rm -f /tmp/grub-install.img.1195
+ rm -f /tmp/grub-install.log.1195
+ test -n
+ /usr/local/sbin/grub --batch --device-map=/boot/grub/device.map
+ grep Error [0-9]*:  /tmp/grub-install.log.1195
+ test no = yes
+ rm -f /tmp/grub-install.log.1195
+ echo Installation finished. No error reported.
Installation finished. No error reported.
+ echo This is the contents of the device map /boot/grub/device.map.
This is the contents of the device map /boot/grub/device.map.
+ echo Check if this is correct or not. If any of the lines is incorrect,
Check if this is correct or not. If any of the lines is incorrect,
+ echo fix it and re-run the script `grub-install'.
fix it and re-run the script `grub-install'.
+ echo

+ cat /boot/grub/device.map
(fd0)   /dev/fd0
(hd0)   /dev/ad0
(hd1)   /dev/md0
+ exit 0

