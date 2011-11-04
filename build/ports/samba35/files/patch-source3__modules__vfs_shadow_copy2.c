--- ./source3/modules/vfs_shadow_copy2.c.orig	2011-08-04 03:24:05.000000000 +0900
+++ ./source3/modules/vfs_shadow_copy2.c	2011-11-03 04:30:27.000000000 +0900
@@ -2,6 +2,7 @@
  * implementation of an Shadow Copy module - version 2
  *
  * Copyright (C) Andrew Tridgell     2007
+ * Copyright (C) Ed Plese            2009
  *
  * This program is free software; you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
@@ -34,6 +35,16 @@
      from the original. This allows the 'restore' button to work
      without a sharing violation
 
+     3) vanity naming for snapshots. Snapshots can be named in any
+     format compatible with str[fp]time conversions.
+
+     4) time stamps in snapshot names can be represented in localtime
+     rather than UTC.
+
+     5) shadow copy results can be sorted before being sent to the
+     client.  This is beneficial for filesystems that don't read
+     directories alphabetically (e.g. ZFS).
+
   Module options:
 
       shadow:snapdir = <directory where snapshots are kept>
@@ -58,10 +69,29 @@
       don't set this option then the 'restore' button in the shadow
       copy UI will fail with a sharing violation.
 
-  Note that the directory names in the snapshot directory must take the form
-  @GMT-YYYY.MM.DD-HH.MM.SS
-  
-  The following command would generate a correctly formatted directory name:
+      shadow:sort = asc/desc, or blank for unsorted (default)
+
+      This is an optional parameter that specifies that the shadow
+      copy directories should be sorted before sending them to the
+      client.  This is beneficial for filesystems that don't read
+      directories alphabetically (e.g. ZFS).  If enabled, you typically
+      want to specify descending order.
+
+      shadow:format = <format specification for snapshot names>
+
+      This is an optional parameter that specifies the format
+      specification for the naming of snapshots.  The format must
+      be compatible with the conversion specifications recognized
+      by str[fp]time.  The default value is "@GMT-%Y.%m.%d-%H.%M.%S".
+
+      shadow:localtime = yes/no (default is no)
+
+      This is an optional parameter that indicates whether the
+      snapshot names are in UTC/GMT or the local time.
+      
+
+  The following command would generate a correctly formatted directory name
+  for use with the default parameters:
      date -u +@GMT-%Y.%m.%d-%H.%M.%S
   
  */
@@ -72,6 +102,11 @@
 #define DBGC_CLASS vfs_shadow_copy2_debug_level
 
 #define GMT_NAME_LEN 24 /* length of a @GMT- name */
+#define SHADOW_COPY2_GMT_FORMAT "@GMT-%Y.%m.%d-%H.%M.%S"
+
+#define SHADOW_COPY2_DEFAULT_SORT ""
+#define SHADOW_COPY2_DEFAULT_LOCALTIME (False)
+#define SHADOW_COPY2_DEFAULT_FORMAT "@GMT-%Y.%m.%d-%H.%M.%S"
 
 /*
   make very sure it is one of our special names 
@@ -158,6 +193,40 @@
 	return pcopy;
 }
 
+static char *shadow_copy2_snapshot_to_gmt(TALLOC_CTX *mem_ctx, vfs_handle_struct *handle, const char *name)
+{
+	struct tm timestamp;
+	time_t timestamp_t;
+	char gmt[GMT_NAME_LEN + 1];
+
+	char *fmt = talloc_strdup(mem_ctx,
+		lp_parm_const_string(SNUM(handle->conn),
+			"shadow", "format", SHADOW_COPY2_DEFAULT_FORMAT));
+
+	if (fmt == NULL) {
+		DEBUG(0, ("shadow_copy2_snapshot_to_gmt: talloc_strdup failed for format\n"));
+		return NULL;
+	}
+
+	memset(&timestamp, 0, sizeof(timestamp));
+	if (strptime(name, fmt, &timestamp) == NULL) {
+		DEBUG(10, ("shadow_copy2_snapshot_to_gmt: no match %s: %s\n", fmt, name));
+		talloc_free(fmt);
+		return NULL;
+	}
+
+	DEBUG(10, ("shadow_copy2_snapshot_to_gmt: match %s: %s\n", fmt, name));
+	if (lp_parm_bool(SNUM(handle->conn), "shadow", "localtime", SHADOW_COPY2_DEFAULT_LOCALTIME)) {
+		timestamp.tm_isdst = -1;
+		timestamp_t = mktime(&timestamp);
+		gmtime_r(&timestamp_t, &timestamp);
+	}
+	strftime(gmt, sizeof(gmt), SHADOW_COPY2_GMT_FORMAT, &timestamp);
+	talloc_free(fmt);
+
+	return talloc_strdup(mem_ctx, gmt);
+}
+
 /*
   convert a name to the shadow directory
  */
@@ -347,6 +416,19 @@
 	size_t baselen;
 	char *ret;
 
+	struct tm timestamp;
+	time_t timestamp_t;
+	char snapshot[MAXPATHLEN];
+	char *fmt = talloc_strdup(tmp_ctx,
+		lp_parm_const_string(SNUM(handle->conn),
+			"shadow", "format", SHADOW_COPY2_DEFAULT_FORMAT));
+
+	if (fmt == NULL) {
+		DEBUG(0, ("shadow_copy2_convert: talloc_strdup failed for format\n"));
+		talloc_free(tmp_ctx);
+		return NULL;
+	}
+
 	snapdir = shadow_copy2_find_snapdir(tmp_ctx, handle);
 	if (snapdir == NULL) {
 		DEBUG(2,("no snapdir found for share at %s\n", handle->conn->connectpath));
@@ -369,7 +451,22 @@
 		}
 	}
 
-	relpath = fname + GMT_NAME_LEN;
+	memset(&timestamp, 0, sizeof(timestamp));
+	relpath = strptime(fname, SHADOW_COPY2_GMT_FORMAT, &timestamp);
+	if (relpath == NULL) {
+		talloc_free(tmp_ctx);
+		return NULL;
+	}
+
+	/* relpath is the remaining portion of the path after the @GMT-xxx */
+
+	if (lp_parm_bool(SNUM(handle->conn), "shadow", "localtime", SHADOW_COPY2_DEFAULT_LOCALTIME)) {
+		timestamp_t = timegm(&timestamp);
+		localtime_r(&timestamp_t, &timestamp);
+	}
+
+	strftime(snapshot, MAXPATHLEN, fmt, &timestamp);
+
 	baselen = strlen(basedir);
 	baseoffset = handle->conn->connectpath + baselen;
 
@@ -385,9 +482,10 @@
 	if (*relpath == '/') relpath++;
 	if (*baseoffset == '/') baseoffset++;
 
-	ret = talloc_asprintf(handle->data, "%s/%.*s/%s/%s", 
+	ret = talloc_asprintf(handle->data, "%s/%s%s%s/%s", 
 			      snapdir, 
-			      GMT_NAME_LEN, fname, 
+			      snapshot,
+			      *baseoffset ? "/" : "",
 			      baseoffset, 
 			      relpath);
 	DEBUG(6,("convert_shadow2_name: '%s' -> '%s'\n", fname, ret));
@@ -559,6 +657,7 @@
 {
 	const char *gmt;
 
+	DEBUG(10,("shadow_copy2_realpath [%s]\n", fname));
 	if (shadow_copy2_match_name(fname, &gmt)
 	    && (gmt[GMT_NAME_LEN] == '\0')) {
 		char *copy, *result;
@@ -570,11 +669,17 @@
 		}
 
 		copy[gmt - fname] = '.';
+		copy[gmt - fname + 1] = '\0';
 
+#if 0
 		DEBUG(10, ("calling NEXT_REALPATH with %s\n", copy));
 		result = SMB_VFS_NEXT_REALPATH(handle, copy, resolved_path);
 		TALLOC_FREE(copy);
 		return result;
+#else
+		DEBUG(10, ("calling NEXT_REALPATH with %s\n", copy));
+		SHADOW2_NEXT(REALPATH, (handle, name, resolved_path), char *, NULL);
+#endif
 	}
         SHADOW2_NEXT(REALPATH, (handle, name, resolved_path), char *, NULL);
 }
@@ -606,6 +711,7 @@
 		return NULL;
 	}
 
+#if 0
 	snapdir = shadow_copy2_find_snapdir(tmp_ctx, handle);
 	if (snapdir == NULL) {
 		DEBUG(2,("no snapdir found for share at %s\n",
@@ -642,6 +748,14 @@
 			      snapdir,
 			      GMT_NAME_LEN, fname,
 			      baseoffset);
+#else
+	ret = convert_shadow2_name(handle, fname, gmt_start);
+	/* remove last slash if any */
+	if (strlen(fname) > 0 && fname[strlen(fname) - 1] != '/' &&
+	    ret != NULL && strlen(ret) > 0 && ret[strlen(ret) - 1] == '/') {
+		ret[strlen(ret) - 1] = '\0';
+	}
+#endif
 	DEBUG(6,("shadow_copy2_connectpath: '%s' -> '%s'\n", fname, ret));
 	TALLOC_FREE(tmp_ctx);
 	return ret;
@@ -718,6 +832,45 @@
         SHADOW2_NEXT(CHMOD_ACL, (handle, name, mode), int, -1);
 }
 
+static int shadow_copy2_label_cmp_asc(const void *x, const void *y)
+{
+	return strncmp((char *)x, (char *)y, sizeof(SHADOW_COPY_LABEL));
+}
+
+static int shadow_copy2_label_cmp_desc(const void *x, const void *y)
+{
+	return -strncmp((char *)x, (char *)y, sizeof(SHADOW_COPY_LABEL));
+}
+
+/*
+  sort the shadow copy data in ascending or descending order
+ */
+static void shadow_copy2_sort_data(vfs_handle_struct *handle,
+				   SHADOW_COPY_DATA *shadow_copy2_data)
+{
+	const char *tmp_str = lp_parm_const_string(SNUM(handle->conn),
+		"shadow", "sort", SHADOW_COPY2_DEFAULT_SORT);
+
+	if (tmp_str && shadow_copy2_data &&
+		shadow_copy2_data->num_volumes > 0 &&
+		shadow_copy2_data->labels) {
+
+		if (strcmp(tmp_str, "asc") == 0) {
+			qsort(shadow_copy2_data->labels,
+				shadow_copy2_data->num_volumes,
+				sizeof(SHADOW_COPY_LABEL),
+				shadow_copy2_label_cmp_asc);
+		} else if (strcmp(tmp_str, "desc") == 0) {
+			qsort(shadow_copy2_data->labels,
+				shadow_copy2_data->num_volumes,
+				sizeof(SHADOW_COPY_LABEL),
+				shadow_copy2_label_cmp_desc);
+		}
+	}
+
+	return;
+}
+
 static int shadow_copy2_get_shadow_copy2_data(vfs_handle_struct *handle, 
 					      files_struct *fsp, 
 					      SHADOW_COPY_DATA *shadow_copy2_data, 
@@ -727,6 +880,7 @@
 	const char *snapdir;
 	SMB_STRUCT_DIRENT *d;
 	TALLOC_CTX *tmp_ctx = talloc_new(handle->data);
+	char *snapshot;
 
 	snapdir = shadow_copy2_find_snapdir(tmp_ctx, handle);
 	if (snapdir == NULL) {
@@ -747,8 +901,6 @@
 		return -1;
 	}
 
-	talloc_free(tmp_ctx);
-
 	shadow_copy2_data->num_volumes = 0;
 	shadow_copy2_data->labels      = NULL;
 
@@ -756,7 +908,9 @@
 		SHADOW_COPY_LABEL *tlabels;
 
 		/* ignore names not of the right form in the snapshot directory */
-		if (!shadow_copy2_match_name(d->d_name, NULL)) {
+		snapshot = shadow_copy2_snapshot_to_gmt(tmp_ctx, handle, d->d_name);
+		DEBUG(6,("shadow_copy2_get_shadow_copy2_data: %s -> %s\n", d->d_name, snapshot));
+		if (!snapshot) {
 			continue;
 		}
 
@@ -772,15 +926,22 @@
 		if (tlabels == NULL) {
 			DEBUG(0,("shadow_copy2: out of memory\n"));
 			SMB_VFS_NEXT_CLOSEDIR(handle, p);
+			talloc_free(tmp_ctx);
 			return -1;
 		}
 
-		strlcpy(tlabels[shadow_copy2_data->num_volumes], d->d_name, sizeof(*tlabels));
+		strlcpy(tlabels[shadow_copy2_data->num_volumes], snapshot, sizeof(*tlabels));
+		talloc_free(snapshot);
+
 		shadow_copy2_data->num_volumes++;
 		shadow_copy2_data->labels = tlabels;
 	}
 
 	SMB_VFS_NEXT_CLOSEDIR(handle,p);
+
+	shadow_copy2_sort_data(handle, shadow_copy2_data);
+
+	talloc_free(tmp_ctx);
 	return 0;
 }
 
