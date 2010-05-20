--- drivers/libhid.c.orig	2010-02-11 22:43:23.000000000 +0100
+++ drivers/libhid.c	2010-05-18 13:08:04.000000000 +0200
@@ -141,7 +141,7 @@
 {
 	int	id = pData->ReportID;
 	int	r;
-	unsigned char	buf[SMALLBUF];
+	unsigned char	buf[8];	/* Maximum size for low-speed USB devices */
 
 	if (rbuf->ts[id] + age > time(NULL)) {
 		/* buffered report is still good; nothing to do */
@@ -469,7 +469,7 @@
  */
 int HIDGetEvents(hid_dev_handle_t udev, HIDData_t **event, int eventsize)
 {
-	unsigned char	buf[SMALLBUF];
+	unsigned char	buf[8];	/* Maximum size for low-speed USB devices */
 	int		itemCount = 0;
 	int		buflen, r, i;
 	HIDData_t	*pData;
