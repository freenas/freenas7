PORTNAME=			fuppes
PORTVERSION=	0.660
PORTEPOCH=		1
CATEGORIES=		net
COMMENT=			Free UPnP Entertainment Service
MAINTAINER=		michael.zoon@freenas.org

MASTER_SITES=			SF/${PORTNAME}/${PORTNAME}/SVN-660
DISTNAME=					${PORTNAME}-${PORTVERSION}

LIB_DEPENDS+=			sqlite3:${PORTSDIR}/databases/sqlite3 \
									tag:${PORTSDIR}/audio/taglib \
									pcre.0:${PORTSDIR}/devel/pcre \
									uuid.1:${PORTSDIR}/misc/e2fsprogs-libuuid \
									mp3lame.0:${PORTSDIR}/audio/lame \
									twolame.0:${PORTSDIR}/audio/twolame \
									vorbis.4:${PORTSDIR}/audio/libvorbis \
									avformat:${PORTSDIR}/multimedia/ffmpeg

USE_AUTOTOOLS=		autoconf
USE_GNOME=				libxml2
USE_ICONV=				yes
USE_GMAKE=				yes
GNU_CONFIGURE=		yes
CONFIGURE_ARGS+=	--enable-largefile \
									--enable-transcoder-ffmpeg \
									--enable-faad \
									--enable-twolame \
									--enable-mp4v2 \
									--enable-taglib \
									--enable-lame \
									--enable-vorbis \
									--enable-musepack \
									--enable-flac \
									--enable-dlna \
									--disable-exiv2

CONFIGURE_ENV=		CPPFLAGS="-I${LOCALBASE}/include -I${LOCALBASE}/include/ffmpeg ${PTHREAD_CFLAGS}" \
									LIBS="-L${LOCALBASE}/lib ${PTHREAD_LIBS}"

pre-configure:
	@if [ -f "${WRKSRC}/m4/version.sh" ]; then \
		${REINPLACE_CMD} -e 's,/bin/bash,${LOCALBASE}/bin/bash,' ${WRKSRC}/m4/version.sh; \
	fi
	@(cd ${WRKDIR}/${DISTNAME}; autoreconf -vfi)

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/${PORTNAME}d ${FREENAS_ROOTFS}/usr/local/bin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/.libs/libfuppes.so.* ${FREENAS_ROOTFS}/usr/local/lib
	@${INSTALL_SCRIPT} -v ${FILESDIR}/${PORTNAME}.in ${FREENAS_ROOTFS}/etc/rc.d/${PORTNAME}
	@${MKDIR} -v ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	@${INSTALL_DATA} -v ${WRKSRC}/src/lib/Presentation/fuppes-small.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	@${INSTALL_DATA} -v ${WRKSRC}/src/lib/Presentation/header-gradient.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	@${INSTALL_DATA} -v ${WRKSRC}/src/lib/Presentation/header-gradient-small.png ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	@${INSTALL_DATA} -v ${WRKSRC}/src/lib/Presentation/style.css ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}
	@${INSTALL_DATA} -v ${FILESDIR}/vfolder.cfg ${FREENAS_ROOTFS}/usr/local/share/${PORTNAME}

	# Install plugins
	@${MKDIR} -v ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libtranscoder_ffmpeg.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libtranscoder_ffmpeg.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libmetadata_taglib.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libmetadata_taglib.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libmetadata_libavformat.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libmetadata_libavformat.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libmetadata_dlna_profiles.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libmetadata_dlna_profiles.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libencoder_wav.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libencoder_wav.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}	
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libencoder_pcm.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libencoder_pcm.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libdecoder_vorbis.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libdecoder_vorbis.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libdecoder_flac.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libdecoder_flac.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${CP} -Rpv ${WRKSRC}/src/plugins/.libs/libdatabase_sqlite3.so ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}
	@${INSTALL_PROGRAM} -v ${WRKSRC}/src/plugins/.libs/libdatabase_sqlite3.so.* ${FREENAS_ROOTFS}/${PREFIX}/lib/${PORTNAME}

.include <bsd.port.mk>
