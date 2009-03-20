/*
	index.js
	Copyright (C) 2008 Volker Theile (votdev@gmx.de)
	All rights reserved.
*/
function update_controls(x) {
	var value = eval('(' + x + ')');

	update_uptime(value['uptime']);
	update_date(value['date']);
	update_memusage(value['memusage']);
	update_loadaverage(value['loadaverage']);
	update_cputemp(value['cputemp']);
	update_cpufreq(value['cpufreq']);
	update_cpuusage(value['cpuusage']);
	update_diskusage(value['diskusage']);
	update_swapusage(value['swapusage']);
}

function update_date(value) {
	if (document.getElementById("date") == null)
		return;
	document.getElementById("date").value = value;
}

function update_uptime(value) {
	if (document.getElementById("uptime") == null)
		return;
	document.getElementById("uptime").value = value;
}

function update_memusage(value, desc) {
	if (document.getElementById("memusage") == null)
		return;
	document.getElementById("memusage").value = value.caption;
	document.getElementById("memusageu").style.width = value.percentage + 'px';
	document.getElementById("memusagef").style.width = (100 - value.percentage) + 'px';
}

function update_loadaverage(value) {
	if (document.getElementById("loadaverage") == null)
		return;
	document.getElementById("loadaverage").value = value;
}

function update_cputemp(value) {
	if (document.getElementById("cputemp") == null)
		return;
	document.getElementById("cputemp").value = value;
}

function update_cpufreq(value) {
	if (document.getElementById("cpufreq") == null)
		return;
	document.getElementById("cpufreq").value = value + 'MHz';
}

function update_cpuusage(value) {
	if (document.getElementById("cpuusage") == null)
		return;
	document.getElementById("cpuusage").value = value + '%';
	document.getElementById("cpuusageu").style.width = value + 'px';
	document.getElementById("cpuusagef").style.width = (100 - value) + 'px';
}

function update_diskusage(value) {
	if (value == 'undefined' || value == null)
		return;
	for (var i=0; i<value.length; i++) {
		if (document.getElementById("diskusagec_" + value[i].id) == null)
			return;
		document.getElementById("diskusagec_" + value[i].id).value = value[i].caption;
		document.getElementById("diskusagecd_" + value[i].id).innerHTML = value[i].caption_detailed;
		document.getElementById("diskusageu_" + value[i].id).style.width = value[i].percentage + 'px';
		document.getElementById("diskusageu_" + value[i].id).title = value[i]['tooltip'].used;
		document.getElementById("diskusagef_" + value[i].id).style.width = (100 - value[i].percentage) + 'px';
		document.getElementById("diskusagef_" + value[i].id).title = value[i]['tooltip'].available;
	}
}

function update_swapusage(value) {
	if (value == 'undefined' || value == null)
		return;

	for (var i=0; i<value.length; i++) {
		if (document.getElementById("swapusagec_" + value[i].id) == null)
			return;
		document.getElementById("swapusagec_" + value[i].id).value = value[i].caption;
		document.getElementById("swapusagecd_" + value[i].id).innerHTML = value[i].caption_detailed;
		document.getElementById("swapusageu_" + value[i].id).style.width = value[i].percentage + 'px';
		document.getElementById("swapusageu_" + value[i].id).title = value[i]['tooltip'].used;
		document.getElementById("swapusagef_" + value[i].id).style.width = (100 - value[i].percentage) + 'px';
		document.getElementById("swapusagef_" + value[i].id).title = value[i]['tooltip'].available;
	}
}

function update_callback() {
	x_update_controls(update_controls);
	window.setTimeout('update_callback()', 5000);
}

window.setTimeout('update_callback()', 5000);
