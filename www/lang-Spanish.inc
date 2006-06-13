<?php 
/*
	lang-Spanish.php
	part of FreeNAS (http://www.freenas.org)
	Copyright (C) 2005-2006 Olivier Cochard <olivier@freenas.org>.
	All rights reserved.
	
	Based on m0n0wall (http://m0n0.ch/wall)
	Copyright (C) 2003-2006 Manuel Kasper <mk@neon1.net>.
	All rights reserved.
	
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met:
	
	1. Redistributions of source code must retain the above copyright notice,
	   this list of conditions and the following disclaimer.
	
	2. Redistributions in binary form must reproduce the above copyright
	   notice, this list of conditions and the following disclaimer in the
	   documentation and/or other materials provided with the distribution.
	
	THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED WARRANTIES,
	INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY
	AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
	AUTHOR BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
	OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
	SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
	INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
	CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
	ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
	POSSIBILITY OF SUCH DAMAGE.
*/

// locale defines
define('_CHARSET','ISO-8859-1');
define('_LOCALE','en_US');
define('_LOCALEWIN', 'eng');

// date and time defines
define('_DATE','Date');
define('_DATEBRIEF','%b %d, %Y');
define('_DATELONG','%A, %B %d, %Y');
define('_DATESTRING','%A, %B %d @ %H:%M:%S');
define('_DATETIMEBRIEF','%b %d, %Y - %I:%M %p');
define('_DATETIMELONG','%A, %B %d, %Y - %I:%M %p');
define('_DAY_OF_WEEK_LONG','Sunday Monday Tuesday Wednesday Thursday Friday Saturday');
define('_DAY_OF_WEEK_SHORT','Sun Mon Tue Wed Thu Fri Sat');
define('_MONTH_LONG','January February March April May June July August September October November December');
define('_MONTH_SHORT','Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec');
define('_TIME', 'Time');
define('_TIMEBRIEF','%I:%M %p');
define('_SECOND', 'second');
define('_SECONDS', 'seconds');
define('_MINUTE', 'minute');
define('_MINUTES', 'minutes');
define('_DAY', 'day');
define('_DAYS', 'days');
define('_WEEK', 'week');
define('_WEEKS', 'weeks');
define('_MONTH', 'month');
define('_MONTHS', 'months');
define('_YEAR', 'year');
define('_YEARS', 'years');

// common words
define('_ALL','All');
define('_AND','and');
define('_BY','by');
define('_DOWN','Down');
define('_NO','No');
define('_OF','of');
define('_OK','OK');
define('_ON','on');
define('_TO','To');
define('_THE', 'The');
define('_UP','Up');
define('_URL', 'URL');
define('_YES','Yes');

// common actions
define('_ACTIVATE','Activar');
define('_ADD','Add');
define('_CANCEL', 'Cancelar');
define('_COMMIT', 'Update');
define('_CREATE', 'Crear');
define('_DEACTIVATE','Deactivate');
define('_DELETE','Borrar');
define('_EDIT','Editar');
define('_HELP', 'Ayuda');
define('_IGNORE','Ignorar');
define('_LOGIN','Log in');
define('_LOGOUT','Log out');
define('_MODIFY','Modificar');
define('_NEW','Nuevo');
define('_SEARCH', 'Buscar');
define('_SAVE', 'Guardar');
define('_SUBMIT','Enviar');
define('_UPDATE', 'Actualizar');
define('_VIEW', 'Mostrar');
define('_APPLY', 'Aplicar Cambios');
define('_WARNING', 'Precaución');

// common messages
define('_MESSAGES_SUCCESS', 'Los cambios han sido aplicados satisfactoriamente.');
define('_MESSAGES_SUCCESSWITHREBOOT', 'Los cambios han sido aplicados. Tiene que <a href=\"/reboot.php\">reiniciar</a> su NAS para que los cambios tengan efecto.');
define('_MESSAGES_ERROR', 'ERROR: los cambios no pudieron ser aplicados(código de error '.$ok.').');

// states
define('_ACTIVE','Active');
define('_INACTIVE','Inactive');

// Webgui layout
// Webgui layout
define('_MENULEFT_SYSTEM','Sistema');
define('_MENULEFT_SYSGENSETUP','Configuración');
define('_MENULEFT_SYSSTATROUTES','Rutas Estáticas');
define('_MENULEFT_SYSFIRMWARE','Sistema base');
define('_MENULEFT_SYSADVANCED','Avanzado');
define('_MENULEFT_INTERFACES','Interfaces');
define('_MENULEFT_INTASSIGN','Asignar');
define('_MENULEFT_INTLAN','LAN');
define('_MENULEFT_DISKS','Discos');
define('_MENULEFT_DISKMANAG','Administrar');
define('_MENULEFT_DISKMNGSOFTRAID','Software RAID');
define('_MENULEFT_DISKMNGMOUNTPOINT','Punto de Montaje');
define('_MENULEFT_SERVICES','Servicios');
define('_MENULEFT_SRVCIFS','CIFS');
define('_MENULEFT_SRVFTP','FTP');
define('_MENULEFT_SRVNFS','NFS');
define('_MENULEFT_SRVRSYNCD','RSYNCD');
define('_MENULEFT_SRVSSHD','SSHD');
define('_MENULEFT_SRVAFP','AFP');
define('_MENULEFT_ACCESS','Acceso');
define('_MENULEFT_ACUSERGROUPS','Usuarios y Grupos');
define('_MENULEFT_ACMSAD','Active Directory');
define('_MENULEFT_ACNIS','NIS');
define('_MENULEFT_ACRADIUS','Radius');
define('_MENULEFT_STATUS','Estado');
define('_MENULEFT_STSYSTEM','Sistema');
define('_MENULEFT_STPROCESS','Procesos');
define('_MENULEFT_STINTERFACES','Interfaces');
define('_MENULEFT_STDISKS','Discos');
define('_MENULEFT_STWIRELESS','Inalámbrico');
define('_MENULEFT_EXTENSIONS','Extensiones');
define('_MENULEFT_DIAGNOSTICS','Diagnóstico');
define('_MENULEFT_DIAGLOGS','Registros');
define('_MENULEFT_DIAGINF','Información');
define('_MENULEFT_DIAGPINGTRACE','Ping/Traceroute');
define('_MENULEFT_DIAGARPTAB','Tabla ARP');
define('_MENULEFT_DIAGEXEC','Ejecutar comando');
define('_MENULEFT_CMDBACKUPRESTORE','Salvar/Restaurar');
define('_MENULEFT_CMDFACTORYDEF','Valores de Frábica');
define('_MENULEFT_CMDREBOOTSYS','Reiniciar Sistema');
define('_MENULEFT_CMDSHUTDOWNSYS','Apagar Sistema');

// Module specific stuff

// System General setup Module
// file: system.php
define('_SYSTEMPHP_MODULE_NAME','Sistema');
define('_SYSTEMPHP_MODULE_NAME_DESCRIPTION','Configuración General');
define('_SYSTEMPHP_HOSTNAME','Nombre de Host');
define('_SYSTEMPHP_HOSTNAMETEXT','nombre del host, sin la parte del dominio<br>e.j. <em>nas');
define('_SYSTEMPHP_DOMAIN','Dominio');
define('_SYSTEMPHP_DOMAINTEXT','e.j. <em>micorp.com</em>');
define('_SYSTEMPHP_DNS','Servidores DNS');
define('_SYSTEMPHP_DNSTEXT','Direcciones IP<br>');
define('_SYSTEMPHP_USERNAME','Usuario');
define('_SYSTEMPHP_USERNAMETEXT','Si desa cambiar el usuario para acceder la WebGUI, introdúscalo aquí.');
define('_SYSTEMPHP_PASSWORD','Contraseña');
define('_SYSTEMPHP_PASSWORDTEXT','Si desea cambiar la contraseña para acceder la webGUI, introdúscala aquí dos veces.');
define('_SYSTEMPHP_WEBGUIPROTOCOL','Protocolo de la WebGUI');
define('_SYSTEMPHP_WEBGUIPROTOCOLTEXT','');
define('_SYSTEMPHP_WEBGUIPORT','Puerto de la WebGUI');
define('_SYSTEMPHP_WEBGUIPORTTEXT','Introduzca un puerto personalizado si desea reemplazar los valores por defecto (80 para HTTP, 443 para HTTPS).');
define('_SYSTEMPHP_LANGUAGE','Idioma');
define('_SYSTEMPHP_LANGUAGETEXT','Seleccione el idioma del sistema');
define('_SYSTEMPHP_TIMEZONE','Zona horaria');
define('_SYSTEMPHP_TIMEZONETEXT','Seleccione el lugar más cerca a usted');
define('_SYSTEMPHP_TIMEUPDATEINT','Intervalo de actualización de tiempo');
define('_SYSTEMPHP_TIMEUPDATEINTTEXT','Minutos entre la sincronización de tiempo por la red.; 300	recomendado, o 0 para deshabilitar');
define('_SYSTEMPHP_NTPSERVER','Servidor NTP');
define('_SYSTEMPHP_NTPSERVERTEXT','Use espacio para separar varios servidores (solo se requiere uno). Recuerde configurar al menos un servidor DNS si introduce un nombre aquí!');

// System Information Module
// file: index.php
define('_INDEXPHP_TITLE','Información del Sistema');
define('_INDEXPHP_NAME','Nombre');
define('_INDEXPHP_VERSION','Versión');
define('_INDEXPHP_PLATFORM','Plataforma');
define('_INDEXPHP_DATE','Fecha');
define('_INDEXPHP_UPTIME','Tiempo Encendido');
define('_INDEXPHP_LASTCHANGE','Ultima configuración');
define('_INDEXPHP_MEMUSE','Uso de memoria');
define('_INDEXPHP_LOADAVERAGE','Promedio de carga');
define('_INDEXPHP_SHOWPROCESSING','mostrar información de procesos');

// Status Processes Module
// file: status_processes.php
define('_STATSPROCPHP_NAME','Estado');
define('_STATSPROCPHP_NAME_DESCRIPTION','Procesos');
define('_STATSPROCPHP_PROC_INF','Información de procesos');

// Static Routes Module
// file: system_routes.php
define('_SYSTEMROUTESPHP_NAME','Sistema');
define('_SYSTEMROUTESPHP_NAME_DESCRIPTION','Rutas Estáticas');
define('_SYSTEMROUTESPHP_MSG_CHANGED','La configuración de las rutas estáticas ha cambiado.<br>Usted tiene que aplicar los cambios para que la configuración tenga efecto.');
define('_SYSTEMROUTESPHP_INT','Interface');
define('_SYSTEMROUTESPHP_NET','Red');
define('_SYSTEMROUTESPHP_GW','Puerta de Enlace');
define('_SYSTEMROUTESPHP_DESC','Descripción');
define('_SYSTEMROUTESPHP_EDITROUTE','Editar Ruta');
define('_SYSTEMROUTESPHP_MSGCONFIRM','Confirma que desea borrar esta ruta?');
define('_SYSTEMROUTESPHP_DELROUTE','Borrar Ruta');
define('_SYSTEMROUTESPHP_ADDROUTE','Añadir Ruta');

// System Firmware Module
// file: system_firmware.php
define('_SYSTEMFIRMWAREPHP_NAME','Sistema');
define('_SYSTEMFIRMWAREPHP_NAMEDESC','Sistema Base');
define('_SYSTEMFIRMWAREPHP_INVALIDFIRM','El fichero de la imagen no es para esta plataforma');
define('_SYSTEMFIRMWAREPHP_OUTMEM','Fallo en la subida de la imagen(memoria agotada?)');
define('_SYSTEMFIRMWAREPHP_INVSIG','La firma digital en esta imagen no es válida.');
define('_SYSTEMFIRMWAREPHP_INVSIGSHORT','Esta imagen no está firmada digitalmente.');
define('_SYSTEMFIRMWAREPHP_INVSIGIMGT','Ha ocurrido un error verificando la firma en esta imagen.');
define('_SYSTEMFIRMWAREPHP_CORRUPTIMG','El fichero de la imagen está corrupto.');
define('_SYSTEMFIRMWAREPHP_INSTREBOOTLAT','El Sistema Base se está instalando. Frenas se reiniciará automáticamente.');
define('_SYSTEMFIRMWAREPHP_NOTSUP','El envio del Sistema Base no está soportado en esta plataforma.');
define('_SYSTEMFIRMWAREPHP_BIGWARN','Esto significa que la imagen enviada ' .
		 	'no es una imagen oficial/soportada y puede traer consigo comportamientos inesperados o ' .
			'compromisos de seguridad. Solo instale imágenes provenientes de fuentes de confianza, y asegure '.
			'quela imagen no ha sido falseada.<br><br>'.
			'La desea instalar de todas formas ? (bajo su propio riesgo)?');
define('_SYSTEMFIRMWAREPHP_CLICKENFIRM','Click &quot;Habilitar la actualización del Sistema Base 
          &quot; a continuación, luego seleccione el fichero de la imagen '.$g['fullplatform'].'-*.img)
			 a enviar.<br>Click &quot;Actualizar Sistema Base&quot; 
          para comenzar el proceso de actualización.');	
define('_SYSTEMFIRMWAREPHP_ENFIRMUP','Habilitar la actualización del Sistema Base');
define('_SYSTEMFIRMWAREPHP_DESFIRMUP','Deshabilitar la actualización del Sistema Base');	
define('_SYSTEMFIRMWAREPHP_FIRMFILE','Fichero Imagen del Sistema Base');
define('_SYSTEMFIRMWAREPHP_UPFIRM','Actualizar Sistema Base');
define('_SYSTEMFIRMWAREPHP_MSGREBOOTUP','Usted debe reiniciar el sistema antes de actualizar el sistema base.');
define('_SYSTEMFIRMWAREPHP_DONOTABORT','No aborte el proceso una vez que comience. 
                    FreeNAS reiniciará automáticamente después de salvar el nuevo Sistema Base. La configuración
                    se mantendrá.<br>
                    Necesita un mínimo de 80 Mb RAM para actualizar el Sistema Base.');
// System Advanced Module
// file: system_advanced.php
define('_SYSTEMADVANCEDPHP_NAME','Sistema');
define('_SYSTEMADVANCEDPHP_NAMEDESC','Configuración Avanzada');
	     	
?>
