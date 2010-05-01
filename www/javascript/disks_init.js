/*
	disks_init.js
	Copyright (C) 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.
*/
function disk_change() {
	var devicespecialfile = document.iform.disk.value;
	x_get_fs_type(devicespecialfile, update_type);
}

function update_type(value) {
	for (i = 0; i < document.iform.type.length; i++) {
		document.iform.type.options[i].selected = false;
		if (document.iform.type.options[i].value == value) {
			document.iform.type.options[i].selected = true;
		}
	}
	type_change();
}

function type_change() {
	var value = document.iform.type.value;
	switch (value) {
		case "ufsgpt":
			showElementById('minspace_tr','show');
			showElementById('volumelabel_tr','show');
			showElementById('aft4k_tr','show');
			break;
		case "ext2":
		case "msdos":
			showElementById('minspace_tr','hide');
			showElementById('volumelabel_tr','show');
			showElementById('aft4k_tr','hide');
			break;
		default:
			showElementById('minspace_tr','hide');
			showElementById('volumelabel_tr','hide');
			showElementById('aft4k_tr','hide');
			break;
	}
}
