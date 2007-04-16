################################################################
# This file contains macros (defaults) used in
# FreeNAS packages/plugins Makefiles.
#
# Copyright (C) 2007 Volker Theile <votdev@gmx.de>.
################################################################

# Check if environment variables are set.
.for variable in FREENAS_ROOTDIR FREENAS_PRODUCTNAME FREENAS_VERSION
.if !defined(${variable})
check-makevars::
	@${ECHO_MSG} "${PKGNAME}: Environment error: '${variable}' not defined."
	@${FALSE}
.endif
.endfor

# No files are needed to be fetched for this package.
DISTFILES?=	

# No build is required for this package.	
NO_BUILD?=	1

# No manpages are installed for this package.
NO_INSTALL_MANPAGES?=	1

# Overwrite any existing package registration information.
FORCE_PKG_REGISTER?=	1

# A top level directory where all packages go.
PACKAGES?=	${FREENAS_ROOTDIR}/packages

# The temporary working directory.
WRKDIR?=	${PACKAGES}/work/${PORTNAME}

# Suffix to specify compilation options.
PKGNAMESUFFIX?=	-${FREENAS_PRODUCTNAME}

# Version of software.
PORTVERSION?=	${FREENAS_VERSION}

################################################################
# Special configuration if package depends on a port:
# - Always clean and build port but do not install it.
################################################################
.if defined(BUILD_DEPENDS)
ALWAYS_BUILD_DEPENDS=	1
DEPENDS_TARGET=	clean build
.endif

.include <bsd.port.mk>
