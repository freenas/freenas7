--- m4/nut_check_ipv6.m4.orig	2010-02-23 09:37:41.000000000 +0100
+++ m4/nut_check_ipv6.m4	2010-04-13 05:18:25.000000000 +0200
@@ -12,7 +12,9 @@
 	AC_CHECK_FUNCS([getaddrinfo freeaddrinfo], [nut_have_ipv6=yes], [nut_have_ipv6=no])
 
 	AC_CHECK_TYPES([struct addrinfo],
-		[], [nut_have_ipv6=no], [#include <netdb.h>])
+		[], [nut_have_ipv6=no], [#include <netdb.h>
+					    #include <sys/socket.h>
+					    #include <netinet/in.h>])
 
 	AC_CHECK_TYPES([struct sockaddr_storage],
 		[], [nut_have_ipv6=no], [#include <sys/socket.h>])
