--- modules/mod_xfer.c.orig	2007-10-09 23:45:03.000000000 +0200
+++ modules/mod_xfer.c	2007-10-10 00:09:12.000000000 +0200
@@ -1703,10 +1703,12 @@
 
   fmode = file_mode(dir);
 
+  if(!fmode) {
+      pr_response_add_err(R_550,"%s: %s",cmd->arg,strerror(errno));
+      return PR_ERROR(cmd);
+  }
+ 
   if (!S_ISREG(fmode)) {
-    if (!fmode)
-      pr_response_add_err(R_550, "%s: %s", cmd->arg, strerror(errno));
-    else
       pr_response_add_err(R_550, _("%s: Not a regular file"), cmd->arg);
     return PR_ERROR(cmd);
   }

