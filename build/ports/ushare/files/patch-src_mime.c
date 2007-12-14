--- src/mime.c.orig	Sun Jul  1 16:57:22 2007
+++ src/mime.c	Fri Dec 14 10:02:21 2007
@@ -41,9 +41,10 @@
 const struct mime_type_t MIME_Type_List[] = {
   /* Video files */
   { "asf",   "MPEG4_P2_ASF_SP_G726",     UPNP_VIDEO, "http-get:*:video/x-ms-asf:"},
-  { "avi",   "MPEG4_P2_TS_SP_MPEG1_L3",  UPNP_VIDEO, "http-get:*:video/x-msvideo:"},
+  { "avc",   "AVC_MP4_BL_CIF15_AAC_520", UPNP_VIDEO, "http-get:*:video/avi:"},
+  { "avi",   "MPEG4_P2_TS_SP_MPEG1_L3",  UPNP_VIDEO, "http-get:*:video/avi:"},
   { "dv",    "MPEG_PS_NTSC",             UPNP_VIDEO, "http-get:*:video/x-dv:"},
-  { "divx",  "MPEG4_P2_TS_SP_MPEG1_L3",  UPNP_VIDEO, "http-get:*:video/x-msvideo:"},
+  { "divx",  "MPEG4_P2_TS_SP_MPEG1_L3",  UPNP_VIDEO, "http-get:*:video/avi:"},
   { "wmv",   "WMVMED_BASE",              UPNP_VIDEO, "http-get:*:video/x-ms-wmv:"},
   { "mjpg",  "MPEG1",                    UPNP_VIDEO, "http-get:*:video/x-motion-jpeg:"},
   { "mjpeg", "MPEG1",                    UPNP_VIDEO, "http-get:*:video/x-motion-jpeg:"},
@@ -65,11 +66,13 @@
   { "mkv",   NULL,                       UPNP_VIDEO, "http-get:*:video/mpeg:"},
   { "rmvb",  NULL,                       UPNP_VIDEO, "http-get:*:video/mpeg:"},
   { "mov",   "AVC_TS_MP_HD_AAC_MULT5",   UPNP_VIDEO, "http-get:*:video/quicktime:"},
+  { "hdmov", "AVC_TS_MP_HD_AAC_MULT5",   UPNP_VIDEO, "http-get:*:video/quicktime:"},
   { "qt",    NULL,                       UPNP_VIDEO, "http-get:*:video/quicktime:"},
   { "bin",   NULL,                       UPNP_VIDEO, "http-get:*:video/mpeg2:"},
   { "iso",   NULL,                       UPNP_VIDEO, "http-get:*:video/mpeg2:"},
 
   /* Audio files */
+  { "3gp",  NULL,         UPNP_AUDIO, "http-get:*:audio/3gpp:"},
   { "aac",  "AAC_ADTS",   UPNP_AUDIO, "http-get:*:audio/x-aac:"},
   { "ac3",  "AC3",        UPNP_AUDIO, "http-get:*:audio/x-ac3:"},
   { "aif",  NULL,         UPNP_AUDIO, "http-get:*:audio/aiff:"},
@@ -95,6 +98,7 @@
   { "ra",   NULL,         UPNP_AUDIO, "http-get:*:audio/x-pn-realaudio:"},
   { "rm",   NULL,         UPNP_AUDIO, "http-get:*:audio/x-pn-realaudio:"},
   { "ram",  NULL,         UPNP_AUDIO, "http-get:*:audio/x-pn-realaudio:"},
+  { "flac", NULL,         UPNP_AUDIO, "http-get:*:audio/x-flac:"},
 
   /* Images files */
   { "bmp",  NULL,      UPNP_PHOTO, "http-get:*:image/bmp:"},
