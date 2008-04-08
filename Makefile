PORTNAME=			lcdproc
PORTVERSION=	0.5.2
PORTREVISION=	2
CATEGORIES=		sysutils
COMMENT=			A client/server suite for LCD devices
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				${MASTER_SITE_SOURCEFORGE}
MASTER_SITE_SUBDIR=	${PORTNAME}

LIB_DEPENDS+=			usb-0.1.8:${PORTSDIR}/devel/libusb

ONLY_FOR_ARCHS=		i386 amd64
USE_AUTOTOOLS=		autoconf:261 automake:19 aclocal:19
USE_GCC=					3.2+
GNU_CONFIGURE=		yes
CONFIGURE_ARGS=		--enable-drivers="${LCDPROC_DRIVERS}" \
									--disable-ldap \
									--disable-doxygen \
									--enable-libusb
CONFIGURE_ENV=		CFLAGS="${CFLAGS}"
CONFIGURE_TARGET=	--build=${MACHINE_ARCH}-portbld-freebsd${OSREL}

LCDPROC_DRIVERS=	bayrad \
									CFontz \
									CFontz633 \
									CFontzPacket \
									CwLnx \
									curses \
									ea65 \
									EyeboxOne \
									glk \
									hd44780 \
									icp_a106 \
									lb216 \
									lcdm001 \
									lcterm \
									MD8800 \
									ms6931 \
									mtc_s16209x \
									MtxOrb \
									NoritakeVFD \
									pyramid \
									sed1330 \
									sed1520 \
									serialPOS \
									serialVFD \
									sli \
									stv5730 \
									t6963 \
									text \
									tyan \
									IOWarrior

.include <bsd.port.pre.mk>

post-patch:
# Copy libusb options file
	@${MKDIR} -v /var/db/ports/libusb
	@${CP} -pv ${FILESDIR}/libusb-options.in /var/db/ports/libusb/options

do-install:
	@${INSTALL_PROGRAM} ${WRKSRC}/clients/lcdproc/lcdproc ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${WRKSRC}/clients/lcdexec/lcdexec ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${WRKSRC}/clients/lcdvc/lcdvc ${PREFIX}/bin
	@${INSTALL_PROGRAM} ${WRKSRC}/server/LCDd ${PREFIX}/sbin

	@${MKDIR} -v ${PREFIX}/lib/lcdproc
	@${CP} -pv ${WRKSRC}/server/drivers/*.so ${PREFIX}/lib/lcdproc

	@${CP} -pv ${PORTSDIR}/devel/libusb/work/libusb*/.libs/libusb-*.so* ${PREFIX}/lib
	@${CP} -pv ${PORTSDIR}/devel/libusb/work/libusb*/.libs/libusbpp-*.so* ${PREFIX}/lib

.include <bsd.port.post.mk>
