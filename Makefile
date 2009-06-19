PORTNAME=			snmp
PORTVERSION=	5.4.2.1
CATEGORIES=		net-mgmt ipv6
COMMENT=			An extendable SNMP implementation
MAINTAINER=		votdev@gmx.de

MASTER_SITES=				SF
MASTER_SITE_SUBDIR=	net-snmp
PKGNAMEPREFIX=			net-
DISTNAME=						${PKGNAMEPREFIX}${PORTNAME}-${PORTVERSION}

MAKE_JOBS_UNSAFE=	yes
GNU_CONFIGURE=		yes
USE_OPENSSL=			yes
USE_LDCONFIG=			yes
USE_PERL5_BUILD=	yes

NET_SNMP_SYS_CONTACT?=		nobody@nowhere.invalid
NET_SNMP_SYS_LOCATION?=		Unknown
NET_SNMP_LOGFILE?=				/var/log/snmpd.log
NET_SNMP_PERSISTENTDIR?=	/var/net-snmp
NET_SNMP_MIB_MODULES?=		${NET_SNMP_MIB_MODULE_LIST}
NET_SNMP_MIB_MODULE_LIST=	host disman/event-mib smux mibII/mta_sendmail mibII/tcpTable ucd-snmp/diskio sctp-mib

CONFIGURE_ENV+=		PERLPROG="${PERL}" PSPROG="${PS_CMD}" SED="${SED}"
CONFIGURE_ARGS+=	--enable-shared \
									--enable-internal-md5 \
									--with-mib-modules="${_NET_SNMP_MIB_MODULES}" \
									--with-sys-contact="${NET_SNMP_SYS_CONTACT}" \
									--with-sys-location="${NET_SNMP_SYS_LOCATION}" \
									--with-logfile="${NET_SNMP_LOGFILE}" \
									--with-persistent-directory="${NET_SNMP_PERSISTENTDIR}" \
									--with-gnu-ld \
									--with-libwrap \
									--with-libs="-lm -lkvm -ldevstat" \
									--with-defaults \
									--disable-embedded-perl \
									--without-perl-modules \
									--enable-ipv6

EXTRA_PATCHES+=		${PATCHDIR}/extra-patch-local_Makefile.in

.include <bsd.port.pre.mk>

_NET_SNMP_MIB_MODULES=
.for module1 in ${NET_SNMP_MIB_MODULE_LIST}
_module1=${module1}
_define=false
. for module2 in ${NET_SNMP_MIB_MODULES}
_module2=${module2}
.  if ${_module1} == ${_module2}
_define=true
.  endif
. endfor
. if ${_define} == true
_NET_SNMP_MIB_MODULES+=${module1}
PLIST_SUB+=	WITH_${module1:C|.*/||:U}=""
. else
PLIST_SUB+=	WITH_${module1:C|.*/||:U}="@comment "
. endif
.endfor

BIN_FILES=	snmpbulkwalk snmpget snmpgetnext snmpset \
		snmpstatus snmptest snmptranslate snmptrap snmpwalk
SBIN_FILES=	snmpd snmptrapd
STARTUP_DIR=	${PREFIX}/etc/rc.d

PS_CMD?=	/bin/ps

post-patch:
.for filename in ${SCRIPT_FILES}
	@${REINPLACE_CMD} ${SCRIPTS_SUB:S/$/!g/:S/^/ -e s!%%/:S/=/%%!/} \
		${WRKSRC}/local/${filename}
.endfor

post-configure:
	@${FIND} ${WRKSRC} -name Makefile | \
	 ${XARGS} ${REINPLACE_CMD} -E -e '/^INSTALL[ 	]+=/s|$$| -m 755|'

post-build:
	${REINPLACE_CMD} -e 's| perlinstall||' ${WRKSRC}/Makefile

.include <bsd.port.post.mk>
