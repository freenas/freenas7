--- libtransmission/torrent.c.orig	2011-03-05 03:02:16.000000000 +0100
+++ libtransmission/torrent.c	2011-04-03 03:27:15.000000000 +0200
@@ -27,7 +27,7 @@
 #include <stdarg.h>
 #include <string.h> /* memcmp */
 #include <stdlib.h> /* qsort */
-
+#include <signal.h> /* signal() */
 #include <event2/util.h> /* evutil_vsnprintf() */
 
 #include "transmission.h"
