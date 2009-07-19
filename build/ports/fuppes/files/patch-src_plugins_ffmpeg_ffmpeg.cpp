--- src/plugins/ffmpeg/ffmpeg.cpp.orig	2009-07-19 13:42:50.000000000 +0000
+++ src/plugins/ffmpeg/ffmpeg.cpp	2009-07-19 17:19:30.000000000 +0000
@@ -1026,7 +1026,7 @@
 #define LIBAVCODEC_VERSION_MINOR 0
 #endif
 
-#if LIBAVCODEC_VERSION_MINOR >= 11
+#if LIBAVCODEC_VERSION_MINOR <= 11
                     av_freep(subtitle_to_free->rects[i]->pict.data[0]);
                     av_freep(subtitle_to_free->rects[i]->pict.data[1]);
                     av_freep(subtitle_to_free->rects[i]);
