--- ext/standard/image.c.orig	2010-06-30 14:21:36.000000000 +0200
+++ ext/standard/image.c	2010-10-02 22:26:22.000000000 +0200
@@ -31,9 +31,9 @@
 #endif
 #include "php_image.h"
 
-#if HAVE_ZLIB && !defined(COMPILE_DL_ZLIB)
+
 #include "zlib.h"
-#endif
+
 
 /* file type markers */
 PHPAPI const char php_sig_gif[3] = {'G', 'I', 'F'};
@@ -80,9 +80,9 @@
 	REGISTER_LONG_CONSTANT("IMAGETYPE_JP2",     IMAGE_FILETYPE_JP2,     CONST_CS | CONST_PERSISTENT);
 	REGISTER_LONG_CONSTANT("IMAGETYPE_JPX",     IMAGE_FILETYPE_JPX,     CONST_CS | CONST_PERSISTENT);
 	REGISTER_LONG_CONSTANT("IMAGETYPE_JB2",     IMAGE_FILETYPE_JB2,     CONST_CS | CONST_PERSISTENT);
-#if HAVE_ZLIB && !defined(COMPILE_DL_ZLIB)
+
 	REGISTER_LONG_CONSTANT("IMAGETYPE_SWC",     IMAGE_FILETYPE_SWC,     CONST_CS | CONST_PERSISTENT);
-#endif	
+	
 	REGISTER_LONG_CONSTANT("IMAGETYPE_IFF",     IMAGE_FILETYPE_IFF,     CONST_CS | CONST_PERSISTENT);
 	REGISTER_LONG_CONSTANT("IMAGETYPE_WBMP",    IMAGE_FILETYPE_WBMP,    CONST_CS | CONST_PERSISTENT);
 	REGISTER_LONG_CONSTANT("IMAGETYPE_JPEG2000",IMAGE_FILETYPE_JPC,     CONST_CS | CONST_PERSISTENT); /* keep alias */
@@ -184,7 +184,7 @@
 }
 /* }}} */
 
-#if HAVE_ZLIB && !defined(COMPILE_DL_ZLIB)
+
 /* {{{ php_handle_swc
  */
 static struct gfxinfo *php_handle_swc(php_stream * stream TSRMLS_DC)
@@ -255,7 +255,7 @@
 	return result;
 }
 /* }}} */
-#endif
+
 
 /* {{{ php_handle_swf
  */
@@ -1309,11 +1309,11 @@
 			result = php_handle_swf(stream TSRMLS_CC);
 			break;
 		case IMAGE_FILETYPE_SWC:
-#if HAVE_ZLIB && !defined(COMPILE_DL_ZLIB)
+
 			result = php_handle_swc(stream TSRMLS_CC);
-#else
-			php_error_docref(NULL TSRMLS_CC, E_NOTICE, "The image is a compressed SWF file, but you do not have a static version of the zlib extension enabled");
-#endif
+
+
+
 			break;
 		case IMAGE_FILETYPE_PSD:
 			result = php_handle_psd(stream TSRMLS_CC);
