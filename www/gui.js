function showElementById(id,state) {
	element = document.getElementById(id);
	if ('show' == state) {
		element.style.display = '';
	}
	if ('hide' == state) {
		element.style.display = 'none';
	}
}

function showhideMenu(tspan, tri) {
	tspanel = document.getElementById(tspan);
	triel = document.getElementById(tri);
	if (tspanel.style.display == 'none') {
		tspanel.style.display = '';
		triel.src = "/tri_o.gif";
	} else {
		tspanel.style.display = 'none';
		triel.src = "/tri_c.gif";
	}
}
