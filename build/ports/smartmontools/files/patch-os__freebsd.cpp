--- os_freebsd.cpp.orig	2009-01-11 03:42:16.000000000 +0000
+++ os_freebsd.cpp	2009-01-11 03:42:18.000000000 +0000
@@ -272,7 +272,6 @@
 }
 
 int highpoint_command_interface(__unused int fd, __unused smart_command_set command, __unused int select, __unused char *data) {
-{
   return -1;
 }
 
@@ -391,6 +390,20 @@
     request.u.ata.lba=0xc24f<<8;
     request.flags=ATA_CMD_CONTROL;
     break;
+  case CHECK_POWER_MODE:
+    request.u.ata.command=ATA_CHECK_POWER_MODE;
+    request.u.ata.feature=0;
+    request.flags=ATA_CMD_CONTROL;
+    break;
+  case WRITE_LOG:
+    memcpy(buff, data, 512);
+    request.u.ata.feature=ATA_SMART_WRITE_LOG_SECTOR;
+    request.u.ata.lba=select|(0xc24f<<8);
+    request.u.ata.count=1;
+    request.flags=ATA_CMD_WRITE;
+    request.data=(char *)buff;
+    request.count=512;
+    break;
   default:
     pout("Unrecognized command %d in ata_command_interface()\n"
          "Please contact " PACKAGE_BUGREPORT "\n", command);
@@ -448,6 +461,10 @@
     return -1;
   }
   // 
+  if (command == CHECK_POWER_MODE) {
+    data[0] = request.u.ata.count & 0xff;
+    return 0;
+  }
   if (copydata)
     memcpy(data, buff, 512);
   
@@ -924,6 +941,7 @@
 static const char * fbsd_dev_prefix = "/dev/";
 static const char * fbsd_dev_ata_disk_prefix = "ad";
 static const char * fbsd_dev_scsi_disk_plus = "da";
+static const char * fbsd_dev_scsi_pass = "pass";
 static const char * fbsd_dev_scsi_tape1 = "sa";
 static const char * fbsd_dev_scsi_tape2 = "nsa";
 static const char * fbsd_dev_scsi_tape3 = "esa";
@@ -960,6 +978,11 @@
     return CONTROLLER_ATA;
   }
   
+  // form /dev/pass* or pass*
+  if (!strncmp(fbsd_dev_scsi_pass, dev_name,
+               strlen(fbsd_dev_scsi_pass)))
+    goto handlescsi;
+
   // form /dev/da* or da*
   if (!strncmp(fbsd_dev_scsi_disk_plus, dev_name,
                strlen(fbsd_dev_scsi_disk_plus)))
