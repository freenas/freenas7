--- Zend/zend_list.c.orig	2010-01-05 20:46:52.000000000 +0100
+++ Zend/zend_list.c	2010-04-30 22:46:12.000000000 +0200
@@ -48,7 +48,7 @@
 	return index;
 }
 
-ZEND_API int _zend_list_delete(int id TSRMLS_DC)
+ZEND_API int _zend_list_delete(ulong id TSRMLS_DC)
 {
 	zend_rsrc_list_entry *le;
 	
@@ -65,7 +65,7 @@
 }
 
 
-ZEND_API void *_zend_list_find(int id, int *type TSRMLS_DC)
+ZEND_API void *_zend_list_find(ulong id, int *type TSRMLS_DC)
 {
 	zend_rsrc_list_entry *le;
 
@@ -78,7 +78,7 @@
 	}
 }
 
-ZEND_API int _zend_list_addref(int id TSRMLS_DC)
+ZEND_API int _zend_list_addref(ulong id TSRMLS_DC)
 {
 	zend_rsrc_list_entry *le;
 	
@@ -349,7 +349,7 @@
 }
 
 
-char *zend_rsrc_list_get_rsrc_type(int resource TSRMLS_DC)
+char *zend_rsrc_list_get_rsrc_type(ulong resource TSRMLS_DC)
 {
 	zend_rsrc_list_dtors_entry *lde;
 	int rsrc_type;
