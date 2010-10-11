--- contrib/mod_wrap.c.orig	2009-12-10 18:59:14.000000000 +0100
+++ contrib/mod_wrap.c	2010-10-10 20:47:09.000000000 +0200
@@ -922,8 +922,8 @@
 
   fromhost(&request);
 
-  if (STR_EQ(eval_hostname(request.client), paranoid) ||
-      !hosts_access(&request)) {
+
+      (!hosts_access(&request)) {
     char *denymsg = NULL;
 
     /* log the denied connection */
