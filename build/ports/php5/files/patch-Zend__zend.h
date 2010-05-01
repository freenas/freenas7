--- Zend/zend.h.orig	2010-01-15 18:26:52.000000000 +0100
+++ Zend/zend.h	2010-04-30 22:34:25.000000000 +0200
@@ -184,7 +184,7 @@
 #endif
 #define restrict __restrict__
 
-#if (HAVE_ALLOCA || (defined (__GNUC__) && __GNUC__ >= 2)) && !(defined(ZTS) && defined(ZEND_WIN32)) && !(defined(ZTS) && defined(NETWARE)) && !(defined(ZTS) && defined(HPUX)) && !defined(DARWIN)
+#if (HAVE_ALLOCA || (defined (__GNUC__) && __GNUC__ >= 2)) && !(defined(ZTS) && defined(ZEND_WIN32)) && !(defined(ZTS) && defined(NETWARE)) && !(defined(ZTS) && defined(HPUX)) && !defined(DARWIN) && !(defined(ZTS) && defined(__FreeBSD__))
 # define do_alloca(p) alloca(p)
 # define free_alloca(p)
 # define ZEND_ALLOCA_MAX_SIZE (32 * 1024)
