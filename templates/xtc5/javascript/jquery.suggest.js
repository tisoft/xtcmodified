/* BOF - Tomcraft - 2009-07-08 - Suggest Styles */
/*
	This is the JavaScript file for the osCommerce AJAX Search Suggest

	You may use this code in your own projects as long as this
	copyright is left	in place.  All code is provided AS-IS.
	This code is distributed in the hope that it will be useful,
 	but WITHOUT ANY WARRANTY; without even the implied warranty of
 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

	For the rest of this code visit <a href="http://www.osCommerce-SSL.com" rel="nofollow">http://www.osCommerce-SSL.com</a>

	For a complete detailed tutorial on how this code works visit:
	<a href="http://www.dynamicajax.com/fr/AJAX_Suggest_Tutorial-271_290_312.html" rel="nofollow">http://www.dynamicajax.com/fr/AJAX_Suggest_Tutorial-271_290_312.html</a>

	For more AJAX code and tutorials visit <a href="http://www.DynamicAJAX.com" rel="nofollow">http://www.DynamicAJAX.com</a>

	Copyright 2006 Ryan Smith / 345 Technical / 345 Group.	

	Auf XT-Commerce portiert von TechWay (Steffen Decker) mit Unterstützung von Purecut (aus dem ecombase.de Forum)
	Copyright 2006 @ TechWay, Steffen Decker
*/
//Gets the browser specific XmlHttpRequest Object
function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		alert("Your Browser Sucks!\nIt's about time to upgrade don't you think?");
	}
}

//Our XmlHttpRequest object to get the auto suggest
var searchReq = getXmlHttpRequestObject();

//Called from keyup on the search textbox.
//Starts the AJAX request.
function searchSuggest() {
	if (searchReq.readyState == 4 || searchReq.readyState == 0) {
		var str = escape(document.getElementById('txtSearch').value);
		searchReq.open("GET", 'searchSuggest.php?search=' + str, true);
		searchReq.onreadystatechange = handleSearchSuggest;
		searchReq.send(null);
	}
}

//Called when the AJAX response is returned.
function handleSearchSuggest() {
	if (searchReq.readyState == 4) {
		var ss = document.getElementById('search_suggest')
		ss.innerHTML = '';
		var str = searchReq.responseText.split("\n");
		for(i=0; i < str.length - 1; i++) {
			//Build our element string.  This is cleaner using the DOM, but
			//IE doesn't support dynamically added attributes.
			var suggest = '<div onmouseover="javascript:suggestOver(this);" ';
			suggest += 'onmouseout="javascript:suggestOut(this);" ';
			suggest += 'onclick="javascript:setSearch(this.innerHTML);" ';
			suggest += 'class="suggest_link">' + str[i] + '</div>';
			ss.innerHTML += suggest;
		}
		if (i==0) {
			ss.style.visibility  = "hidden";
		} else {
			ss.style.visibility  = "visible";
		}
		//Schließen link einfügen
// BOF - Tomcraft - 2009-07-08 - Link korrigiert, damit Style-Zuweisung funktioniert
		//ss.innerHTML += '<p align="right"><a onmouseover="javascript:suggestOver(this);" onmouseout="javascript:suggestOut(this);" onClick="javascript:suggestClose(this);" class="suggest_link"><b>Fenster schlie&szlig;en</b></a></p>';
    ss.innerHTML += '<p align="right" onmouseover="javascript:suggestOver(this);" onmouseout="javascript:suggestOut(this);" onClick="javascript:suggestClose(this);" class="suggest_link"><b>Fenster schlie&szlig;en</b></p>';
// EOF - Tomcraft - 2009-07-08 - Link korrigiert, damit Style-Zuweisung funktioniert
	}
}
// Close Function
function suggestClose (div_value) {
	document.getElementById('search_suggest').innerHTML = '';
	document.getElementById('search_suggest').style.visibility  = "hidden";
}

//Mouse over function
function suggestOver(div_value) {
	div_value.className = 'suggest_link_over';
}
//Mouse out function
function suggestOut(div_value) {
	div_value.className = 'suggest_link';
}
//Click function
function setSearch(value) {
	// HTML-TAGS entfernen
  	var newvalue = value.replace(/<.*?>/gi, '');
	//Kategorienamen entfernen (fängt mit   an)
	var Suche =	newvalue.indexOf(" ");
	var produktname = newvalue.substring(0,Suche);
	document.getElementById('txtSearch').value = produktname;
	document.getElementById('search_suggest').innerHTML = '';
	document.getElementById('search_suggest').style.visibility  = "hidden";
	//zum Suchergebnis weiterleiten
	top.location.href = "advanced_search_result.php?keywords=" + produktname;
}
/* EOF - Tomcraft - 2009-07-08 - Suggest Styles */