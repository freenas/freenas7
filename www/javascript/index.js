/*
	index.js
	Copyright © 2008 Volker Theile (votdev@gmx.de)
  All rights reserved.
*/
function update_controls(x) {
	var values = x.split("|");

	for(var i=0; i<x.length; x++) {
		if(values[i] == 'undefined' || values[i] == null)
			return;
	}

	update_uptime(values[0]);
	update_date(values[1]);
	update_memusage(values[2], values[3]);
	update_loadaverage(values[4]);
	update_cpuusage(values[5]);
}

function update_date(value) {
	if(document.getElementById("date") == null)
		return;
	document.getElementById("date").value = value;
}

function update_uptime(value) {
	if(document.getElementById("uptime") == null)
		return;
	document.getElementById("uptime").value = value;
}

function update_memusage(value, desc) {
	if(document.getElementById("memusage") == null)
		return;
	document.getElementById("memusage").value = desc;
	document.getElementById("memusageu").style.width = value + 'px';
	document.getElementById("memusagef").style.width = (100 - value) + 'px';
}

function update_loadaverage(value) {
	if(document.getElementById("loadaverage") == null)
		return;
	document.getElementById("loadaverage").value = value;
}

function update_cpuusage(value) {
	if(document.getElementById("cpuusage") == null)
		return;
	document.getElementById("cpuusage").value = value + '%';
	document.getElementById("cpuusageu").style.width = value + 'px';
	document.getElementById("cpuusagef").style.width = (100 - value) + 'px';
}

function update_callback() {
	x_update_controls(update_controls);
	window.setTimeout('update_callback()', 5000);
}

window.setTimeout('update_callback()', 5000);
