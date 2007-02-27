# FILESDIR - A directory containing any miscellaneous additional files.
#            Default: ${.CURDIR}/files
FILESDIR?=	${.CURDIR}/files

# PORTSDIR - The root of the ports tree.
#            Default: /usr/ports
PORTSDIR?=		/usr/ports

# Command macros
AWK?=	/usr/bin/awk
BASENAME?=	/usr/bin/basename
CUT?=	/usr/bin/cut
ECHO_CMD?=	echo	# Shell builtin
GREP?=	/usr/bin/grep
REALPATH?=	/bin/realpath
SED?=	/usr/bin/sed
SORT?=	/usr/bin/sort
TR?=	LANG=C /usr/bin/tr
UNAME?=	/usr/bin/uname
PKG_INFO?=	/usr/sbin/pkg_info
TEST?=	test	# Shell builtin

# Get the architecture
.if !defined(ARCH)
ARCH!=	${UNAME} -p
.endif

################################################################
# Main target.
################################################################
.MAIN: all

all:	build-depends build

_UNIFIED_DEPENDS=${BUILD_DEPENDS} ${LIB_DEPENDS}
_DEPEND_DIRS=	${_UNIFIED_DEPENDS:C,^[^:]*:([^:]*).*$,\1,}

all-depends-list:
	@${ALL-DEPENDS-LIST}

ALL-DEPENDS-LIST= \
	L="${_DEPEND_DIRS}";						\
	checked="";							\
	while [ -n "$$L" ]; do						\
		l="";							\
		for d in $$L; do					\
			case $$checked in				\
			$$d\ *|*\ $$d\ *|*\ $$d)			\
				continue;;				\
			esac;						\
			checked="$$checked $$d";			\
			if [ ! -d $$d ]; then				\
				${ECHO_MSG} "${PKGNAME}: \"$$d\" non-existent -- dependency list incomplete" >&2; \
				continue;				\
			fi;						\
			${ECHO_CMD} $$d;				\
			if ! children=$$(cd $$d && ${MAKE} -V _DEPEND_DIRS); then\
				${ECHO_MSG} "${PKGNAME}: \"$$d\" erroneous -- dependency list incomplete" >&2; \
				continue;				\
			fi;						\
			for child in $$children; do			\
				case "$$checked $$l" in			\
				$$child\ *|*\ $$child\ *|*\ $$child)	\
					continue;;			\
				esac;					\
				l="$$l $$child";			\
			done;						\
		done;							\
		L=$$l;							\
	done

build-depends-list:
.if defined(BUILD_DEPENDS) || defined(LIB_DEPENDS)
	@${BUILD-DEPENDS-LIST}
.endif

BUILD-DEPENDS-LIST= \
	for dir in $$(${ECHO_CMD} "${BUILD_DEPENDS} ${LIB_DEPENDS}" | ${TR} '\040' '\012' | ${SED} -e 's/^[^:]*://' -e 's/:.*//' | ${SORT} -u); do \
		if [ -d $$dir ]; then \
			${ECHO_CMD} $$dir; \
		else \
			${ECHO_MSG} "${PKGNAME}: \"$$dir\" non-existent -- dependency list incomplete" >&2; \
		fi; \
	done | ${SORT} -u

package-depends-list:
.if defined(LIB_DEPENDS)
	@${PACKAGE-DEPENDS-LIST}
.endif

PACKAGE-DEPENDS-LIST?= \
	checked="${PARENT_CHECKED}"; \
	for dir in $$(${ECHO_CMD} "${LIB_DEPENDS}" | ${SED} -e 'y/ /\n/' | ${CUT} -f 2 -d ':'); do \
		echo $$dir; \
		dir=$$(${REALPATH} $$dir); \
		if [ -d $$dir ]; then \
			if (${ECHO_CMD} $$checked | ${GREP} -qwv "$$dir"); then \
				childout=$$(cd $$dir; ${MAKE} CHILD_DEPENDS=yes PARENT_CHECKED="$$checked" package-depends-list); \
				set -- $$childout; \
				childdir=""; \
				while [ $$\# != 0 ]; do \
					childdir="$$childdir $$2"; \
					${ECHO_CMD} "$$1 $$2 $$3"; \
					shift 3; \
				done; \
				checked="$$dir $$childdir $$checked"; \
			fi; \
		else \
			${ECHO_MSG} "${PKGNAME}: \"$$dir\" non-existent -- dependency list incomplete" >&2; \
		fi; \
	done

package-depends:
	@${PACKAGE-DEPENDS-LIST} | ${AWK} '{print $$1":"$$3}'

missing:
	@for dir in $$(${ALL-DEPENDS-LIST}); do \
		THISORIGIN=$$(${ECHO_CMD} $$dir | ${SED} 's,${PORTSDIR}/,,'); \
		installed=$$(${PKG_INFO} -qO $${THISORIGIN}); \
		if [ -z "$$installed" ]; then \
			${ECHO_CMD} $$THISORIGIN; \
		fi \
	done

build-depends:
.if defined(BUILD_DEPENDS)
.endif
