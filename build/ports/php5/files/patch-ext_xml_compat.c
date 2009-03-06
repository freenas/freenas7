--- ext/xml/compat.c.orig	2007-12-31 08:20:14.000000000 +0100
+++ ext/xml/compat.c	2009-03-06 09:16:45.000000000 +0100
@@ -411,6 +411,10 @@
 	parser->parser->charset = XML_CHAR_ENCODING_NONE;
 #endif
 
+#if LIBXML_VERSION >= 20703
+	xmlCtxtUseOptions(parser->parser, XML_PARSE_OLDSAX);
+#endif
+
 	parser->parser->replaceEntities = 1;
 	parser->parser->wellFormed = 0;
 	if (sep != NULL) {
