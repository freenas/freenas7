################################################################
# This is the master file for the most common elements to all
# FreeNAS Makefile.
#
# Copyright (C) 2007 Volker Theile <votdev@gmx.de>.
#
# Parts of code taken from FreeBSD <bsd.port.mk>.
################################################################
PKGNAME?=	${.CURDIR:T}

# FILESDIR - A directory containing any miscellaneous additional files.
#            Default: ${.CURDIR}/files
FILESDIR?=	${.CURDIR}/files

# PORTSDIR - The root of the ports tree.
#            Default: /usr/ports
PORTSDIR?=		/usr/ports

LOCALBASE?=	/usr/local
PKGCOMPATDIR?=	${LOCALBASE}/lib/compat/pkg

# Use this as the first operand to always build dependency.
NONEXISTENT?=	/nonexistent

################################################################
# Default config how to process ports/packages/dependencies.
################################################################
.if !defined(DEPENDS_TARGET)
.if make(reinstall)
DEPENDS_TARGET=	reinstall
.else
DEPENDS_TARGET=	install
.endif
.if defined(DEPENDS_CLEAN)
DEPENDS_TARGET+=	clean
DEPENDS_ARGS+=	NOCLEANDEPENDS=yes
.endif
.endif

################################################################
# Special configuration if package depends on a port:
# - Always clean and build port but do not install it.
################################################################
.if defined(BUILD_DEPENDS)
ALWAYS_BUILD_DEPENDS=	1
DEPENDS_TARGET=	clean build
.endif

################################################################
# Command macros
################################################################
.include "bsd.freenas.macros.mk"

################################################################
# Main target
################################################################
.MAIN: all

all:	pre-depends depends clean build

.if !target(build)
build:	build-message pre-build do-build post-build
.endif

.if !target(install)
install:	install-message pre-install do-install post-install
.endif

.if !target(clean)
clean:	clean-message do-clean
.endif

.if !target(do-clean)
do-clean:
.endif

.if !target(pre-depends)
pre-depends:
.endif

.if !target(pre-build)
pre-build:
.endif

.if !target(do-build)
do-build:
.endif

.if !target(post-build)
post-build:
.endif

.if !target(pre-install)
pre-install:
.endif

.if !target(do-install)
do-install:
.endif

.if !target(post-install)
post-install:
.endif

clean-message:
	@${ECHO_MSG} "===>  Cleaning for ${PKGNAME}"
build-message:
	@${ECHO_MSG} "===>  Building for ${PKGNAME}"
install-message:
	@${ECHO_MSG} "===>  Installing for ${PKGNAME}"

################################################################
# Dependencies
################################################################
.if !target(depends)
depends: lib-depends build-depends run-depends

.if defined(ALWAYS_BUILD_DEPENDS)
_DEPEND_ALWAYS=	1
.else
_DEPEND_ALWAYS=	0
.endif

_INSTALL_DEPENDS=	\
		if [ X${USE_PACKAGE_DEPENDS} != "X" ]; then \
			subpkgfile=`(cd $$dir; ${MAKE} $$depends_args -V PKGFILE)`; \
			if [ -r "$${subpkgfile}" -a "$$target" = "${DEPENDS_TARGET}" ]; then \
				if [ -z "${DESTDIR}" ] ; then \
					${ECHO_MSG} "===>   Installing existing package $${subpkgfile}"; \
					${PKG_ADD} $${subpkgfile}; \
				else \
					${ECHO_MSG} "===>   Installing existing package $${subpkgfile} into ${DESTDIR}"; \
					${PKG_ADD} -C ${DESTDIR} $${subpkgfile}; \
				fi; \
			else \
			  (cd $$dir; ${MAKE} -DINSTALLS_DEPENDS $$target $$depends_args) ; \
			fi; \
		else \
			(cd $$dir; ${MAKE} -DINSTALLS_DEPENDS $$target $$depends_args) ; \
		fi; \
		if [ -z "${DESTDIR}" ] ; then \
			${ECHO_MSG} "===>   Returning to build of ${PKGNAME}"; \
		else \
			${ECHO_MSG} "===>   Returning to build of ${PKGNAME} for ${DESTDIR}"; \
		fi;

.for deptype in BUILD RUN
${deptype:L}-depends:
.if defined(${deptype}_DEPENDS)
.if !defined(NO_DEPENDS)
	@for i in `${ECHO_CMD} "${${deptype}_DEPENDS}"`; do \
		prog=`${ECHO_CMD} $$i | ${SED} -e 's/:.*//'`; \
		dir=`${ECHO_CMD} $$i | ${SED} -e 's/[^:]*://'`; \
		if ${EXPR} "$$dir" : '.*:' > /dev/null; then \
			target=`${ECHO_CMD} $$dir | ${SED} -e 's/.*://'`; \
			dir=`${ECHO_CMD} $$dir | ${SED} -e 's/:.*//'`; \
		else \
			target="${DEPENDS_TARGET}"; \
			depends_args="${DEPENDS_ARGS}"; \
		fi; \
		if ${EXPR} "$$prog" : \\/ >/dev/null; then \
			if [ -e "$$prog" ]; then \
				if [ "$$prog" = "${NONEXISTENT}" ]; then \
					${ECHO_MSG} "Error: ${NONEXISTENT} exists.  Please remove it, and restart the build."; \
					${FALSE}; \
				else \
					if [ -z "${DESTDIR}" ] ; then \
						${ECHO_MSG} "===>   ${PKGNAME} depends on file: $$prog - found"; \
					else \
						${ECHO_MSG} "===>   ${PKGNAME} depends on file in ${DESTDIR}: $$prog - found"; \
					fi; \
					if [ ${_DEPEND_ALWAYS} = 1 ]; then \
						${ECHO_MSG} "       (but building it anyway)"; \
						notfound=1; \
					else \
						notfound=0; \
					fi; \
				fi; \
			else \
				if [ -z "${DESTDIR}" ] ; then \
					${ECHO_MSG} "===>   ${PKGNAME} depends on file: $$prog - not found"; \
				else \
					${ECHO_MSG} "===>   ${PKGNAME} depends on file in ${DESTDIR}: $$prog - not found"; \
				fi; \
				notfound=1; \
			fi; \
		else \
			case $${prog} in \
				*\>*|*\<*|*=*)	pkg=yes;; \
				*)		pkg="";; \
			esac; \
			if [ "$$pkg" != "" ]; then \
				if ${PKG_INFO} "$$prog" > /dev/null 2>&1 ; then \
					if [ -z "${DESTDIR}" ] ; then \
						${ECHO_MSG} "===>   ${PKGNAME} depends on package: $$prog - found"; \
					else \
						${ECHO_MSG} "===>   ${PKGNAME} depends on package in ${DESTDIR}: $$prog - found"; \
					fi; \
					if [ ${_DEPEND_ALWAYS} = 1 ]; then \
						${ECHO_MSG} "       (but building it anyway)"; \
						notfound=1; \
					else \
						notfound=0; \
					fi; \
				else \
					if [ -z "${DESTDIR}" ] ; then \
						${ECHO_MSG} "===>   ${PKGNAME} depends on package: $$prog - not found"; \
					else \
						${ECHO_MSG} "===>   ${PKGNAME} depends on package in ${DESTDIR}: $$prog - not found"; \
					fi; \
					notfound=1; \
				fi; \
				if [ $$notfound != 0 ]; then \
					inverse_dep=`${ECHO_CMD} $$prog | ${SED} \
						-e 's/<=/=gt=/; s/</=ge=/; s/>=/=lt=/; s/>/=le=/' \
						-e 's/=gt=/>/; s/=ge=/>=/; s/=lt=/</; s/=le=/<=/'`; \
					pkg_info=`${PKG_INFO} -E "$$inverse_dep" || ${TRUE}`; \
					if [ "$$pkg_info" != "" ]; then \
						${ECHO_MSG} "===>   Found $$pkg_info, but you need to upgrade to $$prog."; \
						exit 1; \
					fi; \
				fi; \
			elif ${WHICH} "$$prog" > /dev/null 2>&1 ; then \
				if [ -z "${PREFIX}" ] ; then \
					${ECHO_MSG} "===>   ${PKGNAME} depends on executable: $$prog - found"; \
				else \
					${ECHO_MSG} "===>   ${PKGNAME} depends on executable in ${DESTDIR}: $$prog - found"; \
				fi; \
				if [ ${_DEPEND_ALWAYS} = 1 ]; then \
					${ECHO_MSG} "       (but building it anyway)"; \
					notfound=1; \
				else \
					notfound=0; \
				fi; \
			else \
				if [ -z "${DESTDIR}" ] ; then \
					${ECHO_MSG} "===>   ${PKGNAME} depends on executable: $$prog - not found"; \
				else \
					${ECHO_MSG} "===>   ${PKGNAME} depends on executable in ${DESTDIR}: $$prog - not found"; \
				fi; \
				notfound=1; \
			fi; \
		fi; \
		if [ $$notfound != 0 ]; then \
			${ECHO_MSG} "===>    Verifying $$target for $$prog in $$dir"; \
			if [ ! -d "$$dir" ]; then \
				${ECHO_MSG} "     => No directory for $$prog.  Skipping.."; \
			else \
				${_INSTALL_DEPENDS} \
			fi; \
		fi; \
	done
.endif
.endif
.endfor

lib-depends:
.if defined(LIB_DEPENDS) && !defined(NO_DEPENDS)
	@for i in ${LIB_DEPENDS}; do \
		lib=$${i%%:*}; \
		case $$lib in \
			*.*.*)	pattern="`${ECHO_CMD} $$lib | ${SED} -e 's/\./\\\\./g'`" ;;\
			*.*)	pattern="$${lib%%.*}\.$${lib#*.}" ;;\
			*)	pattern="$$lib" ;;\
		esac; \
		dir=$${i#*:}; \
		target=$${i##*:}; \
		if ${TEST} $$dir = $$target; then \
			target="${DEPENDS_TARGET}"; \
			depends_args="${DEPENDS_ARGS}"; \
		else \
			dir=$${dir%%:*}; \
		fi; \
		if [ -z "${DESTDIR}" ] ; then \
			${ECHO_MSG} -n "===>   ${PKGNAME} depends on shared library: $$lib"; \
			if ${LDCONFIG} -r | ${GREP} -vwF -e "${PKGCOMPATDIR}" | ${GREP} -qwE -e "-l$$pattern"; then \
				${ECHO_MSG} " - found"; \
				if [ ${_DEPEND_ALWAYS} = 1 ]; then \
					${ECHO_MSG} "       (but building it anyway)"; \
					notfound=1; \
				else \
					notfound=0; \
				fi; \
			else \
				${ECHO_MSG} " - not found"; \
				notfound=1; \
			fi; \
		else \
			${ECHO_MSG} -n "===>   ${PKGNAME} depends on shared library in ${DESTDIR}: $$lib"; \
			if ${CHROOT} ${DESTDIR} ${LDCONFIG} -r | ${GREP} -vwF -e "${PKGCOMPATDIR}" | ${GREP} -qwE -e "-l$$pattern"; then \
				${ECHO_MSG} " - found"; \
				if [ ${_DEPEND_ALWAYS} = 1 ]; then \
					${ECHO_MSG} "       (but building it anyway)"; \
					notfound=1; \
				else \
					notfound=0; \
				fi; \
			else \
				${ECHO_MSG} " - not found"; \
				notfound=1; \
			fi; \
		fi; \
		if [ $$notfound != 0 ]; then \
			${ECHO_MSG} "===>    Verifying $$target for $$lib in $$dir"; \
			if [ ! -d "$$dir" ]; then \
				${ECHO_MSG} "     => No directory for $$lib.  Skipping.."; \
			else \
				${_INSTALL_DEPENDS} \
				if ! ${LDCONFIG} -r | ${GREP} -vwF -e "${PKGCOMPATDIR}" | ${GREP} -qwE -e "-l$$pattern"; then \
					${ECHO_MSG} "Error: shared library \"$$lib\" does not exist"; \
					${FALSE}; \
				fi; \
			fi; \
		fi; \
	done
.endif

.endif
