--- src/sys/cddl/contrib/opensolaris/uts/common/fs/zfs/arc.c.orig	2010-05-25 05:09:40.000000000 +0900
+++ src/sys/cddl/contrib/opensolaris/uts/common/fs/zfs/arc.c	2010-06-13 10:59:00.000000000 +0900
@@ -3763,12 +3763,33 @@
 #endif
 	/* set min cache to 1/32 of all memory, or 16MB, whichever is more */
 	arc_c_min = MAX(arc_c / 4, 64<<18);
+#if 0
 	/* set max to 1/2 of all memory, or all but 1GB, whichever is more */
 	if (arc_c * 8 >= 1<<30)
 		arc_c_max = (arc_c * 8) - (1<<30);
 	else
 		arc_c_max = arc_c_min;
 	arc_c_max = MAX(arc_c * 5, arc_c_max);
+#endif
+#if 1
+	/* for FreeNAS tuning */
+	/* max = kmem - 1.5GB if kmem >= 4GB */
+	/* max = kmem - 1GB if kmem >= 1GB + 256MB */
+	/* otherwise adjust to small */
+	if (arc_c * 8 >= (4096UL * (1<<20)))
+		arc_c_max = (arc_c * 8) - (1536UL * (1<<20));
+	else if (arc_c * 8 >= (1280UL * (1<<20)))
+		arc_c_max = (arc_c * 8) - (1024UL * (1<<20));
+	else if (arc_c * 8 >= (1024UL * (1<<20)))
+		arc_c_max = (256UL * (1<<20));
+	else if (arc_c * 8 >= (512UL * (1<<20)))
+		arc_c_max = (128UL * (1<<20));
+	else if (arc_c * 8 >= (360UL * (1<<20)))
+		arc_c_max = (64UL * (1<<20));
+	else
+		arc_c_max = MIN(arc_c_min, (32UL * (1<<20)));
+#endif
+
 #ifdef _KERNEL
 	/*
 	 * Allow the tunables to override our calculations if they are
@@ -3801,6 +3822,16 @@
 	if (zfs_arc_p_min_shift > 0)
 		arc_p_min_shift = zfs_arc_p_min_shift;
 
+#if 1
+	/* for FreeNAS tuning */
+	if (arc_c_max >= (1024UL * (1<<20)) && arc_c_min < arc_c_max / 2
+	    && zfs_arc_min == 0)
+		arc_c_min = arc_c_max / 2;
+	else if (arc_c_max >= (256UL * (1<<20)) && arc_c_min < arc_c_max / 4
+	    && zfs_arc_min == 0)
+		arc_c_min = arc_c_max / 4;
+#endif
+
 	/* if kmem_flags are set, lets try to use less memory */
 	if (kmem_debugging())
 		arc_c = arc_c / 2;
