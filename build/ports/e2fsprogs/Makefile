PORTNAME=			e2fsprogs
PORTVERSION=	1.41.11
PORTREVISION=	0
CATEGORIES=		sysutils
COMMENT=			Utilities & library to manipulate ext2/3/4 filesystems
MAINTAINER=		ma.zoon@quicknet.nl

MASTER_SITES=			SF/${PORTNAME}/${PORTNAME}/${PORTVERSION}

PATCH_STRIP=			-p1
USE_GMAKE=				yes
USE_LDCONFIG=			yes
GNU_CONFIGURE=		yes
MAKE_JOBS_SAFE=		yes
CONFIGURE_ARGS+=	--disable-fsck --disable-e2initrd-helper \
									--with-root-prefix='${PREFIX}'
#CONFIGURE_ENV+=        CPPFLAGS='${CPPFLAGS} -I${WRKSRC}/lib \ 
   #               -I${LOCALBASE}/include -D_EXT2_USE_C_VERSIONS_' 


CONFIGURE_ENV+=		CPPFLAGS='${CPPFLAGS} -I${WRKSRC}/lib \
									-I${LOCALBASE}/include'
MAKE_ARGS+=				LDFLAGS='${LDFLAGS} -L${LOCALBASE}/lib'

.include <bsd.port.pre.mk>

USE_GETTEXT=	yes

post-patch::
	@${REINPLACE_CMD} -E -e \
		's:\$$\(libdir\)/pkgconfig:${PREFIX}/libdata/pkgconfig:' \
		${WRKSRC}/lib/*/Makefile.in
# don't build/install libext2fs.info
	@${REINPLACE_CMD} -e 's/ install-doc-libs$$//' ${WRKSRC}/Makefile.in

.if ${MASTERDIR} == ${.CURDIR}
post-build:
# Relink e2fsck statically - We need to make sure that tools for the root file
# system are statically linked against anything that is outside the root fs,
# else we're in trouble if e2fsck is needed for boot:
# (we don't use e2fsck.static, since we can link libc.so dynamically)
	cd ${WRKSRC}/e2fsck && ${RM} -f e2fsck \
		&& ${GMAKE} e2fsck \
		STATIC_LIBS="../lib/libext2fs.a ../lib/libcom_err.a \
		../lib/libblkid.a  ../lib/libuuid.a" \
		LIBINTL="${LOCALBASE}/lib/libintl.a ${LOCALBASE}/lib/libiconv.a"
# Regression check: avoid a port (not upstream!) regression from 1.40.5,
# check that e2fsck isn't dynalinked against anything but libc.so:
	@${ECHO_CMD} -n "===>  checking that e2fsck depends on no shared objects beyond libc.so: "
	@a="$$(ldd ${WRKSRC}/e2fsck/e2fsck 2>/dev/null \
	    | ${GREP} -v 'not a dynamic executable' \
	    | ${GREP} '=>' \
	    | ${AWK}  '{print $$3;}' \
	    | ${GREP} -v '^/lib/libc\.so\.' || :)"; \
	if test "x$$a" = "x" ; then echo 'PASS' ; else \
	    echo 'FAIL' ; echo '===>  e2fsck depends on:' ; echo "$$a" ; exit 1 ; fi
# Build fsck(8) wrapper
	${CC} ${CPPFLAGS} ${CFLAGS} ${LDFLAGS} -s ${LIBS} \
		-o ${WRKSRC}/fsck_ext2fs ${FILESDIR}/fsck_ext2fs.c
.endif

do-install:
	@${INSTALL_PROGRAM} -v ${WRKSRC}/e2fsck/e2fsck ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/misc/mke2fs ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/misc/dumpe2fs ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/misc/tune2fs ${FREENAS_ROOTFS}/usr/local/sbin
	@${INSTALL_PROGRAM} -v ${WRKSRC}/fsck_ext2fs ${FREENAS_ROOTFS}/usr/local/sbin

	@${LN} -s -f -v /usr/local/sbin/e2fsck ${FREENAS_ROOTFS}/sbin/e2fsck
	@${LN} -s -f -v /usr/local/sbin/fsck_ext2fs ${FREENAS_ROOTFS}/sbin/fsck_ext2fs

.include <bsd.port.post.mk>
