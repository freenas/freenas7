--- disk.c.orig	Tue Sep 25 23:15:00 2007
+++ disk.c	Tue Oct  9 07:39:40 2007
@@ -762,7 +762,7 @@
 	char	block[DEFAULT_TARGET_BLOCK_LEN];
 
 	size = de_getsize(de);
-	if (de_lseek(de, size - 1, SEEK_SET) == -1) {
+	if (de_lseek(de, size - DEFAULT_TARGET_BLOCK_LEN, SEEK_SET) == -1) {
 		iscsi_trace_error(__FILE__, __LINE__, "error seeking \"%s\"\n", filename);
 		return 0;
 	}
