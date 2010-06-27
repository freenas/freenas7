--- ./nsswitch/pam_winbind.c.orig	2010-05-17 20:51:23.000000000 +0900
+++ ./nsswitch/pam_winbind.c	2010-06-13 11:28:03.605626000 +0900
@@ -173,14 +173,20 @@
 			 const void *_item)
 {
 	const void **item = (const void **)_item;
-	return pam_get_item(pamh, item_type, item);
+	return pam_get_item((pam_handle_t *)pamh, item_type, item);
 }
+
 static int _pam_get_data(const pam_handle_t *pamh,
 			 const char *module_data_name,
 			 const void *_data)
 {
+#if PAM_GET_DATA_ARG3_CONST_VOID_PP
 	const void **data = (const void **)_data;
-	return pam_get_data(pamh, module_data_name, data);
+	return pam_get_data((pam_handle_t *)pamh, module_data_name, data);
+#else
+	void **data = (void **)_data;
+	return pam_get_data((pam_handle_t *)pamh, module_data_name, data);
+#endif
 }
 
 /* some syslogging */
@@ -311,7 +317,7 @@
 	if (item_type != 0) {
 		pam_get_item(ctx->pamh, item_type, &data);
 	} else {
-		pam_get_data(ctx->pamh, key, &data);
+		_pam_get_data(ctx->pamh, key, &data);
 	}
 	if (data != NULL) {
 		const char *type = (item_type != 0) ? "ITEM" : "DATA";
@@ -1434,12 +1440,12 @@
 static bool _pam_check_remark_auth_err(struct pwb_context *ctx,
 				       const struct wbcAuthErrorInfo *e,
 				       const char *nt_status_string,
-				       int *pam_error)
+				       int *pam_err)
 {
 	const char *ntstatus = NULL;
 	const char *error_string = NULL;
 
-	if (!e || !pam_error) {
+	if (!e || !pam_err) {
 		return false;
 	}
 
@@ -1453,18 +1459,18 @@
 		error_string = _get_ntstatus_error_string(nt_status_string);
 		if (error_string) {
 			_make_remark(ctx, PAM_ERROR_MSG, error_string);
-			*pam_error = e->pam_error;
+			*pam_err = e->pam_error;
 			return true;
 		}
 
 		if (e->display_string) {
 			_make_remark(ctx, PAM_ERROR_MSG, e->display_string);
-			*pam_error = e->pam_error;
+			*pam_err = e->pam_error;
 			return true;
 		}
 
 		_make_remark(ctx, PAM_ERROR_MSG, nt_status_string);
-		*pam_error = e->pam_error;
+		*pam_err = e->pam_error;
 
 		return true;
 	}
@@ -2848,8 +2854,8 @@
 		ret = PAM_USER_UNKNOWN;
 		goto out;
 	case 0:
-		pam_get_data(pamh, PAM_WINBIND_NEW_AUTHTOK_REQD,
-			     (const void **)&tmp);
+		_pam_get_data(pamh, PAM_WINBIND_NEW_AUTHTOK_REQD,
+			      (const void **)&tmp);
 		if (tmp != NULL) {
 			ret = atoi((const char *)tmp);
 			switch (ret) {
