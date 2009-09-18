--- src/lib/ContentDirectory/VirtualContainerMgr.cpp.orig	2009-01-23 07:43:02.000000000 +0000
+++ src/lib/ContentDirectory/VirtualContainerMgr.cpp	2009-01-23 07:43:10.000000000 +0000
@@ -376,12 +376,12 @@
       case CONTAINER_PERSON_MUSIC_ARTIST:
         pDetails->sArtist = SQLEscape(pDb->GetResult()->GetValue("VALUE"));
         // artist does not necessarily has exactly 1 genre
-        //pDetails->sGenre  = SQLEscape(pDb->GetResult()->GetValue("d.A_GENRE"));
+        //pDetails->sGenre  = SQLEscape(pDb->GetResult()->GetValue("A_GENRE"));
         break;
       case CONTAINER_ALBUM_MUSIC_ALBUM:
         pDetails->sAlbum  = SQLEscape(pDb->GetResult()->GetValue("VALUE"));
-        pDetails->sArtist = SQLEscape(pDb->GetResult()->GetValue("d.A_ARTIST"));
-        pDetails->sGenre  = SQLEscape(pDb->GetResult()->GetValue("d.A_GENRE"));
+        pDetails->sArtist = SQLEscape(pDb->GetResult()->GetValue("A_ARTIST"));
+        pDetails->sGenre  = SQLEscape(pDb->GetResult()->GetValue("A_GENRE"));
         break;      
       default:
         cout << "unhandled property '" << sProp << "'" << endl;
