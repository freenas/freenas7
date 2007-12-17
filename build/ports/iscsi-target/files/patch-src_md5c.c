--- md5c.c.orig	Sun Dec  9 09:21:06 2007
+++ md5c.c	Mon Dec 17 13:14:41 2007
@@ -29,9 +29,7 @@
  * documentation and/or software.
  */
 
-#ifdef HAVE_CONFIG_H
 #include <config.h>
-#endif
 
 #if defined(_KERNEL) || defined(_STANDALONE)
 #include <lib/libkern/libkern.h>
