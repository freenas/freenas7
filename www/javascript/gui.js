function showElementById(id, state) {
	var element = document.getElementById(id);
	switch (state) {
		case "show":
			element.style.display = "";
			break;
		case "hide":
			element.style.display = "none";
	}
}

function onKeyPress(evt) {
	var evt = (evt) ? evt : ((event) ? event : null);
	var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
	if ((evt.keyCode == 13) && ((node.type=="text") || node.type=="checkbox")) { return false; }
}

document.onkeypress = onKeyPress;
