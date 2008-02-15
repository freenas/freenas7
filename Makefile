PORTNAME=				avahi
PORTVERSION=		0.6.22
PORTREVISION?=	1
CATEGORIES?=		net dns
COMMENT=				Service discovery on a local network
MAINTAINER=			votdev@gmx.de

MASTER_SITES=		http://www.avahi.org/download/

USE_GNOME?=				intltool
USE_GETTEXT=			yes
USE_GMAKE=				yes
USE_AUTOTOOLS=		libtool:15
USE_LDCONFIG=			yes
USE_GETOPT_LONG=	yes

CONFIGURE_ARGS?=	--with-distro=freebsd \
									--disable-compat-libdns_sd \
									--disable-rpath \
									--disable-glib \
									--disable-gobject \
									--disable-qt3 \
									--disable-qt4 \
									--disable-gtk \
									--disable-dbus \
									--disable-gdbm \
									--disable-libdaemon \
									--disable-python \
									--disable-pygtk \
									--disable-python-dbus \
									--disable-mono \
									--disable-monodoc \
									--disable-autoipd \
									--disable-doxygen-doc \
									--disable-doxygen-dot \
									--disable-doxygen-xml \
									--disable-doxygen-html \
									--disable-manpages \
									--disable-xmltoman \
									--localstatedir=/var \
									--with-xml=bsdxml

CONFIGURE_ENV=		CPPFLAGS="-I${LOCALBASE}/include -DHAVE_KQUEUE" \
									LDFLAGS="-L${LOCALBASE}/lib" \
									PTHREAD_CFLAGS="${PTHREAD_CFLAGS}" \
									PTHREAD_LIBS="${PTHREAD_LIBS}"

do-install:

.include <bsd.port.mk>
