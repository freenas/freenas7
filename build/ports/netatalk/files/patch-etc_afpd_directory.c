--- etc/afpd/directory.c.orig	2008-05-14 15:30:52.000000000 +0200
+++ etc/afpd/directory.c	2008-05-14 15:36:36.000000000 +0200
@@ -2271,6 +2271,54 @@
     return err;
 }
 
+
+int afp_syncdir(obj, ibuf, ibuflen, rbuf, rbuflen )
+AFPObj  *obj;
+char    *ibuf, *rbuf;
+int     ibuflen, *rbuflen;
+{
+    DIR                  *dp;
+    int                  dfd;
+    struct vol           *vol;
+    struct dir           *dir;
+    u_int32_t            did;
+    u_int16_t            vid;
+
+    *rbuflen = 0;
+    ibuf += 2;
+
+    memcpy( &vid, ibuf, sizeof( vid ));
+    ibuf += sizeof( vid );
+    if (NULL == (vol = getvolbyvid( vid )) ) {
+        return( AFPERR_PARAM );
+    }
+
+    memcpy( &did, ibuf, sizeof( did ));
+    ibuf += sizeof( did );
+    if (NULL == ( dir = dirlookup( vol, did )) ) {
+        return afp_errno; /* was AFPERR_NOOBJ */
+    }
+
+    if (NULL == ( dp = opendir( "." )) ) {
+        switch( errno ) {
+        case ENOENT :
+            return( AFPERR_NOOBJ );
+        case EACCES :
+            return( AFPERR_ACCESS );
+        default :
+            return( AFPERR_PARAM );
+        }
+    }
+
+    dfd = dirfd( dp );
+    if ( fsync ( dfd ) < 0 ) {
+        LOG(log_error, logtype_afpd, "syncdir(%s): ddir(%d) %s", dir->d_u_name, dfd, strerror(errno) );
+    }
+    closedir(dp);
+
+    return ( AFP_OK );
+}
+
 int afp_createdir(obj, ibuf, ibuflen, rbuf, rbuflen )
 AFPObj  *obj;
 char	*ibuf, *rbuf;
