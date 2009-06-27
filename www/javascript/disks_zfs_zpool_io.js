/*
	disks_zfs_zpool_io.js
	Copyright (C) 2009 Volker Theile (votdev@gmx.de)
	All rights reserved.
*/
function update_zfs_zpool_iostat(value) {
	if (document.getElementById("zfs_zpool_iostat") == null)
		return;
	if (isIE) {
		document.getElementById("zfs_zpool_iostat").innerText = value;
	} else {
		document.getElementById("zfs_zpool_iostat").innerHTML = value;
	}
}

function update_callback() {
	x_zfs_zpool_get_iostat(update_zfs_zpool_iostat);
	window.setTimeout('update_callback()', 5000);
}

window.setTimeout('update_callback()', 5000);
