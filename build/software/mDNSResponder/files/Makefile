targets: build/dns-sd

clean:
	rm -rf build

build:
	mkdir build

build/dns-sd: build dns-sd.c
	cc $(filter %.c %.o, $+) -ldns_sd -L../mDNSPosix/build/prod -I../mDNSShared -o $@
