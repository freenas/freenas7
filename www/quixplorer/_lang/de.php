<?php

// German Language Module for v2.3 (translated by the QuiX project)

$GLOBALS["charset"] = "iso-8859-1";
$GLOBALS["text_dir"] = "ltr"; // ('ltr' for left to right, 'rtl' for right to left)
$GLOBALS["date_fmt"] = "d.m.Y H:i";
$GLOBALS["error_msg"] = array(
	// error
	"error"			=> "FEHLER",
	"back"			=> "Zurück",
	
	// root
	"home"			=> "Das Home-Verzeichnis existiert nicht, kontrollieren sie ihre Einstellungen.",
	"abovehome"		=> "Das aktuelle Verzeichnis darf nicht höher liegen als das Home-Verzeichnis.",
	"targetabovehome"	=> "Das Zielverzeichnis darf nicht höher liegen als das Home-Verzeichnis.",
	
	// exist
	"direxist"		=> "Dieses Verzeichnis existiert nicht.",
	//"filedoesexist"	=> "Diese Datei existiert bereits.",
	"fileexist"		=> "Diese Datei existiert nicht.",
	"itemdoesexist"		=> "Dieses Objekt existiert bereits.",
	"itemexist"		=> "Dieses Objekt existiert nicht.",
	"targetexist"		=> "Das Zielverzeichnis existiert nicht.",
	"targetdoesexist"	=> "Das Zielobjekt existiert bereits.",
	
	// open
	"opendir"		=> "Kann Verzeichnis nicht öffnen.",
	"readdir"		=> "Kann Verzeichnis nicht lesen",
	
	// access
	"accessdir"		=> "Zugriff auf dieses Verzeichnis verweigert.",
	"accessfile"		=> "Zugriff auf diese Datei verweigert.",
	"accessitem"		=> "Zugriff auf dieses Objekt verweigert.",
	"accessfunc"		=> "Zugriff auf diese Funktion verweigert.",
	"accesstarget"		=> "Zugriff auf das Zielverzeichnis verweigert.",
	
	// actions
	"permread"		=> "Rechte lesen fehlgeschlagen.",
	"permchange"		=> "Rechte ändern fehlgeschlagen.",
	"openfile"		=> "Datei öffnen fehlgeschlagen.",
	"savefile"		=> "Datei speichern fehlgeschlagen.",
	"createfile"		=> "Datei anlegen fehlgeschlagen.",
	"createdir"		=> "Verzeichnis anlegen fehlgeschlagen.",
	"uploadfile"		=> "Datei hochladen fehlgeschlagen.",
	"copyitem"		=> "Kopieren fehlgeschlagen.",
	"moveitem"		=> "Versetzen fehlgeschlagen.",
	"delitem"		=> "Löschen fehlgeschlagen.",
	"chpass"		=> "Passwort ändern fehlgeschlagen.",
	"deluser"		=> "Benutzer löschen fehlgeschlagen.",
	"adduser"		=> "Benutzer hinzufügen fehlgeschlagen.",
	"saveuser"		=> "Benutzer speichern fehlgeschlagen.",
	"searchnothing"		=> "Sie müssen etwas zum suchen eintragen.",
	
	// misc
	"miscnofunc"		=> "Funktion nicht vorhanden.",
	"miscfilesize"		=> "Datei ist größer als die maximale Größe.",
	"miscfilepart"		=> "Datei wurde nur zum Teil hochgeladen.",
	"miscnoname"		=> "Sie müssen einen Namen eintragen",
	"miscselitems"		=> "Sie haben keine Objekt(e) ausgewählt.",
	"miscdelitems"		=> "Sollen die \"+num+\" markierten Objekt(e) gelöscht werden?",
	"miscdeluser"		=> "Soll der Benutzer '\"+user+\"' gelöscht werden?",
	"miscnopassdiff"	=> "Das neue und das heutige Passwort sind nicht verschieden.",
	"miscnopassmatch"	=> "Passwörter sind nicht gleich.",
	"miscfieldmissed"	=> "Sie haben ein wichtiges Eingabefeld vergessen auszufüllen",
	"miscnouserpass"	=> "Benutzer oder Passwort unbekannt.",
	"miscselfremove"	=> "Sie können sich selbst nicht löschen.",
	"miscuserexist"		=> "Der Benutzer existiert bereits.",
	"miscnofinduser"	=> "Kann Benutzer nicht finden.",
);
$GLOBALS["messages"] = array(
	// links
	"permlink"		=> "RECHTE ÄNDERN",
	"editlink"		=> "BEARBEITEN",
	"downlink"		=> "HERUNTERLADEN",
	"uplink"		=> "HÖHER",
	"homelink"		=> "HOME",
	"reloadlink"		=> "ERNEUERN",
	"copylink"		=> "KOPIEREN",
	"movelink"		=> "VERSETZEN",
	"dellink"		=> "LÖSCHEN",
	"comprlink"		=> "ARCHIVIEREN",
	"adminlink"		=> "ADMINISTRATION",
	"logoutlink"		=> "ABMELDEN",
	"uploadlink"		=> "HOCHLADEN",
	"searchlink"		=> "SUCHEN",
	
	// list
	"nameheader"		=> "Name",
	"sizeheader"		=> "Größe",
	"typeheader"		=> "Typ",
	"modifheader"		=> "Geändert",
	"permheader"		=> "Rechte",
	"actionheader"		=> "Aktionen",
	"pathheader"		=> "Pfad",
	
	// buttons
	"btncancel"		=> "Abbrechen",
	"btnsave"		=> "Speichern",
	"btnchange"		=> "Ändern",
	"btnreset"		=> "Zurücksetzen",
	"btnclose"		=> "Schließen",
	"btncreate"		=> "Anlegen",
	"btnsearch"		=> "Suchen",
	"btnupload"		=> "Hochladen",
	"btncopy"		=> "Kopieren",
	"btnmove"		=> "Verschieben",
	"btnlogin"		=> "Anmelden",
	"btnlogout"		=> "Abmelden",
	"btnadd"		=> "Hinzufügen",
	"btnedit"		=> "Ändern",
	"btnremove"		=> "Löschen",
	
	// actions
	"actdir"		=> "Verzeichnis",
	"actperms"		=> "Rechte ändern",
	"actedit"		=> "Datei bearbeiten",
	"actsearchresults"	=> "Suchergebnisse",
	"actcopyitems"		=> "Objekt(e) kopieren",
	"actcopyfrom"		=> "Kopiere von /%s nach /%s ",
	"actmoveitems"		=> "Objekt(e) verschieben",
	"actmovefrom"		=> "Versetze von /%s nach /%s ",
	"actlogin"		=> "Anmelden",
	"actloginheader"	=> "Melden sie sich an um QuiXplorer zu benutzen",
	"actadmin"		=> "Administration",
	"actchpwd"		=> "Passwort ändern",
	"actusers"		=> "Benutzer",
	"actarchive"		=> "Objekt(e) archivieren",
	"actupload"		=> "Datei(en) hochladen",
	
	// misc
	"miscitems"		=> "Objekt(e)",
	"miscfree"		=> "Freier Speicher",
	"miscusername"		=> "Benutzername",
	"miscpassword"		=> "Passwort",
	"miscoldpass"		=> "Altes Passwort",
	"miscnewpass"		=> "Neues Passwort",
	"miscconfpass"		=> "Bestätige Passwort",
	"miscconfnewpass"	=> "Bestätige neues Passwort",
	"miscchpass"		=> "Ändere Passwort",
	"mischomedir"		=> "Home-Verzeichnis",
	"mischomeurl"		=> "Home URL",
	"miscshowhidden"	=> "Versteckte Objekte anzeigen",
	"mischidepattern"	=> "Versteck-Filter",
	"miscperms"		=> "Rechte",
	"miscuseritems"		=> "(Name, Home-Verzeichnis, versteckte Objekte anzeigen, Rechte, aktiviert)",
	"miscadduser"		=> "Benutzer hinzufügen",
	"miscedituser"		=> "Benutzer '%s' ändern",
	"miscactive"		=> "Aktiviert",
	"misclang"		=> "Sprache",
	"miscnoresult"		=> "Suche ergebnislos.",
	"miscsubdirs"		=> "Suche in Unterverzeichnisse",
	"miscpermnames"		=> array("Nur ansehen","Ändern","Passwort ändern",
					"Ändern & Passwort ändern","Administrator"),
	"miscyesno"		=> array("Ja","Nein","J","N"),
	"miscchmod"		=> array("Besitzer", "Gruppe", "Publik"),
);
?>