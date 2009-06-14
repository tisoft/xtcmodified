<?php


# SIE SIND IM BEGRIFF ETWAS ZU ÄNDERN, WAS NICHT FAIR IST. SIE MÖCHTEN MIT
# DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
# UND MONATE VERBRACHT DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
# DANKESCHÖN AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
# ODER KRATZEN SIE AUCH VON IHREN ELEKTROGERÄTEN IM HAUS DIE MARKENZEICHEN AB!!!!

?>

<?php

/* -----------------------------------------------------------------------------------------

   $Id: outputfilter.note.php 779 2005-02-19 17:19:28Z novalis $

   XT-Commerce - community made shopping
   http://www.xt-commerce.com
   Copyright (c) 2003 XT-Commerce

   -----------------------------------------------------------------------------------------

   Released under the GNU General Public License

   ---------------------------------------------------------------------------------------*/



function smarty_outputfilter_note($tpl_output, &$smarty) {

// BOF - Christian - 2009-06-14 - modified copyright phrase
//	$cop='<div align="center" style="font-size:11px;">eCommerce Engine &copy; 2006 <a rel="nofollow" href="http://www.xt-commerce.com/" target="_blank">xt:Commerce Shopsoftware</a> | eCommerce Engine modifiziert 2009 von <a href="http://www.jung-gestalten.com/" rel="nofollow" target="_blank">JUNG/GESTALTEN.com</a></div>';
	$cop='<div align="center" style="font-size:11px;">eCommerce Engine 2006 based on <a href="http://www.xt-commerce.com/" target="_blank">xt:Commerce</a><br />eCommerce Engine &copy; 2008 - 2009 <a href="http://www.xtc-modified.org" target="_blank">xtcModfied.org</a> supported under GNU/GPL</div>';
// EOF - Christian - 2009-06-12 - modified copyright phrase

//BOF - Dokuman - 2009-05-03 - Produce Valid Links
	//for ($i=0; $i<count($str_arr);$i++) $cop.=chr($str_arr[$i]);

    function NoEntities($Input) {
      $TransTable1 = get_html_translation_table (HTML_ENTITIES);
      foreach($TransTable1 as $ASCII => $Entity) {
        $TransTable2[$ASCII] = '&#'.ord($ASCII).';';
      }
      $TransTable1 = array_flip ($TransTable1);
      $TransTable2 = array_flip ($TransTable2);
      return strtr (strtr ($Input, $TransTable1), $TransTable2);
    }
    function AmpReplace($Treffer) {
      return $Treffer[1].htmlentities(NoEntities($Treffer[2])).$Treffer[3];
    }
    $tpl_output = preg_replace_callback("/(<a[^>]*href=\"|<form[^>]*action=\")(.*)(\"[^<]*>)/Usi","AmpReplace",$tpl_output);
    $tpl_output = preg_replace_callback("/(<a[^>]*href='|<form[^>]*action=')(.*)('[^<]*>)/Usi","AmpReplace",$tpl_output);
//EOF - Dokuman - 2009-05-03 - Produce Valid Links

    return $tpl_output.$cop;

}

?>


<?php


# SIE SIND IM BEGRIFF ETWAS ZU ÄNDERN, WAS NICHT FAIR IST. SIE MÖCHTEN MIT
# DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
# UND MONATE VERBRACHT DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
# DANKESCHÖN AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
# ODER KRATZEN SIE AUCH VON IHREN ELEKTROGERÄTEN IM HAUS DIE MARKENZEICHEN AB!!!!

?>