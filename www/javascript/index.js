/*
	index.js
	Copyright (C) 2008-2009 Volker Theile (votdev@gmx.de)
	All rights reserved.
	Modified for SMP by Daisuke Aoyama (aoyama@peach.ne.jp)
*/
function update_controls(x) {
	var value = eval('(' + x + ')');

	update_uptime(value['uptime']);
	update_date(value['date']);
	update_memusage(value['memusage']);
	update_loadaverage(value['loadaverage']);
	update_cputemp(value['cputemp']);
	update_cputemp2(value['cputemp2']);
	update_cpufreq(value['cpufreq']);
	update_cpuusage(value['cpuusage']);
	update_cpuusage2(value['cpuusage2']);
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
	document.getElementById("uptime").innerHTML = value;
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

function update_cputemp2(value) {
	for (var idx = 0; idx < value.length; idx++) {
		if (document.getElementById("cputemp"+idx) == null)
			return;
		document.getElementById("cputemp"+idx).value = value[idx];
	}
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

function update_cpuusage2(value) {
	for (var idx = 0; idx < value.length; idx++) {
		if (document.getElementById("cpuusage"+idx) == null)
			continue;
		document.getElementById("cpuusage"+idx).value = value[idx] + '%';
		document.getElementById("cpuusageu"+idx).style.width = value[idx] + 'px';
		document.getElementById("cpuusagef"+idx).style.width = (100 - value[idx]) + 'px';
	}
}

function update_diskusage(value) {
	if (value == 'undefined' || value == null)
		return;
	for (var i=0; i<value.length; i++) {
		if (document.getElementById("diskusage_" + value[i].id + "_bar_used") == null)
			return;
		document.getElementById("diskusage_" + value[i].id + "_name").innerHTML = value[i].name;
		document.getElementById("diskusage_" + value[i].id + "_bar_used").style.width = value[i].percentage + 'px';
		document.getElementById("diskusage_" + value[i].id + "_bar_used").title = value[i]['tooltip'].used;
		document.getElementById("diskusage_" + value[i].id + "_bar_free").style.width = (100 - value[i].percentage) + 'px';
		document.getElementById("diskusage_" + value[i].id + "_bar_free").title = value[i]['tooltip'].available;
		document.getElementById("diskusage_" + value[i].id + "_capacity").innerHTML = value[i].capacity;
		document.getElementById("diskusage_" + value[i].id + "_total").innerHTML = value[i].size;
		document.getElementById("diskusage_" + value[i].id + "_used").innerHTML = value[i].used;
		document.getElementById("diskusage_" + value[i].id + "_free").innerHTML = value[i].avail;
	}
}

function update_swapusage(value) {
	if (value == 'undefined' || value == null)
		return;

	for (var i=0; i<value.length; i++) {
		if (document.getElementById("swapusage_" + value[i].id + "_bar_used") == null)
			return;
		document.getElementById("swapusage_" + value[i].id + "_bar_used").style.width = value[i].percentage + 'px';
		document.getElementById("swapusage_" + value[i].id + "_bar_used").title = value[i]['tooltip'].used;
		document.getElementById("swapusage_" + value[i].id + "_bar_free").style.width = (100 - value[i].percentage) + 'px';
		document.getElementById("swapusage_" + value[i].id + "_bar_free").title = value[i]['tooltip'].available;
		document.getElementById("swapusage_" + value[i].id + "_capacity").innerHTML = value[i].capacity;
		document.getElementById("swapusage_" + value[i].id + "_total").innerHTML = value[i].total;
		document.getElementById("swapusage_" + value[i].id + "_used").innerHTML = value[i].used;
		document.getElementById("swapusage_" + value[i].id + "_free").innerHTML = value[i].avail;
	}
}

function update_callback() {
	x_update_controls(update_controls);
	window.setTimeout('update_callback()', 5000);
}

window.setTimeout('update_callback()', 5000);
