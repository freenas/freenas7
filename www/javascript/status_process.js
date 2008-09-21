/*
	status_process.js
	Copyright (C) 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.
*/
function update_procinfo(value) {
	if (document.getElementById("procinfo") == null)
		return;
	document.getElementById("procinfo").value = value; 
}

function update_callback() {
	x_get_process_info(update_procinfo);
	window.setTimeout('update_callback()', 5000);
}

window.setTimeout('update_callback()', 5000);
