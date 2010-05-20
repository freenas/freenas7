--- tools/nut-usbinfo.pl.orig	2010-02-11 22:43:12.000000000 +0100
+++ tools/nut-usbinfo.pl	2010-05-18 07:29:36.000000000 +0200
@@ -82,7 +82,7 @@
 	print $outUdev 'ACTION!="add", GOTO="nut-usbups_rules_end"'."\n";
 	print $outUdev 'SUBSYSTEM=="usb_device", GOTO="nut-usbups_rules_real"'."\n";
 	print $outUdev 'SUBSYSTEM=="usb", GOTO="nut-usbups_rules_real"'."\n";
-	print $outUdev 'BUS!="usb", GOTO="nut-usbups_rules_end"'."\n\n";
+	print $outUdev 'SUBSYSTEM!="usb", GOTO="nut-usbups_rules_end"'."\n\n";
 	print $outUdev 'LABEL="nut-usbups_rules_real"'."\n";
 
 	# DeviceKit-power file header
