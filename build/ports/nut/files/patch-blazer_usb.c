--- drivers/blazer_usb.c.orig	2009-02-17 10:20:48.000000000 +0100
+++ drivers/blazer_usb.c	2010-10-09 13:18:37.000000000 +0200
@@ -4,7 +4,7 @@
  * A document describing the protocol implemented by this driver can be
  * found online at "http://www.networkupstools.org/protocols/megatec.html".
  *
- * Copyright (C) 2008 - Arjen de Korte <adkorte-guest@alioth.debian.org>
+ * Copyright (C) 2003-2009  Arjen de Korte <adkorte-guest@alioth.debian.org>
  *
  * This program is free software; you can redistribute it and/or modify
  * it under the terms of the GNU General Public License as published by
@@ -47,12 +47,86 @@
 static int (*subdriver_command)(const char *cmd, char *buf, size_t buflen) = NULL;
 
 
+static int cypress_command(const char *cmd, char *buf, size_t buflen)
+{
+	char	tmp[SMALLBUF];
+	int	ret;
+	size_t	i;
+
+	memset(tmp, 0, sizeof(tmp));
+	snprintf(tmp, sizeof(tmp), "%s", cmd);
+
+	for (i = 0; i < strlen(tmp); i += ret) {
+
+		/* Write data in 8-byte chunks */
+		/* ret = usb->set_report(udev, 0, (unsigned char *)&tmp[i], 8); */
+		ret = usb_control_msg(udev, USB_ENDPOINT_OUT + USB_TYPE_CLASS + USB_RECIP_INTERFACE,
+			0x09, 0x200, 0, &tmp[i], 8, 1000);
+
+		if (ret <= 0) {
+			upsdebugx(3, "send: %s", ret ? usb_strerror() : "timeout");
+			return ret;
+		}
+	}
+
+	upsdebugx(3, "send: %.*s", (int)strcspn(tmp, "\r"), tmp);
+
+	memset(buf, 0, buflen);
+
+	for (i = 0; (i <= buflen-8) && (strchr(buf, '\r') == NULL); i += ret) {
+
+		/* Read data in 8-byte chunks */
+		/* ret = usb->get_interrupt(udev, (unsigned char *)&buf[i], 8, 1000); */
+		ret = usb_interrupt_read(udev, 0x81, &buf[i], 8, 1000);
+
+		/*
+		 * Any errors here mean that we are unable to read a reply (which
+		 * will happen after successfully writing a command to the UPS)
+		 */
+		if (ret <= 0) {
+			upsdebugx(3, "read: %s", ret ? usb_strerror() : "timeout");
+			return ret;
+		}
+	}
+
+	upsdebugx(3, "read: %.*s", (int)strcspn(buf, "\r"), buf);
+	return i;
+}
+
+
 static int phoenix_command(const char *cmd, char *buf, size_t buflen)
 {
 	char	tmp[SMALLBUF];
 	int	ret;
 	size_t	i;
 
+	for (i = 0; i < 8; i++) {
+
+		/* Read data in 8-byte chunks */
+		/* ret = usb->get_interrupt(udev, (unsigned char *)tmp, 8, 1000); */
+		ret = usb_interrupt_read(udev, 0x81, tmp, 8, 1000);
+
+		/*
+		 * This USB to serial implementation is crappy. In order to read correct
+		 * replies we need to flush the output buffers of the converter until we
+		 * get no more data (ie, it times out).
+		 */
+		switch (ret)
+		{
+		case -EPIPE:		/* Broken pipe */
+			usb_clear_halt(udev, 0x81);
+		case -ETIMEDOUT:	/* Connection timed out */
+			break;
+		}
+
+		if (ret < 0) {
+			upsdebugx(3, "flush: %s", usb_strerror());
+			break;
+		}
+
+		upsdebug_hex(4, "dump", tmp, ret);
+	}
+
 	memset(tmp, 0, sizeof(tmp));
 	snprintf(tmp, sizeof(tmp), "%s", cmd);
 
@@ -69,7 +143,7 @@
 		}
 	}
 
-	upsdebugx(3, "send: %.*s", strcspn(tmp, "\r"), tmp);
+	upsdebugx(3, "send: %.*s", (int)strcspn(tmp, "\r"), tmp);
 
 	memset(buf, 0, buflen);
 
@@ -84,16 +158,57 @@
 		 * will happen after successfully writing a command to the UPS)
 		 */
 		if (ret <= 0) {
-			upsdebugx(3, "read: timeout");
-			return 0;
+			upsdebugx(3, "read: %s", ret ? usb_strerror() : "timeout");
+			return ret;
 		}
 	}
 
-	upsdebugx(3, "read: %.*s", strcspn(buf, "\r"), buf);
+	upsdebugx(3, "read: %.*s", (int)strcspn(buf, "\r"), buf);
 	return i;
 }
 
 
+static int ippon_command(const char *cmd, char *buf, size_t buflen)
+{
+	char	tmp[64];
+	int	ret;
+	size_t	i;
+
+	snprintf(tmp, sizeof(tmp), "%s", cmd);
+
+	for (i = 0; i < strlen(tmp); i += ret) {
+
+		/* Write data in 8-byte chunks */
+		ret = usb_control_msg(udev, USB_ENDPOINT_OUT + USB_TYPE_CLASS + USB_RECIP_INTERFACE,
+			0x09, 0x2, 0, &tmp[i], 8, 1000);
+
+		if (ret <= 0) {
+			upsdebugx(3, "send: %s", (ret != -ETIMEDOUT) ? usb_strerror() : "Connection timed out");
+			return ret;
+		}
+	}
+
+	upsdebugx(3, "send: %.*s", (int)strcspn(tmp, "\r"), tmp);
+
+	/* Read all 64 bytes of the reply in one large chunk */
+	ret = usb_interrupt_read(udev, 0x81, tmp, sizeof(tmp), 1000);
+
+	/*
+	 * Any errors here mean that we are unable to read a reply (which
+	 * will happen after successfully writing a command to the UPS)
+	 */
+	if (ret <= 0) {
+		upsdebugx(3, "read: %s", (ret != -ETIMEDOUT) ? usb_strerror() : "Connection timed out");
+		return ret;
+	}
+
+	snprintf(buf, buflen, "%.*s", ret, tmp);
+
+	upsdebugx(3, "read: %.*s", (int)strcspn(buf, "\r"), buf);
+	return ret;
+}
+
+
 static int krauler_command(const char *cmd, char *buf, size_t buflen)
 {
 	/*
@@ -118,9 +233,10 @@
 	};
 
 	int	i;
+	char tbuf[255];	/* Some devices choke on size > 255 */
+
+	upsdebugx(3, "send: %.*s", (int)strcspn(cmd, "\r"), cmd);
 
-	upsdebugx(3, "send: %.*s", strcspn(cmd, "\r"), cmd);
-	
 	for (i = 0; command[i].str; i++) {
 		int	retry;
 
@@ -129,14 +245,35 @@
 		}
 
 		for (retry = 0; retry < 10; retry++) {
-			int	ret;
-
-			ret = usb_get_string_simple(udev, command[i].index, buf, buflen);
+			int	ret, di, si;
+	
+			//ret = usb_get_string_simple(udev, command[i].index, buf, buflen);
+			// Most of the guts of usb_get_string_simple, but eliminated
+			// request for languages - seems to confuse stoopid UPS
+			ret = usb_get_string(udev, command[i].index, 0x0409, tbuf, sizeof(tbuf));
 
 			if (ret <= 0) {
 				upsdebugx(3, "read: %s", ret ? usb_strerror() : "timeout");
 				return ret;
 			}
+			if (tbuf[1] != USB_DT_STRING)
+				return -EIO;
+			
+			if (tbuf[0] > ret)
+				return -EFBIG;
+		    
+			for (di = 0, si = 2; si < tbuf[0]; si += 2)
+			{
+				if (di >= (buflen - 1))
+					break;
+		
+				if (tbuf[si + 1])	/* high byte */
+					buf[di++] = '?';
+				else
+					buf[di++] = tbuf[si];
+			}
+		
+			buf[di] = 0;
 
 			/* "UPS No Ack" has a special meaning */
 			if (!strcasecmp(buf, "UPS No Ack")) {
@@ -149,19 +286,26 @@
 			return ret;
 		}
 
-		upsdebugx(3, "read: %.*s", strcspn(buf, "\r"), buf);
+		upsdebugx(3, "read: %.*s", (int)strcspn(buf, "\r"), buf);
 		return 0;
 	}
 
 	/* echo the unknown command back */
-	upsdebugx(3, "read: %.*s", strcspn(cmd, "\r"), cmd);
+	upsdebugx(3, "read: %.*s", (int)strcspn(cmd, "\r"), cmd);
 	return snprintf(buf, buflen, "%s", cmd);
 }
 
 
-static void *phoenix_subdriver(void)
+static void *cypress_subdriver(void)
+{
+	subdriver_command = &cypress_command;
+	return NULL;
+}
+
+
+static void *ippon_subdriver(void)
 {
-	subdriver_command = &phoenix_command;
+	subdriver_command = &ippon_command;
 	return NULL;
 }
 
@@ -174,12 +318,12 @@
 
 
 static usb_device_id_t blazer_usb_id[] = {
-	{ USB_DEVICE(0x05b8, 0x0000), &phoenix_subdriver },	/* Agiler UPS */
+	{ USB_DEVICE(0x05b8, 0x0000), &cypress_subdriver },	/* Agiler UPS */
 	{ USB_DEVICE(0x0001, 0x0000), &krauler_subdriver },	/* Krauler UP-M500VA */
 	{ USB_DEVICE(0xffff, 0x0000), &krauler_subdriver },	/* Ablerex 625L USB */
-	{ USB_DEVICE(0x0665, 0x5161), &phoenix_subdriver },	/* Belkin F6C1200-UNV */
-	{ USB_DEVICE(0x06da, 0x0003), &phoenix_subdriver },	/* Mustek Powermust */
-	{ USB_DEVICE(0x0f03, 0x0001), &phoenix_subdriver },	/* Unitek Alpha 1200Sx */
+	{ USB_DEVICE(0x0665, 0x5161), &cypress_subdriver },	/* Belkin F6C1200-UNV */
+	{ USB_DEVICE(0x06da, 0x0003), &ippon_subdriver },	/* Mustek Powermust */
+	{ USB_DEVICE(0x0f03, 0x0001), &cypress_subdriver },	/* Unitek Alpha 1200Sx */
 	/* end of list */
 	{-1, -1, NULL}
 };
@@ -236,27 +380,38 @@
 
 	switch (ret)
 	{
-	case -EBUSY:
+	case -EBUSY:		/* Device or resource busy */
 		fatal_with_errno(EXIT_FAILURE, "Got disconnected by another driver");
 
-	case -EPERM:
+	case -EPERM:		/* Operation not permitted */
 		fatal_with_errno(EXIT_FAILURE, "Permissions problem");
 
-	case -EPIPE:
-		if (!usb_clear_halt(udev, 0x81)) {
-			/* stall condition cleared */
+	case -EPIPE:		/* Broken pipe */
+		if (usb_clear_halt(udev, 0x81) == 0) {
+			upsdebugx(1, "Stall condition cleared");
 			break;
 		}
-	case -ENODEV:
-	case -EACCES:
-	case -EIO:
-	case -ENOENT:
-	case -ETIMEDOUT:
-	default:
+#ifdef ETIME
+	case -ETIME:		/* Timer expired */
+#endif
+		if (usb_reset(udev) == 0) {
+			upsdebugx(1, "Device reset handled");
+		}
+	case -ENODEV:		/* No such device */
+	case -EACCES:		/* Permission denied */
+	case -EIO:		/* I/O error */
+	case -ENXIO:		/* No such device or address */
+	case -ENOENT:		/* No such file or directory */
 		/* Uh oh, got to reconnect! */
 		usb->close(udev);
 		udev = NULL;
 		break;
+
+	case -ETIMEDOUT:	/* Connection timed out */
+	case -EOVERFLOW:	/* Value too large for defined data type */
+	case -EPROTO:		/* Protocol error */
+	default:
+		break;
 	}
 
 	return ret;
@@ -318,7 +473,9 @@
 		const char	*name;
 		int		(*command)(const char *cmd, char *buf, size_t buflen);
 	} subdriver[] = {
+		{ "cypress", &cypress_command },
 		{ "phoenix", &phoenix_command },
+		{ "ippon", &ippon_command },
 		{ "krauler", &krauler_command },
 		{ NULL }
 	};
@@ -369,12 +526,13 @@
 		fatalx(EXIT_FAILURE, "invalid regular expression: %s", regex_array[ret]);
 	}
 
+
 	/* link the matchers */
 	regex_matcher->next = &device_matcher;
 
 	ret = usb->open(&udev, &usbdevice, regex_matcher, NULL);
 	if (ret < 0) {
-		fatalx(EXIT_FAILURE, 
+		fatalx(EXIT_FAILURE,
 			"No supported devices found. Please check your device availability with 'lsusb'\n"
 			"and make sure you have an up-to-date version of NUT. If this does not help,\n"
 			"try running the driver with at least 'subdriver', 'vendorid' and 'productid'\n"
@@ -394,6 +552,9 @@
 
 	/* link the matchers */
 	reopen_matcher->next = regex_matcher;
+
+	dstate_setinfo("ups.vendorid", "%04x", usbdevice.VendorID);
+	dstate_setinfo("ups.productid", "%04x", usbdevice.ProductID);
 #endif
 	blazer_initups();
 }
@@ -402,9 +563,6 @@
 void upsdrv_initinfo(void)
 {
 	blazer_initinfo();
-
-	dstate_setinfo("ups.vendorid", "%04x", usbdevice.VendorID);
-	dstate_setinfo("ups.productid", "%04x", usbdevice.ProductID);
 }
 
 
