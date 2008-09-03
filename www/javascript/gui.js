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
