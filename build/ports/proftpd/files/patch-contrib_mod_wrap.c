--- contrib/mod_wrap.c.orig	2004-10-31 01:16:41.000000000 +0200
+++ contrib/mod_wrap.c	2006-05-10 22:05:53.000000000 +0200
@@ -888,8 +888,7 @@
 
   fromhost(&request);
 
-  if (STR_EQ(eval_hostname(request.client), paranoid) ||
-      !hosts_access(&request)) {
+  if (!hosts_access(&request)) {
     char *denymsg = NULL;
 
     /* log the denied connection */

