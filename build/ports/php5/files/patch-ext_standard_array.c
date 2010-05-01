--- ext/standard/array.c.orig	2010-01-03 10:23:27.000000000 +0100
+++ ext/standard/array.c	2010-04-30 23:41:31.000000000 +0200
@@ -311,6 +311,7 @@
 PHP_FUNCTION(count)
 {
 	zval *array;
+	zend_class_entry **ce_Countable;
 	long mode = COUNT_NORMAL;
 	
 	if (zend_parse_parameters (ZEND_NUM_ARGS() TSRMLS_CC, "z|l", &array, &mode) == FAILURE)
@@ -324,11 +325,12 @@
 			RETURN_LONG (php_count_recursive (array, mode TSRMLS_CC));
 			break;
 		case IS_OBJECT: {
-#ifdef HAVE_SPL
+
 			/* it the object implements Countable we call its count() method */
 			zval *retval;
 
-			if (Z_OBJ_HT_P(array)->get_class_entry && instanceof_function(Z_OBJCE_P(array), spl_ce_Countable TSRMLS_CC)) {
+			if (zend_lookup_class_ex("Countable", 9, 0, &ce_Countable TSRMLS_CC) != FAILURE) {
+			if (Z_OBJ_HT_P(array)->get_class_entry && instanceof_function(Z_OBJCE_P(array), *ce_Countable TSRMLS_CC)) {
 				zend_call_method_with_0_params(&array, NULL, NULL, "count", &retval);
 				if (retval) {
 					convert_to_long_ex(&retval);
@@ -337,7 +339,7 @@
 				}
 				return;
 			}
-#endif
+		}
 			/* if not we return the number of properties (not taking visibility into account) */
 			if (Z_OBJ_HT_P(array)->count_elements) {
 				RETVAL_LONG(1);
