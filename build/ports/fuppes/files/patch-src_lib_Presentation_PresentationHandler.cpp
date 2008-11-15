--- src/lib/Presentation/PresentationHandler.cpp.orig	2007-11-23 07:56:59.000000000 +0000
+++ src/lib/Presentation/PresentationHandler.cpp	2008-11-15 20:15:56.000000000 +0000
@@ -3,7 +3,7 @@
  *
  *  FUPPES - Free UPnP Entertainment Service
  *
- *  Copyright (C) 2005 - 2007 Ulrich Völkel <u-voelkel@users.sourceforge.net>
+ *  Copyright (C) 2005 - 2007 Ulrich VÃ¶lkel <u-voelkel@users.sourceforge.net>
  ****************************************************************************/
 
 /*
@@ -324,7 +324,7 @@
 {
   std::stringstream sResult;
   
-  sResult << "<h1>system information</h1>" << endl;  
+  sResult << "<h1>System information</h1>" << endl;  
   
   sResult << "<p>" << endl;
   sResult << "Version: " << CSharedConfig::Shared()->GetAppVersion() << "<br />" << endl;
@@ -334,8 +334,8 @@
   sResult << "</p>" << endl;
   
   sResult << "<p>" << endl;
-  sResult << "build at: " << __DATE__ << " - " << __TIME__ "<br />" << endl;
-  sResult << "build with: " << __VERSION__ << endl;
+  sResult << "Build at: " << __DATE__ << " - " << __TIME__ "<br />" << endl;
+  sResult << "Build with: " << __VERSION__ << endl;
   sResult << "</p>" << endl;
   
   sResult << "<p>" << endl;
@@ -343,7 +343,7 @@
   sResult << "</p>" << endl;
   
   
-  sResult << "<h1>remote devices</h1>";
+  sResult << "<h1>Remote devices</h1>";
   sResult << BuildFuppesDeviceList(CSharedConfig::Shared()->GetFuppesInstance(0), p_sImgPath);
   
   return sResult.str();
@@ -382,17 +382,17 @@
   /*sResult << "<a href=\"http://sourceforge.net/projects/fuppes/\">http://sourceforge.net/projects/fuppes/</a><br />" << endl; */
 
   //((CFuppes*)m_vFuppesInstances[0])->GetContentDirectory()->BuildDB();
-  sResult << "<h1>database options</h1>" << endl;
+  sResult << "<h1>Database options</h1>" << endl;
   if(!CContentDatabase::Shared()->IsRebuilding() && !CVirtualContainerMgr::Shared()->IsRebuilding())  {
-    sResult << "<a href=\"/presentation/options.html?db=rebuild\">rebuild database</a><br />" << endl;
-    sResult << "<a href=\"/presentation/options.html?db=update\">update database</a><br />" << endl;
-		sResult << "<a href=\"/presentation/options.html?vcont=rebuild\">rebuild virtual container</a>" << endl;
+    sResult << "<a href=\"/presentation/options.html?db=rebuild\">Rebuild database</a><br />" << endl;
+    sResult << "<a href=\"/presentation/options.html?db=update\">Update database</a><br />" << endl;
+		sResult << "<a href=\"/presentation/options.html?vcont=rebuild\">Rebuild virtual container</a>" << endl;
   }
   else {
 		if(CContentDatabase::Shared()->IsRebuilding())
-			sResult << "database rebuild/update in progress" << endl;
+			sResult << "Database Rebuild/Update in progress, This may take a few seconds" << endl;
 		else if(CVirtualContainerMgr::Shared()->IsRebuilding())
-			sResult << "virtual container rebuild in progress" << endl;
+			sResult << "Virtual Container rebuild in progress" << endl;
 	}
   
   return sResult.str();
@@ -413,7 +413,7 @@
   OBJECT_TYPE nType = OBJECT_TYPE_UNKNOWN;  
   
   // Database status
-  sResult << "<h1>database status</h1>" << endl;  
+  sResult << "<h1>Database status</h1>" << endl;  
   sResult << 
     "<table rules=\"all\" style=\"font-size: 10pt; border-style: solid; border-width: 1px; border-color: #000000;\" cellspacing=\"0\" width=\"400\">" << endl <<
       "<thead>" << endl <<
@@ -448,13 +448,13 @@
   
   
   string sTranscoding;
-  sResult << "<h1>transcoding</h1>";
+  sResult << "<h1>Transcoding</h1>";
   CTranscodingMgr::Shared()->PrintTranscodingSettings(&sTranscoding);
   sResult << sTranscoding;
   
   
   
-  sResult << "<h1>build options</h1>" <<
+  sResult << "<h1>Build options</h1>" <<
   "<table>" <<    
     "<tr>" <<
       "<th>option</th>" <<
@@ -565,7 +565,7 @@
   "</table>";    
   
   // system status
-  sResult << "<h1>system status</h1>" << endl;  
+  sResult << "<h1>System status</h1>" << endl;  
   
   sResult << "<p>" << endl;
   sResult << "UUID: " << CSharedConfig::Shared()->GetFuppesInstance(0)->GetUUID() << "<br />";    
@@ -574,7 +574,7 @@
   
   
   // device settings
-  sResult << "<h1>device settings</h1>" << endl;
+  sResult << "<h1>Device settings</h1>" << endl;
   string sDeviceSettings;
   CDeviceIdentificationMgr::Shared()->PrintSettings(&sDeviceSettings);
   sResult << sDeviceSettings << endl;
@@ -676,7 +676,7 @@
   sResult << "<form method=\"POST\" action=\"/presentation/config.html\" enctype=\"text/plain\" accept-charset=\"UTF-8\">" << endl;  //  
   
   // shared objects
-  sResult << "<h2>shared objects</h2>" << endl;
+  sResult << "<h2>Shared objects</h2>" << endl;
   
   // object list
   sResult << "<p>" << endl <<  
