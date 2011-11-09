--- /dev/null	2011-11-09 19:28:39.000000000 +0900
+++ ./source3/modules/vfs_shadow_copy3.c	2011-11-09 19:28:37.000000000 +0900
@@ -0,0 +1,1455 @@
+/*
+ * Third attempt at a shadow copy module
+ *
+ * Copyright (C) Andrew Tridgell 2007 (portions taken from shadow_copy2)
+ * Copyright (C) Ed Plese        2009
+ * Copyright (C) Volker Lendecke 2011
+ *
+ * This program is free software; you can redistribute it and/or modify
+ * it under the terms of the GNU General Public License as published by
+ * the Free Software Foundation; either version 2 of the License, or
+ * (at your option) any later version.
+ *
+ * This program is distributed in the hope that it will be useful,
+ * but WITHOUT ANY WARRANTY; without even the implied warranty of
+ * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
+ * GNU General Public License for more details.
+ *
+ * You should have received a copy of the GNU General Public License
+ * along with this program; if not, write to the Free Software
+ * Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.
+ */
+
+#include "includes.h"
+#include "system/filesys.h"
+#include "include/ntioctl.h"
+#include "smbd/proto.h"
+#include "util_tdb.h"
+
+#define GMT_NAME_LEN 24 /* length of a @GMT- name */
+#define GMT_FORMAT "@GMT-%Y.%m.%d-%H.%M.%S"
+
+static bool shadow_copy3_find_slashes(TALLOC_CTX *mem_ctx, const char *str,
+				      size_t **poffsets,
+				      unsigned *pnum_offsets)
+{
+	unsigned num_offsets;
+	size_t *offsets;
+	const char *p;
+
+	num_offsets = 0;
+
+	p = str;
+	while ((p = strchr(p, '/')) != NULL) {
+		num_offsets += 1;
+		p += 1;
+	}
+
+	offsets = talloc_array(mem_ctx, size_t, num_offsets);
+	if (offsets == NULL) {
+		return false;
+	}
+
+	p = str;
+	num_offsets = 0;
+	while ((p = strchr(p, '/')) != NULL) {
+		offsets[num_offsets] = p-str;
+		num_offsets += 1;
+		p += 1;
+	}
+
+	*poffsets = offsets;
+	*pnum_offsets = num_offsets;
+	return true;
+}
+
+static char *shadow_copy3_insert_string(TALLOC_CTX *mem_ctx,
+					struct vfs_handle_struct *handle,
+					time_t snapshot)
+{
+	struct tm snap_tm;
+	char gmt[NAME_MAX+1];
+	size_t gmt_len;
+
+	if (localtime_r(&snapshot, &snap_tm) == 0) {
+		DEBUG(10, ("gmtime_r failed\n"));
+		return NULL;
+	}
+	gmt_len = strftime(gmt, sizeof(gmt),
+			   lp_parm_const_string(SNUM(handle->conn), "shadow",
+						"format", GMT_FORMAT),
+			   &snap_tm);
+	if (gmt_len == 0) {
+		DEBUG(10, ("strftime failed\n"));
+		return NULL;
+	}
+	return talloc_asprintf(talloc_tos(), "/%s/%s",
+			       lp_parm_const_string(
+				       SNUM(handle->conn), "shadow", "snapdir",
+				       ".snapshots"),
+			       gmt);
+}
+
+static bool shadow_copy3_strip_snapshot(TALLOC_CTX *mem_ctx,
+					struct vfs_handle_struct *handle,
+					const char *name,
+					time_t *ptimestamp,
+					char **pstripped)
+{
+	struct tm tm;
+	time_t timestamp;
+	const char *p;
+	char *q;
+	char *stripped;
+	size_t rest_len, dst_len;
+
+	p = strstr_m(name, "@GMT-");
+	if (p == NULL) {
+		goto no_snapshot;
+	}
+	if ((p > name) && (p[-1] != '/')) {
+		goto no_snapshot;
+	}
+	q = strptime(p, GMT_FORMAT, &tm);
+	if (q == NULL) {
+		goto no_snapshot;
+	}
+	tm.tm_isdst = -1;
+	timestamp = mktime(&tm);
+	if (timestamp == (time_t)-1) {
+		goto no_snapshot;
+	}
+	if ((p == name) && (q[0] == '\0')) {
+		if (pstripped != NULL) {
+			stripped = talloc_strdup(mem_ctx, "");
+			if (stripped == NULL) {
+				return false;
+			}
+			*pstripped = stripped;
+		}
+		*ptimestamp = timestamp;
+		return true;
+	}
+	if (q[0] != '/') {
+		goto no_snapshot;
+	}
+	q += 1;
+
+	rest_len = strlen(q);
+	dst_len = (p-name) + rest_len;
+
+	if (lp_parm_bool(SNUM(handle->conn), "shadow", "snapdirseverywhere",
+			 false)) {
+		char *insert;
+		bool have_insert;
+		insert = shadow_copy3_insert_string(talloc_tos(), handle,
+						    timestamp);
+		if (insert == NULL) {
+			errno = ENOMEM;
+			return false;
+		}
+
+		have_insert = (strstr(name, insert+1) != NULL);
+		TALLOC_FREE(insert);
+		if (have_insert) {
+			goto no_snapshot;
+		}
+		
+	}
+
+	if (pstripped != NULL) {
+		stripped = talloc_array(mem_ctx, char, dst_len+1);
+		if (stripped == NULL) {
+			errno = ENOMEM;
+			return false;
+		}
+		if (p > name) {
+			memcpy(stripped, name, p-name);
+		}
+		if (rest_len > 0) {
+			memcpy(stripped + (p-name), q, rest_len);
+		}
+		stripped[dst_len] = '\0';
+		*pstripped = stripped;
+	}
+	*ptimestamp = timestamp;
+	return true;
+no_snapshot:
+	*ptimestamp = 0;
+	return true;
+}
+
+static char *shadow_copy3_find_mount_point(TALLOC_CTX *mem_ctx,
+					   vfs_handle_struct *handle)
+{
+	char *path = talloc_strdup(mem_ctx, handle->conn->connectpath);
+	dev_t dev;
+	struct stat st;
+	char *p;
+
+	if (stat(path, &st) != 0) {
+		talloc_free(path);
+		return NULL;
+	}
+
+	dev = st.st_dev;
+
+	while ((p = strrchr(path, '/')) && p > path) {
+		*p = 0;
+		if (stat(path, &st) != 0) {
+			talloc_free(path);
+			return NULL;
+		}
+		if (st.st_dev != dev) {
+			*p = '/';
+			break;
+		}
+	}
+
+	return path;	
+}
+
+static char *shadow_copy3_convert(TALLOC_CTX *mem_ctx,
+				  struct vfs_handle_struct *handle,
+				  const char *name, time_t timestamp)
+{
+	struct smb_filename converted_fname;
+	char *result = NULL;
+	size_t *slashes = NULL;
+	unsigned num_slashes;
+	char *path = NULL;
+	size_t pathlen;
+	char *insert = NULL;
+	char *converted = NULL;
+	size_t insertlen;
+	int i, saved_errno;
+	size_t min_offset;
+
+	path = talloc_asprintf(mem_ctx, "%s/%s", handle->conn->connectpath,
+			       name);
+	if (path == NULL) {
+		errno = ENOMEM;
+		goto fail;
+	}
+	pathlen = talloc_get_size(path)-1;
+
+	DEBUG(10, ("converting %s\n", path));
+
+	if (!shadow_copy3_find_slashes(talloc_tos(), path,
+				       &slashes, &num_slashes)) {
+		goto fail;
+	}
+	insert = shadow_copy3_insert_string(talloc_tos(), handle, timestamp);
+	if (insert == NULL) {
+		goto fail;
+	}
+	insertlen = talloc_get_size(insert)-1;
+	converted = talloc_array(mem_ctx, char, pathlen + insertlen + 1);
+	if (converted == NULL) {
+		goto fail;
+	}
+
+	if (path[pathlen-1] != '/') {
+		/*
+		 * Append a fake slash to find the snapshot root
+		 */
+		size_t *tmp;
+		tmp = talloc_realloc(talloc_tos(), slashes,
+				     size_t, num_slashes+1);
+		if (tmp == NULL) {
+			goto fail;
+		}
+		slashes = tmp;
+		slashes[num_slashes] = pathlen;
+		num_slashes += 1;
+	}
+
+	min_offset = 0;
+
+	if (!lp_parm_bool(SNUM(handle->conn), "shadow", "crossmountpoints",
+			  false)) {
+		char *mount_point;
+
+		mount_point = shadow_copy3_find_mount_point(talloc_tos(),
+							    handle);
+		if (mount_point == NULL) {
+			goto fail;
+		}
+		min_offset = strlen(mount_point);
+		TALLOC_FREE(mount_point);
+	}
+
+	memcpy(converted, path, pathlen+1);
+	converted[pathlen+insertlen] = '\0';
+
+	ZERO_STRUCT(converted_fname);
+	converted_fname.base_name = converted;
+
+	for (i = num_slashes-1; i>=0; i--) {
+		int ret;
+		size_t offset;
+
+		offset = slashes[i];
+
+		if (offset < min_offset) {
+			errno = ENOENT;
+			goto fail;
+		}
+
+		memcpy(converted+offset, insert, insertlen);
+
+		offset += insertlen;
+		memcpy(converted+offset, path + slashes[i],
+		       pathlen - slashes[i]);
+
+		ret = SMB_VFS_NEXT_LSTAT(handle, &converted_fname);
+
+		DEBUG(10, ("Trying %s: %d (%s)\n", converted,
+			   ret, ret == 0 ? "ok" : strerror(errno)));
+		if (ret == 0) {
+			/* success */
+			break;
+		}
+		if (errno == ENOTDIR) {
+			/*
+			 * This is a valid condition: We appended the
+			 * .snaphots/@GMT.. to a file name. Just try
+			 * with the upper levels.
+			 */
+			continue;
+		}
+		if (errno != ENOENT) {
+			/* Other problem than "not found" */
+			goto fail;
+		}
+	}
+
+	if (i >= 0) {
+		/*
+		 * Found something
+		 */
+		DEBUG(10, ("Found %s\n", converted));
+		result = converted;
+		converted = NULL;
+	} else {
+		errno = ENOENT;
+	}
+fail:
+	saved_errno = errno;
+	TALLOC_FREE(converted);
+	TALLOC_FREE(insert);
+	TALLOC_FREE(slashes);
+	TALLOC_FREE(path);
+	errno = saved_errno;
+	return result;
+}
+
+/*
+  modify a sbuf return to ensure that inodes in the shadow directory
+  are different from those in the main directory
+ */
+static void convert_sbuf(vfs_handle_struct *handle, const char *fname,
+			 SMB_STRUCT_STAT *sbuf)
+{
+	if (lp_parm_bool(SNUM(handle->conn), "shadow", "fixinodes", False)) {
+		/* some snapshot systems, like GPFS, return the name
+		   device:inode for the snapshot files as the current
+		   files. That breaks the 'restore' button in the shadow copy
+		   GUI, as the client gets a sharing violation.
+
+		   This is a crude way of allowing both files to be
+		   open at once. It has a slight chance of inode
+		   number collision, but I can't see a better approach
+		   without significant VFS changes
+		*/
+		uint32_t shash;
+		TDB_DATA data = string_tdb_data(fname);
+
+		shash = tdb_jenkins_hash(&data) & 0xFF000000;
+		if (shash == 0) {
+			shash = 1;
+		}
+		sbuf->st_ex_ino ^= shash;
+	}
+}
+
+static SMB_STRUCT_DIR *shadow_copy3_opendir(vfs_handle_struct *handle,
+					    const char *fname,
+					    const char *mask,
+					    uint32 attr)
+{
+	time_t timestamp;
+	char *stripped;
+	SMB_STRUCT_DIR *ret;
+	int saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return NULL;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_OPENDIR(handle, fname, mask, attr);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return NULL;
+	}
+	ret = SMB_VFS_NEXT_OPENDIR(handle, conv, mask, attr);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_rename(vfs_handle_struct *handle,
+			       const struct smb_filename *smb_fname_src,
+			       const struct smb_filename *smb_fname_dst)
+{
+	time_t timestamp_src, timestamp_dst;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle,
+					 smb_fname_src->base_name,
+					 &timestamp_src, NULL)) {
+		return -1;
+	}
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle,
+					 smb_fname_dst->base_name,
+					 &timestamp_dst, NULL)) {
+		return -1;
+	}
+	if (timestamp_src != 0) {
+		errno = EXDEV;
+		return -1;
+	}
+	if (timestamp_dst != 0) {
+		errno = EROFS;
+		return -1;
+	}
+	return SMB_VFS_NEXT_RENAME(handle, smb_fname_src, smb_fname_dst);
+}
+
+static int shadow_copy3_symlink(vfs_handle_struct *handle,
+				const char *oldname, const char *newname)
+{
+	time_t timestamp_old, timestamp_new;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, oldname,
+					 &timestamp_old, NULL)) {
+		return -1;
+	}
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, newname,
+					 &timestamp_new, NULL)) {
+		return -1;
+	}
+	if ((timestamp_old != 0) || (timestamp_new != 0)) {
+		errno = EROFS;
+		return -1;
+	}
+	return SMB_VFS_NEXT_SYMLINK(handle, oldname, newname);
+}
+
+static int shadow_copy3_link(vfs_handle_struct *handle,
+			     const char *oldname, const char *newname)
+{
+	time_t timestamp_old, timestamp_new;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, oldname,
+					 &timestamp_old, NULL)) {
+		return -1;
+	}
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, newname,
+					 &timestamp_new, NULL)) {
+		return -1;
+	}
+	if ((timestamp_old != 0) || (timestamp_new != 0)) {
+		errno = EROFS;
+		return -1;
+	}
+	return SMB_VFS_NEXT_LINK(handle, oldname, newname);
+}
+
+static int shadow_copy3_stat(vfs_handle_struct *handle,
+			     struct smb_filename *smb_fname)
+{
+	time_t timestamp;
+	char *stripped, *tmp;
+	int ret, saved_errno;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle,
+					 smb_fname->base_name,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_STAT(handle, smb_fname);
+	}
+
+	tmp = smb_fname->base_name;
+	smb_fname->base_name = shadow_copy3_convert(
+		talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+
+	if (smb_fname->base_name == NULL) {
+		smb_fname->base_name = tmp;
+		return -1;
+	}
+
+	ret = SMB_VFS_NEXT_STAT(handle, smb_fname);
+	saved_errno = errno;
+
+	TALLOC_FREE(smb_fname->base_name);
+	smb_fname->base_name = tmp;
+
+	if (ret == 0) {
+		convert_sbuf(handle, smb_fname->base_name, &smb_fname->st);
+	}
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_lstat(vfs_handle_struct *handle,
+			      struct smb_filename *smb_fname)
+{
+	time_t timestamp;
+	char *stripped, *tmp;
+	int ret, saved_errno;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle,
+					 smb_fname->base_name,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_LSTAT(handle, smb_fname);
+	}
+
+	tmp = smb_fname->base_name;
+	smb_fname->base_name = shadow_copy3_convert(
+		talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+
+	if (smb_fname->base_name == NULL) {
+		smb_fname->base_name = tmp;
+		return -1;
+	}
+
+	ret = SMB_VFS_NEXT_LSTAT(handle, smb_fname);
+	saved_errno = errno;
+
+	TALLOC_FREE(smb_fname->base_name);
+	smb_fname->base_name = tmp;
+
+	if (ret == 0) {
+		convert_sbuf(handle, smb_fname->base_name, &smb_fname->st);
+	}
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_fstat(vfs_handle_struct *handle, files_struct *fsp,
+			      SMB_STRUCT_STAT *sbuf)
+{
+	time_t timestamp;
+	int ret;
+
+	ret = SMB_VFS_NEXT_FSTAT(handle, fsp, sbuf);
+	if (ret == -1) {
+		return ret;
+	}
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle,
+					 fsp->fsp_name->base_name,
+					 &timestamp, NULL)) {
+		return 0;
+	}
+	if (timestamp != 0) {
+		convert_sbuf(handle, fsp->fsp_name->base_name, sbuf);
+	}
+	return 0;
+}
+
+static int shadow_copy3_open(vfs_handle_struct *handle,
+			     struct smb_filename *smb_fname, files_struct *fsp,
+			     int flags, mode_t mode)
+{
+	time_t timestamp;
+	char *stripped, *tmp;
+	int ret, saved_errno;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle,
+					 smb_fname->base_name,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_OPEN(handle, smb_fname, fsp, flags, mode);
+	}
+
+	tmp = smb_fname->base_name;
+	smb_fname->base_name = shadow_copy3_convert(
+		talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+
+	if (smb_fname->base_name == NULL) {
+		smb_fname->base_name = tmp;
+		return -1;
+	}
+
+	ret = SMB_VFS_NEXT_OPEN(handle, smb_fname, fsp, flags, mode);
+	saved_errno = errno;
+
+	TALLOC_FREE(smb_fname->base_name);
+	smb_fname->base_name = tmp;
+
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_unlink(vfs_handle_struct *handle,
+			       const struct smb_filename *smb_fname)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	struct smb_filename *conv;
+	NTSTATUS status;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle,
+					 smb_fname->base_name,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_UNLINK(handle, smb_fname);
+	}
+	status = copy_smb_filename(talloc_tos(), smb_fname, &conv);
+	if (!NT_STATUS_IS_OK(status)) {
+		errno = ENOMEM;
+		return -1;
+	}
+	conv->base_name = shadow_copy3_convert(
+		conv, handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv->base_name == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_UNLINK(handle, conv);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_chmod(vfs_handle_struct *handle, const char *fname,
+			      mode_t mode)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_CHMOD(handle, fname, mode);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_CHMOD(handle, conv, mode);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_chown(vfs_handle_struct *handle, const char *fname,
+			      uid_t uid, gid_t gid)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_CHOWN(handle, fname, uid, gid);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_CHOWN(handle, conv, uid, gid);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_chdir(vfs_handle_struct *handle,
+			      const char *fname)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_CHDIR(handle, fname);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_CHDIR(handle, conv);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_ntimes(vfs_handle_struct *handle,
+			       const struct smb_filename *smb_fname,
+			       struct smb_file_time *ft)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	struct smb_filename *conv;
+	NTSTATUS status;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle,
+					 smb_fname->base_name,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_NTIMES(handle, smb_fname, ft);
+	}
+	status = copy_smb_filename(talloc_tos(), smb_fname, &conv);
+	if (!NT_STATUS_IS_OK(status)) {
+		errno = ENOMEM;
+		return -1;
+	}
+	conv->base_name = shadow_copy3_convert(
+		conv, handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv->base_name == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_NTIMES(handle, conv, ft);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_readlink(vfs_handle_struct *handle,
+				 const char *fname, char *buf, size_t bufsiz)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_READLINK(handle, fname, buf, bufsiz);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_READLINK(handle, conv, buf, bufsiz);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_mknod(vfs_handle_struct *handle,
+			      const char *fname, mode_t mode, SMB_DEV_T dev)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_MKNOD(handle, fname, mode, dev);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_MKNOD(handle, conv, mode, dev);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static char *shadow_copy3_realpath(vfs_handle_struct *handle,
+				   const char *fname)
+{
+	time_t timestamp;
+	char *stripped = NULL;
+	char *tmp = NULL;
+	char *result = NULL;
+	char *inserted = NULL;
+	char *inserted_to, *inserted_end;
+	int saved_errno;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		goto done;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_REALPATH(handle, fname);
+	}
+
+	tmp = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	if (tmp == NULL) {
+		goto done;
+	}
+
+	result = SMB_VFS_NEXT_REALPATH(handle, tmp);
+	if (result == NULL) {
+		goto done;
+	}
+
+	/*
+	 * Take away what we've inserted. This removes the @GMT-thingy
+	 * completely, but will give a path under the share root.
+	 */
+	inserted = shadow_copy3_insert_string(talloc_tos(), handle, timestamp);
+	if (inserted == NULL) {
+		goto done;
+	}
+	inserted_to = strstr_m(result, inserted);
+	if (inserted_to == NULL) {
+		DEBUG(2, ("SMB_VFS_NEXT_REALPATH removed %s\n", inserted));
+		goto done;
+	}
+	inserted_end = inserted_to + talloc_get_size(inserted) - 1;
+	memmove(inserted_to, inserted_end, strlen(inserted_end)+1);
+
+done:
+	saved_errno = errno;
+	TALLOC_FREE(inserted);
+	TALLOC_FREE(tmp);
+	TALLOC_FREE(stripped);
+	errno = saved_errno;
+	return result;
+}
+
+static char *have_snapdir(struct vfs_handle_struct *handle,
+			  const char *path)
+{
+	struct smb_filename smb_fname;
+	int ret;
+
+	ZERO_STRUCT(smb_fname);
+	smb_fname.base_name = talloc_asprintf(
+		talloc_tos(), "%s/%s", path,
+		lp_parm_const_string(SNUM(handle->conn), "shadow", "snapdir",
+				     ".snapshots"));
+	if (smb_fname.base_name == NULL) {
+		return NULL;
+	}
+
+	ret = SMB_VFS_NEXT_STAT(handle, &smb_fname);
+	if ((ret == 0) && (S_ISDIR(smb_fname.st.st_ex_mode))) {
+		return smb_fname.base_name;
+	}
+	TALLOC_FREE(smb_fname.base_name);
+	return NULL;
+}
+
+static char *shadow_copy3_find_snapdir(TALLOC_CTX *mem_ctx,
+				       struct vfs_handle_struct *handle,
+				       struct smb_filename *smb_fname)
+{
+	char *path, *p;
+
+	path = talloc_asprintf(mem_ctx, "%s/%s",
+			       handle->conn->connectpath,
+			       smb_fname->base_name);
+	if (path == NULL) {
+		return NULL;
+	}
+
+	while ((p = strrchr(path, '/')) && (p > path)) {
+		char *snapdir;
+
+		p[0] = '\0';
+
+		snapdir = have_snapdir(handle, path);
+		if (snapdir != NULL) {
+			TALLOC_FREE(path);
+			return snapdir;
+		}
+	}
+	TALLOC_FREE(path);
+	return NULL;
+}
+
+static bool shadow_copy3_snapshot_to_gmt(TALLOC_CTX *mem_ctx,
+					 vfs_handle_struct *handle,
+					 const char *name,
+					 char *gmt, size_t gmt_len)
+{
+	struct tm timestamp;
+	time_t timestamp_t;
+	const char *fmt;
+
+	fmt = lp_parm_const_string(SNUM(handle->conn), "shadow",
+				   "format", GMT_FORMAT);
+
+	ZERO_STRUCT(timestamp);
+	if (strptime(name, fmt, &timestamp) == NULL) {
+		DEBUG(10, ("shadow_copy2_snapshot_to_gmt: no match %s: %s\n",
+			   fmt, name));
+		return false;
+	}
+
+	DEBUG(10, ("shadow_copy2_snapshot_to_gmt: match %s: %s\n", fmt, name));
+
+	if (lp_parm_bool(SNUM(handle->conn), "shadow", "localtime", false)) {
+		timestamp.tm_isdst = -1;
+		timestamp_t = mktime(&timestamp);
+		gmtime_r(&timestamp_t, &timestamp);
+	}
+	strftime(gmt, gmt_len, GMT_FORMAT, &timestamp);
+	return true;
+}
+
+static int shadow_copy3_get_shadow_copy_data(
+	vfs_handle_struct *handle, files_struct *fsp,
+	struct shadow_copy_data *shadow_copy3_data,
+	bool labels)
+{
+	SMB_STRUCT_DIR *p;
+	const char *snapdir;
+	SMB_STRUCT_DIRENT *d;
+	TALLOC_CTX *tmp_ctx = talloc_stackframe();
+
+	snapdir = shadow_copy3_find_snapdir(tmp_ctx, handle, fsp->fsp_name);
+	if (snapdir == NULL) {
+		DEBUG(0,("shadow:snapdir not found for %s in get_shadow_copy_data\n",
+			 handle->conn->connectpath));
+		errno = EINVAL;
+		talloc_free(tmp_ctx);
+		return -1;
+	}
+
+	p = SMB_VFS_NEXT_OPENDIR(handle, snapdir, NULL, 0);
+
+	if (!p) {
+		DEBUG(2,("shadow_copy3: SMB_VFS_NEXT_OPENDIR() failed for '%s'"
+			 " - %s\n", snapdir, strerror(errno)));
+		talloc_free(tmp_ctx);
+		errno = ENOSYS;
+		return -1;
+	}
+
+	shadow_copy3_data->num_volumes = 0;
+	shadow_copy3_data->labels      = NULL;
+
+	while ((d = SMB_VFS_NEXT_READDIR(handle, p, NULL))) {
+		char snapshot[GMT_NAME_LEN+1];
+		SHADOW_COPY_LABEL *tlabels;
+
+		/*
+		 * ignore names not of the right form in the snapshot
+		 * directory
+		 */
+		if (!shadow_copy3_snapshot_to_gmt(
+			    tmp_ctx, handle, d->d_name,
+			    snapshot, sizeof(snapshot))) {
+
+			DEBUG(6, ("shadow_copy3_get_shadow_copy_data: "
+				  "ignoring %s\n", d->d_name));
+			continue;
+		}
+		DEBUG(6,("shadow_copy3_get_shadow_copy_data: %s -> %s\n",
+			 d->d_name, snapshot));
+
+		if (!labels) {
+			/* the caller doesn't want the labels */
+			shadow_copy3_data->num_volumes++;
+			continue;
+		}
+
+		tlabels = talloc_realloc(shadow_copy3_data,
+					 shadow_copy3_data->labels,
+					 SHADOW_COPY_LABEL,
+					 shadow_copy3_data->num_volumes+1);
+		if (tlabels == NULL) {
+			DEBUG(0,("shadow_copy3: out of memory\n"));
+			SMB_VFS_NEXT_CLOSEDIR(handle, p);
+			talloc_free(tmp_ctx);
+			return -1;
+		}
+
+		strlcpy(tlabels[shadow_copy3_data->num_volumes], snapshot,
+			sizeof(*tlabels));
+
+		shadow_copy3_data->num_volumes++;
+		shadow_copy3_data->labels = tlabels;
+	}
+
+	SMB_VFS_NEXT_CLOSEDIR(handle,p);
+
+	talloc_free(tmp_ctx);
+	return 0;
+}
+
+static NTSTATUS shadow_copy3_get_nt_acl(vfs_handle_struct *handle,
+					const char *fname,
+					uint32 security_info,
+					struct security_descriptor **ppdesc)
+{
+	time_t timestamp;
+	char *stripped;
+	NTSTATUS status;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return map_nt_error_from_unix(errno);
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_GET_NT_ACL(handle, fname, security_info,
+					       ppdesc);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return map_nt_error_from_unix(errno);
+	}
+	status = SMB_VFS_NEXT_GET_NT_ACL(handle, fname, security_info, ppdesc);
+	TALLOC_FREE(conv);
+	return status;
+}
+
+static int shadow_copy3_mkdir(vfs_handle_struct *handle,
+			      const char *fname, mode_t mode)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_MKDIR(handle, fname, mode);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_MKDIR(handle, conv, mode);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_rmdir(vfs_handle_struct *handle, const char *fname)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_RMDIR(handle, fname);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_RMDIR(handle, conv);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_chflags(vfs_handle_struct *handle, const char *fname,
+				unsigned int flags)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_CHFLAGS(handle, fname, flags);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_CHFLAGS(handle, conv, flags);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static ssize_t shadow_copy3_getxattr(vfs_handle_struct *handle,
+				     const char *fname, const char *aname,
+				     void *value, size_t size)
+{
+	time_t timestamp;
+	char *stripped;
+	ssize_t ret;
+	int saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_GETXATTR(handle, fname, aname, value,
+					     size);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_GETXATTR(handle, conv, aname, value, size);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static ssize_t shadow_copy3_lgetxattr(vfs_handle_struct *handle,
+				      const char *fname, const char *aname,
+				      void *value, size_t size)
+{
+	time_t timestamp;
+	char *stripped;
+	ssize_t ret;
+	int saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_LGETXATTR(handle, fname, aname, value,
+					      size);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_LGETXATTR(handle, conv, aname, value, size);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static ssize_t shadow_copy3_listxattr(struct vfs_handle_struct *handle,
+				      const char *fname,
+				      char *list, size_t size)
+{
+	time_t timestamp;
+	char *stripped;
+	ssize_t ret;
+	int saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_LISTXATTR(handle, fname, list, size);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_LISTXATTR(handle, conv, list, size);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_removexattr(vfs_handle_struct *handle,
+				    const char *fname, const char *aname)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_REMOVEXATTR(handle, fname, aname);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_REMOVEXATTR(handle, conv, aname);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_lremovexattr(vfs_handle_struct *handle,
+				     const char *fname, const char *aname)
+{
+	time_t timestamp;
+	char *stripped;
+	int ret, saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_LREMOVEXATTR(handle, fname, aname);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_LREMOVEXATTR(handle, conv, aname);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_setxattr(struct vfs_handle_struct *handle,
+				 const char *fname,
+				 const char *aname, const void *value,
+				 size_t size, int flags)
+{
+	time_t timestamp;
+	char *stripped;
+	ssize_t ret;
+	int saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_SETXATTR(handle, fname, aname, value, size,
+					     flags);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_SETXATTR(handle, conv, aname, value, size, flags);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_lsetxattr(struct vfs_handle_struct *handle,
+				  const char *fname,
+				  const char *aname, const void *value,
+				  size_t size, int flags)
+{
+	time_t timestamp;
+	char *stripped;
+	ssize_t ret;
+	int saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_LSETXATTR(handle, fname, aname, value,
+					      size, flags);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_LSETXATTR(handle, conv, aname, value, size, flags);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_chmod_acl(vfs_handle_struct *handle,
+				  const char *fname, mode_t mode)
+{
+	time_t timestamp;
+	char *stripped;
+	ssize_t ret;
+	int saved_errno;
+	char *conv;
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, fname,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_CHMOD_ACL(handle, fname, mode);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_CHMOD_ACL(handle, conv, mode);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+static int shadow_copy3_get_real_filename(struct vfs_handle_struct *handle,
+					  const char *path,
+					  const char *name,
+					  TALLOC_CTX *mem_ctx,
+					  char **found_name)
+{
+	time_t timestamp;
+	char *stripped;
+	ssize_t ret;
+	int saved_errno;
+	char *conv;
+
+	if (shadow_copy3_strip_snapshot(talloc_tos(), handle, name,
+					&timestamp, &stripped)
+	    && (stripped[0] == '\0')) {
+		*found_name = talloc_strdup(mem_ctx, name);
+		if (*found_name == NULL) {
+			errno = ENOMEM;
+			return -1;
+		}
+		return 0;
+	}
+
+	if (!shadow_copy3_strip_snapshot(talloc_tos(), handle, path,
+					 &timestamp, &stripped)) {
+		return -1;
+	}
+	if (timestamp == 0) {
+		return SMB_VFS_NEXT_GET_REAL_FILENAME(handle, path, name,
+						      mem_ctx, found_name);
+	}
+	conv = shadow_copy3_convert(talloc_tos(), handle, stripped, timestamp);
+	TALLOC_FREE(stripped);
+	if (conv == NULL) {
+		return -1;
+	}
+	ret = SMB_VFS_NEXT_GET_REAL_FILENAME(handle, conv, name,
+					     mem_ctx, found_name);
+	saved_errno = errno;
+	TALLOC_FREE(conv);
+	errno = saved_errno;
+	return ret;
+}
+
+
+static struct vfs_fn_pointers vfs_shadow_copy3_fns = {
+	.opendir = shadow_copy3_opendir,
+	.rename = shadow_copy3_rename,
+	.link = shadow_copy3_link,
+	.symlink = shadow_copy3_symlink,
+	.stat = shadow_copy3_stat,
+	.lstat = shadow_copy3_lstat,
+	.fstat = shadow_copy3_fstat,
+	.open_fn = shadow_copy3_open,
+	.unlink = shadow_copy3_unlink,
+	.chmod = shadow_copy3_chmod,
+	.chown = shadow_copy3_chown,
+	.chdir = shadow_copy3_chdir,
+	.ntimes = shadow_copy3_ntimes,
+	.vfs_readlink = shadow_copy3_readlink,
+	.mknod = shadow_copy3_mknod,
+	.realpath = shadow_copy3_realpath,
+	.get_nt_acl = shadow_copy3_get_nt_acl,
+	.get_shadow_copy_data = shadow_copy3_get_shadow_copy_data,
+	.mkdir = shadow_copy3_mkdir,
+	.rmdir = shadow_copy3_rmdir,
+	.getxattr = shadow_copy3_getxattr,
+	.lgetxattr = shadow_copy3_lgetxattr,
+	.listxattr = shadow_copy3_listxattr,
+	.removexattr = shadow_copy3_removexattr,
+	.lremovexattr = shadow_copy3_lremovexattr,
+	.setxattr = shadow_copy3_setxattr,
+	.lsetxattr = shadow_copy3_lsetxattr,
+	.chmod_acl = shadow_copy3_chmod_acl,
+	.chflags = shadow_copy3_chflags,
+	.get_real_filename = shadow_copy3_get_real_filename,
+};
+
+NTSTATUS vfs_shadow_copy3_init(void);
+NTSTATUS vfs_shadow_copy3_init(void)
+{
+	return smb_register_vfs(SMB_VFS_INTERFACE_VERSION,
+				"shadow_copy3", &vfs_shadow_copy3_fns);
+}
