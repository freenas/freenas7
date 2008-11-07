PORTNAME=			mediatomb
PORTVERSION=	0.11.0
CATEGORIES=		net multimedia
COMMENT=			UPnP A/V MediaServer
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	${PORTNAME}

LIB_DEPENDS+=			sqlite3:${PORTSDIR}/databases/sqlite3 \
									tag:${PORTSDIR}/audio/taglib \
									expat:${PORTSDIR}/textproc/expat2

GNU_CONFIGURE=		yes
LDFLAGS+=					${PTHREAD_LIBS}
CFLAGS+=					${PTHREAD_CFLAGS}
CONFIGURE_ARGS=		--with-search="${LOCALBASE}" \
									--disable-inotify \
									--enable-sqlite3 \
									--with-sqlite3-h="${LOCALBASE}/include" \
									--with-sqlite3-libs="${LOCALBASE}/lib" \
									--enable-taglib \
									--with-taglib-cfg="${LOCALBASE}/bin/taglib-config" \
									--disable-id3lib \
									--disable-libmagic \
									--disable-mysql \
									--disable-libextractor \
									--disable-libjs \
									--disable-libexif \
									--disable-tombdebug \
									--enable-protocolinfo-extension \
									--disable-external-transcoding \
									--disable-curl \
									--disable-ffmpeg
CONFIGURE_ENV=		PTHREAD_LIBS="${PTHREAD_LIBS}" \
									PTHREAD_CFLAGS="${PTHREAD_CFLAGS}" \
									LDFLAGS="${LDFLAGS}" \
									CFLAGS="${CFLAGS}"

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/build/${PORTNAME} ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.sh.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}.sh

	-@${MKDIR} ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/js
	-@${MKDIR} ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	-@${MKDIR} ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	-@${MKDIR} ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	-@${MKDIR} ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js

	@${INSTALL_DATA} -v ${WRKSRC}/scripts/js/import.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/js
	@${INSTALL_DATA} -v ${WRKSRC}/scripts/js/playlists.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/js
	@${INSTALL_DATA} -v ${WRKSRC}/scripts/js/common.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/js

	@${INSTALL_DATA} -v ${WRKSRC}/config/mappings.xml ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	@${INSTALL_DATA} -v ${WRKSRC}/config/sqlite3.sql ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}

	@${INSTALL_DATA} -v ${WRKSRC}/web/cds.xml ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/cm.xml ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/disabled.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/favicon.ico ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/add_as_autoscan.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/autoscan_inotify_config_folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/autoscan_inotify_folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/autoscan_timed_config_folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/autoscan_timed_folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/blank.gif ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/blank.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/document-new.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/film.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/folder_new.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/go-first.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/go-last.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/go-next.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/go-previous.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mediatomb.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon120.bmp ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon120.jpg ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon120.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon32.bmp ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon32.jpg ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon32.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon48.bmp ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon48.jpg ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/mt-icon48.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/autoscan_inotify_config_folder_closed.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/autoscan_inotify_config_folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/autoscan_inotify_folder_closed.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/autoscan_inotify_folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/autoscan_timed_config_folder_closed.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/autoscan_timed_config_folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/autoscan_timed_folder_closed.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/autoscan_timed_folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/folder_closed.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/folder_open.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/lastnode.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/line.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/minus.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/minus_last.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/minus_last_no_root.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/minus_no_root.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/plus.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/plus_last.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/plus_last_no_root.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/plus_no_root.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/t.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/t_no_root.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/nanotree/images/white.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons/nanotree/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/remove_all.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/remove_autoscan.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/remove_this.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/status.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/status_loading.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/status_updates_pending.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/stock-add.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/stock_edit.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/icons/stock_exit.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/icons
	@${INSTALL_DATA} -v ${WRKSRC}/web/index.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/auth.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/autoscan.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/icons.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/items.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/md5.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/nanotree.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/iepngfix.htc ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/prototype.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/tasks.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/tools.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/js/tree.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/js
	@${INSTALL_DATA} -v ${WRKSRC}/web/left.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/main.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/mr_reg.xml ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/right.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/std_treelook.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/top.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/topleft.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/topright.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web

.include <bsd.port.mk>
