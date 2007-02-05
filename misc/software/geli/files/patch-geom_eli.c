--- ./geom_eli.c.orig	Mon Feb  5 08:45:32 2007
+++ ./geom_eli.c	Mon Feb  5 08:45:23 2007
@@ -61,6 +61,7 @@
 static intmax_t iterations = -1;
 static intmax_t sectorsize = 0;
 static char keyfile[] = "", newkeyfile[] = "";
+static char password[] = "";
 
 static void eli_main(struct gctl_req *req, unsigned flags);
 static void eli_init(struct gctl_req *req);
@@ -77,9 +78,9 @@
 /*
  * Available commands:
  *
- * init [-bhPv] [-a aalgo] [-e ealgo] [-i iterations] [-l keylen] [-K newkeyfile] prov
+ * init [-bhPv] [-a aalgo] [-e ealgo] [-i iterations] [-l keylen] [-K newkeyfile] [-X password] prov
  * label - alias for 'init'
- * attach [-dprv] [-k keyfile] prov
+ * attach [-dprv] [-k keyfile] [-X password] prov
  * detach [-fl] prov ...
  * stop - alias for 'detach'
  * onetime [-d] [-a aalgo] [-e ealgo] [-l keylen] prov ...
@@ -103,9 +104,10 @@
 		{ 'l', "keylen", &keylen, G_TYPE_NUMBER },
 		{ 'P', "nonewpassphrase", NULL, G_TYPE_NONE },
 		{ 's', "sectorsize", &sectorsize, G_TYPE_NUMBER },
+		{ 'X', "password", password, G_TYPE_STRING },
 		G_OPT_SENTINEL
 	    },
-	    "[-bPv] [-a aalgo] [-e ealgo] [-i iterations] [-l keylen] [-K newkeyfile] [-s sectorsize] prov"
+	    "[-bPv] [-a aalgo] [-e ealgo] [-i iterations] [-l keylen] [-K newkeyfile] [-s sectorsize] [-X password] prov"
 	},
 	{ "label", G_FLAG_VERBOSE, eli_main,
 	    {
@@ -127,9 +129,10 @@
 		{ 'k', "keyfile", keyfile, G_TYPE_STRING },
 		{ 'p', "nopassphrase", NULL, G_TYPE_NONE },
 		{ 'r', "readonly", NULL, G_TYPE_NONE },
+		{ 'X', "password", password, G_TYPE_STRING },
 		G_OPT_SENTINEL
 	    },
-	    "[-dprv] [-k keyfile] prov"
+	    "[-dprv] [-k keyfile] [-X password] prov"
 	},
 	{ "detach", 0, NULL,
 	    {
@@ -358,37 +361,43 @@
 			gctl_error(req, "Missing -p flag.");
 			return (NULL);
 		}
-		for (;;) {
-			p = readpassphrase(
-			    new ? "Enter new passphrase:" : "Enter passphrase:",
-			    buf1, sizeof(buf1), RPP_ECHO_OFF | RPP_REQUIRE_TTY);
-			if (p == NULL) {
-				bzero(buf1, sizeof(buf1));
-				gctl_error(req, "Cannot read passphrase: %s.",
-				    strerror(errno));
-				return (NULL);
-			}
-	
-			if (new) {
-				p = readpassphrase("Reenter new passphrase: ",
-				    buf2, sizeof(buf2),
-				    RPP_ECHO_OFF | RPP_REQUIRE_TTY);
+
+		const char *passwd = gctl_get_ascii(req, "password");
+		if( 0 != strlen(passwd)) {
+			strlcpy(buf1, passwd, BUFSIZ);
+		} else {
+			for (;;) {
+				p = readpassphrase(
+				    new ? "Enter new passphrase:" : "Enter passphrase:",
+				    buf1, sizeof(buf1), RPP_ECHO_OFF | RPP_REQUIRE_TTY);
 				if (p == NULL) {
 					bzero(buf1, sizeof(buf1));
-					gctl_error(req,
-					    "Cannot read passphrase: %s.",
+					gctl_error(req, "Cannot read passphrase: %s.",
 					    strerror(errno));
 					return (NULL);
 				}
-	
-				if (strcmp(buf1, buf2) != 0) {
+		
+				if (new) {
+					p = readpassphrase("Reenter new passphrase: ",
+					    buf2, sizeof(buf2),
+					    RPP_ECHO_OFF | RPP_REQUIRE_TTY);
+					if (p == NULL) {
+						bzero(buf1, sizeof(buf1));
+						gctl_error(req,
+						    "Cannot read passphrase: %s.",
+						    strerror(errno));
+						return (NULL);
+					}
+		
+					if (strcmp(buf1, buf2) != 0) {
+						bzero(buf2, sizeof(buf2));
+						fprintf(stderr, "They didn't match.\n");
+						continue;
+					}
 					bzero(buf2, sizeof(buf2));
-					fprintf(stderr, "They didn't match.\n");
-					continue;
 				}
-				bzero(buf2, sizeof(buf2));
+				break;
 			}
-			break;
 		}
 		/*
 		 * Field md_iterations equal to -1 means "choose some sane
