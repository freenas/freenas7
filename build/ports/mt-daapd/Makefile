PORTNAME=						mt-daapd
PORTVERSION=				1586
CATEGORIES=					audio
COMMENT=						Multithread daapd yet another server for Apple iTunes
MAINTAINER=					votdev@gmx.de

MASTER_SITES=				http://nightlies.fireflymediaserver.org/nightlies/svn-1586/
DISTNAME=						${PORTNAME}-svn-${PORTVERSION}

LIB_DEPENDS=				gdbm.3:${PORTSDIR}/databases/gdbm \
										sqlite3:${PORTSDIR}/databases/sqlite3 \
										id3tag.0:${PORTSDIR}/audio/libid3tag \
										vorbis.4:${PORTSDIR}/audio/libvorbis \
										FLAC.10:${PORTSDIR}/audio/flac \
										avcodec:${PORTSDIR}/multimedia/ffmpeg

USE_AUTOTOOLS=			autoheader:262 autoconf:262:env
USE_ICONV=					yes
GNU_CONFIGURE=			yes
CONFIGURE_ENV=			CFLAGS="${CFLAGS} ${PTHREAD_CFLAGS}" \
										CPPFLAGS="${CPPFLAGS} -I${LOCALBASE}/include" \
										LDFLAGS="-L${LOCALBASE}/lib" \
										LIBS="-L${LOCALBASE}/lib ${PTHREAD_LIBS}"
CONFIGURE_ARGS=			--disable-gdbm \
										--enable-sqlite3 \
										--with-id3tag=${PREFIX} \
										--enable-oggvorbis \
										--enable-flac \
										--enable-ffmpeg \
										--with-ffmpeg-includes=${LOCALBASE}/include/ffmpeg

do-install:
	-@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	-@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/plugins

	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/${PORTNAME} ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/out-daap.so ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/plugins
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/rsp.so ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/plugins
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/ssc-ffmpeg.so ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/plugins
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.playlist ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}

	-@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	-@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js
	-@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js/script.aculo.us

	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/about.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/config.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/config.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/config.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/config.xml ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/config-update.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/CREDITS ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/feedback.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/ff_logo_sm.gif ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/firefly.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/firefly.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/ftr.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/gpl-license.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/hdr.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/index.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/index.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/linkOpaque.gif ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/linkTransparent.gif ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/no_access.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/playlist.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/playlist.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/pngfix.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/required.gif ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/smart.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/smart.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/smartpopup.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/spinner.gif ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/spinner_stopped.gif ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/status.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/thanks.html ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/util.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/prototype.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/rico.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/script.aculo.us/builder.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js/script.aculo.us
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/script.aculo.us/controls.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js/script.aculo.us
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/script.aculo.us/dragdrop.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js/script.aculo.us
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/script.aculo.us/effects.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js/script.aculo.us
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/script.aculo.us/scriptaculous.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js/script.aculo.us
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/script.aculo.us/slider.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js/script.aculo.us
	@${INSTALL_DATA} -v ${WRKSRC}/admin-root/lib-js/script.aculo.us/unittest.js ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root/lib-js/script.aculo.us

	@${INSTALL_DATA} -v ${FILESDIR}/favicon.ico ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}/admin-root

.include <bsd.port.mk>
