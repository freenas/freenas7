################################################################
# This is the master file for the most common elements to all
# FreeNAS Makefile.
#
# Copyright (C) 2007 Volker Theile <votdev@gmx.de>.
#
# Parts of code taken from FreeBSD <bsd.port.mk>.
################################################################

# Check if environment variables are set.
.for variable in FREENAS_ROOTFS FREENAS_SVNDIR FREENAS_PRODUCTNAME FREENAS_KERNCONF
.if !defined(${variable})
check-makevars::
	@${ECHO_MSG} "${PKGNAME}: Environment error: '${variable}' not defined."
	@${FALSE}
.endif
.endfor

# No files are needed to be fetched if MASTER_SITES is not defined.
.if !defined(MASTER_SITES)
DISTFILES?=
.endif

# No manpages are installed for this port.
NO_INSTALL_MANPAGES?=	1

# Don't register a port installation as a package.
NO_PKG_REGISTER?=	1

################################################################
# Special configuration if port depends on other ports:
# - Always clean and build port but do not install it.
################################################################
.if defined(BUILD_DEPENDS)
ALWAYS_BUILD_DEPENDS=	1
DEPENDS_TARGET=	clean build
.endif

.include <bsd.port.mk>
