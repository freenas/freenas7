#!/bin/sh
#
# This script was written by David Courtney of Ultradesic
# http://www.ultradesic.com
# E-Mail Contact: minibsd@ultradesic.com
#
# Adapted for FreeNAS by Olivier Cochard-Labbé (http://www.freenas.org)
# Modified by Volker Theile (votdev@gmx.de)

MINIBSD_DIR=/usr/local/freenas/rootfs ;

echo "Create FreeNAS directory structure..."

if [ ! -z "$1" ]; then
  MINIBSD_DIR=$1;
  echo "Using directory $1.";
fi

if [ -d "$MINIBSD_DIR" ] ; then
  echo ;
  echo "$MINIBSD_DIR already exists. Remove the directory" ;
  echo "before running this script." ;
  echo ;
  echo "Exiting..." ;
  echo ;
  exit ;
fi ;

mkdir $MINIBSD_DIR ;
cd $MINIBSD_DIR ;
mkdir boot ;
mkdir boot/kernel ;
mkdir bin ;
mkdir cf ;
mkdir ftmp ;
mkdir conf.default ;
mkdir dev ;
mkdir etc ;
mkdir etc/defaults ;
mkdir etc/inc ;
mkdir etc/pam.d ;
mkdir etc/ssh ;
mkdir lib ;
mkdir libexec ;
mkdir -m 0777 mnt ;
mkdir -m 0700 root ;
mkdir sbin ;
mkdir usr ;
mkdir usr/bin ;
mkdir usr/lib ;
mkdir usr/lib/aout ;
mkdir usr/libexec ;
mkdir usr/local ;
mkdir usr/local/bin;
mkdir usr/local/lib ;
mkdir usr/local/sbin ;
mkdir usr/local/www ;
mkdir usr/sbin ;
mkdir usr/share ;
mkdir tmp;
# share/empty mandatory for VSFTPD
mkdir usr/share/empty ;
mkdir var ;
#ln -s var/tmp tmp
# Creating symbolic links
ln -s cf/conf conf
ln -s /var/run/htpasswd usr/local/www/.htpasswd
ln -s /var/etc/resolv.conf etc/resolv.conf
ln -s /var/etc/master.passwd etc/master.passwd
ln -s /var/etc/passwd etc/passwd
ln -s /var/etc/group etc/group
ln -s /var/etc/pwd.db etc/pwd.db
ln -s /var/etc/spwd.db etc/spwd.db
ln -s /var/etc/crontab etc/crontab
ln -s /var/etc/ssh/sshd_config etc/ssh/sshd_config
ln -s /var/etc/ssh/ssh_host_dsa_key etc/ssh/ssh_host_dsa_key
ln -s /var/etc/pam.d/ftp etc/pam.d/ftp
ln -s /var/etc/pam.d/sshd etc/pam.d/sshd
ln -s /var/etc/pam.d/login etc/pam.d/login
ln -s /var/etc/nsswitch.conf etc/nsswitch.conf
ln -s /libexec/ld-elf.so.1 usr/libexec/ld-elf.so.1
