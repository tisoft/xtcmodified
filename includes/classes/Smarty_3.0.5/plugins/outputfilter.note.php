<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 xt:Commerce (campaigns.php 1117 2005-07-25)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

# SIE SIND IM BEGRIFF ETWAS ZU �NDERN, WAS NICHT FAIR IST. SIE M�CHTEN MIT
# DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
# UND MONATE VERBRACHT DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
# DANKESCH�N AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
# ODER KRATZEN SIE AUCH VON IHREN ELEKTROGER�TEN IM HAUS DIE MARKENZEICHEN AB!!!!

function smarty_outputfilter_note($tpl_output, &$smarty) {

// BOF - Christian - 2009-06-14 - modified copyright phrase
//	$cop='<div align="center" style="font-size:11px;">eCommerce Engine &copy; 2006 <a rel="nofollow" href="http://www.xt-commerce.com/" target="_blank">xt:Commerce Shopsoftware</a> | eCommerce Engine modifiziert 2009 von <a href="http://www.jung-gestalten.com/" rel="nofollow" target="_blank">JUNG/GESTALTEN.com</a></div>';
	$cop='<div class="copyright"><a href="http://www.xtc-modified.org" target="_blank">' . PROJECT_VERSION . '</a>' . '&nbsp;' . '&copy;' . date('Y') . '&nbsp;' . 'provides no warranty and is redistributable under the <a href="http://www.fsf.org/licensing/licenses/gpl.txt" target="_blank">GNU General Public License</a><br />eCommerce Engine 2006 based on <a href="http://www.xt-commerce.com/" rel="nofollow" target="_blank">xt:Commerce</a></div>';
// EOF - Christian - 2009-06-12 - modified copyright phrase

//BOF - Dokuman - 2009-05-03 - Produce Valid Links
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
    $tpl_output = preg_replace_callback("/(<[^>]*['\"])(http[s]?\:\/\/[^'\"]*)(['\"][^<]*>)/Usi","AmpReplace",$tpl_output);
//EOF - Dokuman - 2009-05-03 - Produce Valid Links

//BOF - Dokuman - 2010-11-25 - html output minify (thanks to DJ-Ready)
/* Uncomment the following 2 lines to remove linebreaks and comments from html-output */
/* lines commented for easier template debugging */
//$regex = array('{\t|\r|\n}', '{(/\*(.*?)\*/)}', '/<!--(.*)-->/Uis',);
//$tpl_output = preg_replace($regex, '', $tpl_output);
//EOF - Dokuman - 2010-11-25 - html output minify (thanks to DJ-Ready)

    return $tpl_output.$cop;
}

# SIE SIND IM BEGRIFF ETWAS ZU �NDERN, WAS NICHT FAIR IST. SIE M�CHTEN MIT
# DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
# UND MONATE VERBRACHT DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
# DANKESCH�N AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
# ODER KRATZEN SIE AUCH VON IHREN ELEKTROGER�TEN IM HAUS DIE MARKENZEICHEN AB!!!!
?>