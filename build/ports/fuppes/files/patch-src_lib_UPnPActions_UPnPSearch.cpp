--- src/lib/UPnPActions/UPnPSearch.cpp.orig	2009-02-27 11:01:57.000000000 +0100
+++ src/lib/UPnPActions/UPnPSearch.cpp	2009-02-27 11:09:43.000000000 +0100
@@ -3,13 +3,14 @@
  *
  *  FUPPES - Free UPnP Entertainment Service
  *
- *  Copyright (C) 2007 Ulrich Völkel <u-voelkel@users.sourceforge.net>
+ *  Copyright (C) 2007-2008 Ulrich Völkel <u-voelkel@users.sourceforge.net>
  ****************************************************************************/
 
 /*
  *  This program is free software; you can redistribute it and/or modify
- *  it under the terms of the GNU General Public License version 2 as 
- *  published by the Free Software Foundation.
+ *  it under the terms of the GNU General Public License
+ *  as published by the Free Software Foundation; either version 2
+ *  of the License, or (at your option) any later version.
  *
  *  This program is distributed in the hope that it will be useful,
  *  but WITHOUT ANY WARRANTY; without even the implied warranty of
@@ -211,6 +212,14 @@
 				  sProp = "d.A_ARTIST";
 					bNumericProp = false;
 				}
+				else if(sProp.compare("upnp:album") == 0) {
+					sProp = "d.A_ALBUM";
+					bNumericProp = false;
+				}
+				else if(sProp.compare("upnp:genre") == 0) {
+				  sProp = "d.A_GENRE";
+					bNumericProp = false;
+				}
         else if(sProp.compare("res:protocolInfo") == 0) {
 				  sPrevLog = sLogOp;
 					continue;
@@ -250,7 +259,9 @@
           sOp = "in";
           
           #warning todo: use values from ContentDatabase.h
-				  if(sVal.compare("object.item.imageItem") == 0)
+				  /*if(sVal.compare("object.item") == 0)
+						
+					else*/ if(sVal.compare("object.item.imageItem") == 0)
 					  sVal = "(110, 111)";
 					else if(sVal.compare("object.item.audioItem") == 0)
 					  sVal = "(120, 121, 122)";	
