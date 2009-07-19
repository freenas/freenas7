--- src/lib/SSDP/UDPSocket.cpp.orig	2009-07-19 13:36:43.000000000 +0000
+++ src/lib/SSDP/UDPSocket.cpp	2009-07-19 16:53:28.000000000 +0000
@@ -85,7 +85,7 @@
 	flag = 1;
 	ret = setsockopt(m_Socket, SOL_SOCKET, SO_REUSEPORT, &flag, sizeof(flag));
 	if(ret == -1) {
-		throw Exception(__FILE__, __LINE__, "failed to setsockopt: SO_REUSEPORT");
+		throw fuppes::Exception(__FILE__, __LINE__, "failed to setsockopt: SO_REUSEPORT");
 	}
 	#endif
 	
