"use strict"

document.addEventListener('DOMContentLoaded', function() {
	var date = new Date();
	var yearElem = document.getElementsByClassName("yearElem");
	
	for(var x=0; x<yearElem.length; x++)
		yearElem[x].innerHTML = date.getFullYear();
});