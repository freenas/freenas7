/*
	status_process.js
	Copyright © 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.
*/
function update_content(x) {
	if (document.getElementById("content") == null)
		return;
	document.getElementById("content").value = x; 
}

function update_callback() {
	x_get_top_content(update_content);
	window.setTimeout('update_callback()', 5000);
}

window.setTimeout('update_callback()', 5000);
