--- etc/afpd/auth.c.orig	2008-05-14 15:30:52.000000000 +0200
+++ etc/afpd/auth.c	2008-05-14 15:39:10.000000000 +0200
@@ -76,7 +76,8 @@
             { "AFP2.2",	22 },
 #ifdef AFP3x
             { "AFPX03", 30 },
-            { "AFP3.1", 31 }
+            { "AFP3.1", 31 },
+            { "AFP3.2", 32 }
 #endif            
         };
 
@@ -186,6 +187,7 @@
     else {
         afp_switch = postauth_switch;
         switch (afp_version) {
+        case 32:
         case 31:
 	    uam_afpserver_action(AFP_ENUMERATE_EXT2, UAM_AFPSERVER_POSTAUTH, afp_enumerate_ext2, NULL); 
         case 30:
