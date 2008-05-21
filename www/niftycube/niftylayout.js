window.onload=function(){
	Nifty("td.tabcont");
	Nifty("div#errorbox");
	Nifty("div#infobox");
	Nifty("td.listtopic","small");
	Nifty("td.optsect_t","small");
	// Use rounded corners only if browser is not IE (to prevent
	// rendering errors in tabs).
	var agt = navigator.userAgent.toLowerCase();
	if (!((agt.indexOf("msie") != -1) && (agt.indexOf("opera") == -1))) {
		Nifty("li.tabact","transparent top");
		Nifty("li.tabinact","transparent top");
	}
}
