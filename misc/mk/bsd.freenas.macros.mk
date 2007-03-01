################################################################
# This file contains macros used in Makefile.
#
# Copyright (C) 2007 Volker Theile <votdev@gmx.de>.
#
# Parts of code taken from FreeBSD <bsd.port.mk>.
################################################################

################################################################
# Command macros
################################################################
AWK?=	/usr/bin/awk
BASENAME?=	/usr/bin/basename
CUT?=	/usr/bin/cut
CHROOT?=	/usr/sbin/chroot
ECHO_CMD?=	echo	# Shell builtin
ECHO_MSG?=	${ECHO_CMD}
EXPR?=	/bin/expr
FALSE?=	false	# Shell builtin
GREP?=	/usr/bin/grep
REALPATH?=	/bin/realpath
SED?=	/usr/bin/sed
SORT?=	/usr/bin/sort
TR?=	LANG=C /usr/bin/tr
UNAME?=	/usr/bin/uname
PKG_ADD?=	/usr/sbin/pkg_add
PKG_INFO?=	/usr/sbin/pkg_info
TAR?=	/usr/bin/tar
TEST?=	test	# Shell builtin
WHICH?=	/usr/bin/which
LDCONFIG?=	/sbin/ldconfig
FETCH_CMD?=	/usr/bin/fetch -ApRr
CP?=	/bin/cp
CHMOD?=		/bin/chmod
MKDIR?= /bin/mkdir -p
RM?=	/bin/rm
LN?=	/bin/ln
INSTALL?=	/usr/bin/install
INSTALL_PROGRAM?=	${INSTALL} -vs
INSTALL_SCRIPT?=	${INSTALL} -v
INSTALL_DATA?=	${INSTALL} -v

# Get the architecture
.if !defined(ARCH)
ARCH!=	${UNAME} -p
.endif
