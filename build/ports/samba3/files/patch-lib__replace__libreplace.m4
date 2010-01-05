--- lib/replace/libreplace.m4.orig	2009-09-30 21:21:56.000000000 +0900
+++ lib/replace/libreplace.m4	2009-12-30 10:10:02.000000000 +0900
@@ -98,7 +98,11 @@
 AC_CHECK_HEADERS(stdarg.h vararg.h)
 AC_CHECK_HEADERS(sys/socket.h netinet/in.h netdb.h arpa/inet.h)
 AC_CHECK_HEADERS(netinet/in_systm.h)
-AC_CHECK_HEADERS([netinet/ip.h], [], [], [#ifdef HAVE_NETINET_IN_H
+AC_CHECK_HEADERS([netinet/ip.h], [], [], [
+#ifdef HAVE_SYS_TYPES_H
+#include <sys/types.h>
+#endif
+#ifdef HAVE_NETINET_IN_H
 #include <netinet/in.h>
 #endif
 #ifdef HAVE_NETINET_IN_SYSTM_H
@@ -339,7 +343,6 @@
 m4_include(strptime.m4)
 m4_include(win32.m4)
 m4_include(timegm.m4)
-m4_include(repdir.m4)
 
 AC_CHECK_FUNCS([syslog memset memcpy],,[AC_MSG_ERROR([Required function not found])])
 
