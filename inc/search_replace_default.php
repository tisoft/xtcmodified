<?php
/*-----------------------------------------------------------------------
    $Id$
	
    Zeichenkodierung: ISO-8859-1
   
   Version 1.06 rev.04 (c) by web28  - www.rpa-com.de   
------------------------------------------------------------------------*/
function shopstat_getRegExps(&$search, &$replace)
{
    $search     = array(
                        "'\s&\s'",          //--Kaufm�nnisches Und mit Blanks muss raus
						"'[\r\n\s]+'",	    // strip out white space
						"'&(quote|#34);'i",	// replace html entities
						"'&(amp|#38);'i",
						"'&(lt|#60);'i",
						"'&(gt|#62);'i",
						"'&(nbsp|#160);'i",
						"'&(iexcl|#161);'i",
						"'&(cent|#162);'i",
						"'&(pound|#163);'i",
						"'&(copy|#169);'i",
                        "'&'",              //--Kaufm�nnisches Und wird +
                        "'%'",              //--Prozent muss weg
                        "/[\[\({]/",        //--�ffnende Klammern nach Bindestriche
                        "/[\)\]\}]/",       //--schliessende Klammern weg
                        "/�/",              //--Umlaute etc.
                        "/�/",              //--Umlaute etc.
                        "/�/",              //--Umlaute etc.
                        "/�/",              //--Umlaute etc.
                        "/�/",              //--Umlaute etc.
                        "/�/",              //--Umlaute etc.
                        "/�/",              //--Umlaute etc.
                        "/'|\"|�|`/",       //--Anf�hrungszeichen weg.
                        "/[:,\.!?\*\+]/",   //--Doppelpunkte, Komma, Punkt etc. weg.
                        );
    $replace    = array(
                        "-",
						"-",
					    "\"",
						"-",
						"<",
						">",
						"",
						chr(161),
						chr(162),
						chr(163),
						chr(169),
                        "-",
						"+",
                        "-",
                        "",
                        "ss",
                        "ae",
                        "ue",
                        "oe",
                        "Ae",
                        "Ue",
                        "Oe",
                        "",
                        ""
                        );

}
?>