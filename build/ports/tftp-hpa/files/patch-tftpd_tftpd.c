
$FreeBSD: ports/ftp/tftp-hpa/files/patch-tftpd_tftpd.c,v 1.3 2007/12/28 22:11:54 brooks Exp $

--- tftpd/tftpd.c.orig
+++ tftpd/tftpd.c
@@ -302,6 +302,9 @@
 
   p = strrchr(argv[0], '/');
   __progname = (p && p[1]) ? p+1 : argv[0];
+
+  /* syslog in localtime */
+  tzset();
   
   openlog(__progname, LOG_PID|LOG_NDELAY, LOG_DAEMON);
 
