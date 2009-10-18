--- src/lib/UPnPActions/UPnPActionFactory.cpp	2009/07/19 11:33:14	640
+++ src/lib/UPnPActions/UPnPActionFactory.cpp	2009/07/21 09:07:48	641
@@ -38,9 +38,11 @@
 {
   xmlDocPtr pDoc = NULL;
   pDoc = xmlReadMemory(p_sContent.c_str(), p_sContent.length(), "", NULL, 0);
-  if(!pDoc)
+  if(!pDoc) {
     cout << "error parsing action" << endl;
-   
+		return NULL;
+  }
+	
   xmlNode* pRootNode = NULL;  
   xmlNode* pTmpNode  = NULL;   
   pRootNode = xmlDocGetRootElement(pDoc);
@@ -65,7 +67,11 @@
   }
 
   
-  CUPnPAction* pAction = NULL;  
+  CUPnPAction* pAction = NULL;
+	if(!pTmpNode->nsDef) {
+		cout << "malformed xml" << endl;
+		return NULL;
+	}
   string sNs   = (char*)pTmpNode->nsDef->href;
 	string sName = (char*)pTmpNode->name;
 
@@ -180,9 +186,13 @@
     sRxObjId = "<ContainerID.*>(.+)</ContainerID>";
   }    
   RegEx rxObjId(sRxObjId.c_str());
-  if (rxObjId.Search(pAction->GetContent().c_str()))
+  if(rxObjId.Search(pAction->GetContent().c_str())) {
+		if(strlen(rxObjId.Match(1)) > 10)
+			return false;
+
     pAction->m_sObjectId = rxObjId.Match(1);
-  
+  }
+		
   /* Browse flag */
   pAction->m_nBrowseFlag = UPNP_BROWSE_FLAG_UNKNOWN;    
   RegEx rxBrowseFlag("<BrowseFlag.*>(.+)</BrowseFlag>");
