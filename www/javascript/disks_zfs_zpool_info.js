/*
	disks_zfs_zpool_info.js
	Copyright (C) 2009 Volker Theile (votdev@gmx.de)
	All rights reserved.
*/
function update_zfs_zpool_status(value) {
	if (document.getElementById("zfs_zpool_status") == null)
		return;
	if (isIE) {
		document.getElementById("zfs_zpool_status").innerHTML = "<pre>" + value + "</pre>";
	} else {
		document.getElementById("zfs_zpool_status").innerHTML = value;
	}
}

function update_callback() {
	x_zfs_zpool_get_status(update_zfs_zpool_status);
	window.setTimeout('update_callback()', 5000);
}

window.setTimeout('update_callback()', 5000);
