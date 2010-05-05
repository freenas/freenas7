PORTNAME=				transmission
PORTVERSION=		1.93
PORTREVISION?=	1
CATEGORIES?=		net-p2p
COMMENT=				A fast and lightweight CLI bitTorrent client
MAINTAINER=			ma.zoon@quicknet.nl

MASTER_SITES=		http://download.m0k.org/${PORTNAME}/files/

LIB_DEPENDS+=		curl.6:${PORTSDIR}/ftp/curl

USE_GMAKE=			yes
USE_GNOME?=			pkgconfig
USE_OPENSSL=		yes
USE_BZIP2=			yes
GNU_CONFIGURE=	yes

CONFIGURE_ARGS+=	--with-ssl=${OPENSSLBASE} \
									--with-zlib=/usr \
									--enable-largefile \
									--enable-daemon \
									--disable-gtk \
									--disable-libnotify \
		 							--disable-mac \
		 							--disable-nls

general-patch:
	@${REINPLACE_CMD} -e 's|SUBDIRS = . sample.*|SUBDIRS = .|g' \
		${WRKSRC}/third-party/libevent/Makefile.*
	@${FIND} ${WRKSRC} -name Makefile.* | ${XARGS} ${REINPLACE_CMD} -e \
		's|noinst_PROGRAMS = |noinst_PROGRAMS = #|g'
	@${REINPLACE_CMD} -e \
		's|_pthread_flags=".*"|_pthread_flags="${PTHREAD_LIBS}"|g ; \
		s|libevent_source=system|libevent_source=bundled|g' \
			${WRKSRC}/configure
	@${REINPLACE_CMD} -e 's|-lrt||g' \
		${WRKSRC}/third-party/libevent/configure
	@${REINPLACE_CMD} -e 's|doc \\| \\|g' \
		${WRKSRC}/Makefile.in

post-patch: general-patch

do-install:
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}

	@${INSTALL_PROGRAM} -v ${WRKSRC}/daemon/transmission-daemon ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/daemon/transmission-remote ${FREENAS_ROOTFS}/usr/local/bin

	@${MKDIR} -pv ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${MKDIR} -pv ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/graphics
	@${MKDIR} -pv ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/progress
	@${MKDIR} -pv ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript/jquery
	@${MKDIR} -pv ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/stylesheets

	@${INSTALL_DATA} -v ${WRKSRC}/web/LICENSE ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/cancel.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/file_priority_buttons.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/file_wanted_buttons.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/info_activity.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/info_files.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/info_trackers.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/info_general.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/tab_backgrounds.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/toolbar_buttons.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/buttons/torrent_buttons.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/buttons
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/favicon.ico ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/favicon.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/graphics/chrome.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/graphics
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/graphics/filter_bar.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/graphics
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/graphics/filter_icon.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/graphics
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/graphics/iphone_chrome.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/graphics
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/graphics/logo.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/graphics
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/graphics/transfer_arrows.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/graphics
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/progress/progress.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images/progress
	@${INSTALL_DATA} -v ${WRKSRC}/web/images/webclip-icon.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/images
	@${INSTALL_DATA} -v ${WRKSRC}/web/index.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/common.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/dialog.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/jquery/jquery.contextmenu.min.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript/jquery
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/jquery/jquery.form.min.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript/jquery
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/jquery/jquery.min.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript/jquery
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/jquery/jquery.transmenu.min.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript/jquery
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/jquery/json.min.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript/jquery
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/menu.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/torrent.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/transmission.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript
	@${INSTALL_DATA} -v ${WRKSRC}/web/javascript/transmission.remote.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/javascript
	@${INSTALL_DATA} -v ${WRKSRC}/web/stylesheets/common.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/stylesheets
	@${INSTALL_DATA} -v ${WRKSRC}/web/stylesheets/ie6.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/stylesheets
	@${INSTALL_DATA} -v ${WRKSRC}/web/stylesheets/ie7.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/stylesheets
	@${INSTALL_DATA} -v ${WRKSRC}/web/stylesheets/iphone.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/web/stylesheets

.include <bsd.port.mk>
