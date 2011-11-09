--- ./source3/modules/vfs_shadow_copy2.c.orig	2011-10-19 03:48:48.000000000 +0900
+++ ./source3/modules/vfs_shadow_copy2.c	2011-11-09 18:48:16.000000000 +0900
@@ -237,7 +237,7 @@
 	if (shadow_copy2_match_name(fname, &gmt_start)) {	\
 		char *name2; \
 		rtype ret; \
-		name2 = convert_shadow2_name(handle, fname, gmt_start);	\
+		name2 = convert_shadow2_name(handle, fname, gmt_start, True);	\
 		if (name2 == NULL) { \
 			errno = EINVAL; \
 			return eret; \
@@ -258,7 +258,7 @@
 		char *name2; \
 		char *smb_base_name_tmp = NULL; \
 		rtype ret; \
-		name2 = convert_shadow2_name(handle, smb_fname->base_name, gmt_start); \
+		name2 = convert_shadow2_name(handle, smb_fname->base_name, gmt_start, True); \
 		if (name2 == NULL) { \
 			errno = EINVAL; \
 			return eret; \
@@ -285,7 +285,7 @@
         if (shadow_copy2_match_name(fname, &gmt_start)) {	\
                 char *name2; \
                 NTSTATUS ret; \
-                name2 = convert_shadow2_name(handle, fname, gmt_start);	\
+                name2 = convert_shadow2_name(handle, fname, gmt_start, True);	\
                 if (name2 == NULL) { \
                         errno = EINVAL; \
                         return eret; \
@@ -409,7 +409,8 @@
   convert a filename from a share relative path, to a path in the
   snapshot directory
  */
-static char *convert_shadow2_name(vfs_handle_struct *handle, const char *fname, const char *gmt_path)
+static char *convert_shadow2_name(vfs_handle_struct *handle, const char *fname,
+				  const char *gmt_path, const bool incl_rel)
 {
 	TALLOC_CTX *tmp_ctx = talloc_new(handle->data);
 	const char *snapdir, *relpath, *baseoffset, *basedir;
@@ -486,11 +487,13 @@
 	if (*relpath == '/') relpath++;
 	if (*baseoffset == '/') baseoffset++;
 
-	ret = talloc_asprintf(handle->data, "%s/%s/%s/%s",
+	ret = talloc_asprintf(handle->data, "%s/%s%s%s%s%s",
 			      snapdir, 
 			      snapshot,
+			      *baseoffset ? "/" : "",
 			      baseoffset, 
-			      relpath);
+			      *relpath ? "/" : "",
+			      incl_rel ? relpath : "");
 	DEBUG(6,("convert_shadow2_name: '%s' -> '%s'\n", fname, ret));
 	talloc_free(tmp_ctx);
 	return ret;
@@ -687,68 +690,17 @@
 static const char *shadow_copy2_connectpath(struct vfs_handle_struct *handle,
 					    const char *fname)
 {
-	TALLOC_CTX *tmp_ctx;
-	const char *snapdir, *baseoffset, *basedir, *gmt_start;
-	size_t baselen;
+	const char *gmt_start;
 	char *ret;
 
 	DEBUG(10, ("shadow_copy2_connectpath called with %s\n", fname));
 
 	if (!shadow_copy2_match_name(fname, &gmt_start)) {
-		return handle->conn->connectpath;
-	}
-
-        /*
-         * We have to create a real temporary context because we have
-         * to put our result on talloc_tos(). Thus we can't use a
-         * talloc_stackframe() here.
-         */
-	tmp_ctx = talloc_new(talloc_tos());
-
-	fname = shadow_copy2_normalise_path(tmp_ctx, fname, gmt_start);
-	if (fname == NULL) {
-		TALLOC_FREE(tmp_ctx);
-		return NULL;
-	}
-
-	snapdir = shadow_copy2_find_snapdir(tmp_ctx, handle);
-	if (snapdir == NULL) {
-		DEBUG(2,("no snapdir found for share at %s\n",
-			 handle->conn->connectpath));
-		TALLOC_FREE(tmp_ctx);
-		return NULL;
+		return SMB_VFS_NEXT_CONNECTPATH(handle, fname);
 	}
 
-	basedir = shadow_copy2_find_basedir(tmp_ctx, handle);
-	if (basedir == NULL) {
-		DEBUG(2,("no basedir found for share at %s\n",
-			 handle->conn->connectpath));
-		TALLOC_FREE(tmp_ctx);
-		return NULL;
-	}
-
-	baselen = strlen(basedir);
-	baseoffset = handle->conn->connectpath + baselen;
-
-	/* some sanity checks */
-	if (strncmp(basedir, handle->conn->connectpath, baselen) != 0 ||
-	    (handle->conn->connectpath[baselen] != 0
-	     && handle->conn->connectpath[baselen] != '/')) {
-		DEBUG(0,("shadow_copy2_connectpath: basedir %s is not a "
-			 "parent of %s\n", basedir,
-			 handle->conn->connectpath));
-		TALLOC_FREE(tmp_ctx);
-		return NULL;
-	}
-
-	if (*baseoffset == '/') baseoffset++;
-
-	ret = talloc_asprintf(talloc_tos(), "%s/%.*s/%s",
-			      snapdir,
-			      GMT_NAME_LEN, fname,
-			      baseoffset);
+	ret = convert_shadow2_name(handle, fname, gmt_start, False);
 	DEBUG(6,("shadow_copy2_connectpath: '%s' -> '%s'\n", fname, ret));
-	TALLOC_FREE(tmp_ctx);
 	return ret;
 }
 
