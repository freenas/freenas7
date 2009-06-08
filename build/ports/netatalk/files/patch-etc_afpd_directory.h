--- etc/afpd/directory.h.orig	2008-05-14 15:30:52.000000000 +0200
+++ etc/afpd/directory.h	2008-05-14 15:36:39.000000000 +0200
@@ -219,6 +219,7 @@
 extern int      afp_closedir __P((AFPObj *, char *, int, char *, int *));
 extern int	afp_mapid __P((AFPObj *, char *, int, char *, int *));
 extern int	afp_mapname __P((AFPObj *, char *, int, char *, int *));
+extern int      afp_syncdir __P((AFPObj *, char *, int, char *, int *));
 
 /* from enumerate.c */
 extern int	afp_enumerate __P((AFPObj *, char *, unsigned int, char *, unsigned int *));
