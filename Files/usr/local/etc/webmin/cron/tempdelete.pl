#!/usr/bin/perl
open(CONF, "/usr/local/etc/webmin/miniserv.conf");
while(<CONF>) {
        $root = $1 if (/^root=(.*)/);
        }
close(CONF);
$ENV{'WEBMIN_CONFIG'} = "/usr/local/etc/webmin";
$ENV{'WEBMIN_VAR'} = "/var/log/webmin";
chdir("$root/cron");
exec("$root/cron/tempdelete.pl", @ARGV) || die "Failed to run $root/cron/tempdelete.pl : $!";
