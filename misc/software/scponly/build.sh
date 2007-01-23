#!/usr/bin/env bash

build_scponly() {
	cd /usr/ports/shells/scponly/

  export WITH_SCPONLY_RSYNC=YES
  export WITH_SCPONLY_SCP=YES
  export WITH_SCPONLY_WINSCP=YES
  export WITH_SCPONLY_UNISON=YES

	make clean
	make

	return $?
}

install_scponly() {
	cd /usr/ports/shells/scponly

	install -vs work/scponly-*/scponly $FREENAS/usr/local/bin/

	return 0
}
