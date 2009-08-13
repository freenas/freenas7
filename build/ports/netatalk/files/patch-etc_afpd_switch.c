--- etc/afpd/switch.c.orig	2008-05-14 15:30:52.000000000 +0200
+++ etc/afpd/switch.c	2008-05-14 15:36:00.000000000 +0200
@@ -152,7 +152,7 @@
     NULL, NULL, NULL, NULL,
     NULL, NULL, NULL, NULL,					/*  64 -  71 */
     NULL, NULL, NULL, NULL,
-    NULL, NULL, NULL, NULL,					/*  72 -  79 */
+    NULL, NULL, afp_syncdir, afp_flushfork,				/*  72 -  79 */
     NULL, NULL, NULL, NULL,
     NULL, NULL, NULL, NULL,					/*  80 -  87 */
     NULL, NULL, NULL, NULL,
