--- ./smbd/aio.c.orig	2009-04-01 11:48:54.000000000 +0000
+++ ./smbd/aio.c	2009-04-07 01:39:14.000000000 +0000
@@ -24,9 +24,6 @@
 
 /* The signal we'll use to signify aio done. */
 #ifndef RT_SIGNAL_AIO
-#ifndef SIGRTMIN
-#define SIGRTMIN	NSIG
-#endif
 #define RT_SIGNAL_AIO	(SIGRTMIN+3)
 #endif
 
