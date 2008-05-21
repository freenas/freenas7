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

function showhideMenu(tspan, tri) {
	var tspanel = document.getElementById(tspan);
	var triel = document.getElementById(tri);
	if (tspanel.style.display == "none") {
		tspanel.style.display = "";
		triel.src = "/tri_o.gif";
	} else {
		tspanel.style.display = "none";
		triel.src = "/tri_c.gif";
	}
}
