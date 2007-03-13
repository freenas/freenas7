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
CHMOD?=		/bin/chmod
CHROOT?=	/usr/sbin/chroot
CP?=	/bin/cp
CUT?=	/usr/bin/cut
ECHO_CMD?=	echo	# Shell builtin
ECHO_MSG?=	${ECHO_CMD}
EXPR?=	/bin/expr
FALSE?=	false	# Shell builtin
FETCH_CMD?=	/usr/bin/fetch -ApRr
GCC?=	/usr/bin/gcc
GREP?=	/usr/bin/grep
INSTALL?=	/usr/bin/install
INSTALL_DATA?=	${INSTALL} -v
INSTALL_PROGRAM?=	${INSTALL} -vs
INSTALL_SCRIPT?=	${INSTALL} -v
LDCONFIG?=	/sbin/ldconfig
LN?=	/bin/ln
MKDIR?= /bin/mkdir -p
PKG_ADD?=	/usr/sbin/pkg_add
PKG_INFO?=	/usr/sbin/pkg_info
REALPATH?=	/bin/realpath
RM?=	/bin/rm
SED?=	/usr/bin/sed
SORT?=	/usr/bin/sort
TAR?=	/usr/bin/tar
TEST?=	test	# Shell builtin
TR?=	LANG=C /usr/bin/tr
UNAME?=	/usr/bin/uname
WHICH?=	/usr/bin/which

# Get the architecture
.if !defined(ARCH)
ARCH!=	${UNAME} -p
.endif
