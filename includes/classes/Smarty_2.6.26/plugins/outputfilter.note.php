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

# SIE SIND IM BEGRIFF ETWAS ZU ÄNDERN, WAS NICHT FAIR IST. SIE MÖCHTEN MIT
# DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
# UND MONATE VERBRACHT DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
# DANKESCHÖN AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
# ODER KRATZEN SIE AUCH VON IHREN ELEKTROGERÄTEN IM HAUS DIE MARKENZEICHEN AB!!!!

function smarty_outputfilter_note($tpl_output, &$smarty) {

  $cop='<div class="copyright"><a href="http://www.xtc-modified.org" target="_blank">' . PROJECT_VERSION . '</a>' . '&nbsp;' . '&copy;' . date('Y') . '&nbsp;' . 'provides no warranty and is redistributable under the <a href="http://www.fsf.org/licensing/licenses/gpl.txt" target="_blank">GNU General Public License</a><br />eCommerce Engine 2006 based on <a href="http://www.xt-commerce.com/" rel="nofollow" target="_blank">xt:Commerce</a></div>';

//BOF - DokuMan - Removed "Produce Valid Links", since this became void with the modified shopstat_functions.php
/* 
  //BOF - Dokuman - 2009-05-03 - Produce Valid Links
  if (!function_exists('NoEntities')) {
    function NoEntities($Input) {
      $TransTable1 = get_html_translation_table (HTML_ENTITIES);
      foreach($TransTable1 as $ASCII => $Entity) {
        $TransTable2[$ASCII] = '&#'.ord($ASCII).';';
      }
      $TransTable1 = array_flip ($TransTable1);
      $TransTable2 = array_flip ($TransTable2);
      return strtr (strtr ($Input, $TransTable1), $TransTable2);
    }
  }
  
  if (!function_exists('AmpReplace')) {
    function AmpReplace($Treffer) {
      return $Treffer[1].htmlentities(NoEntities($Treffer[2])).$Treffer[3];
    }
  }

  $tpl_output = preg_replace_callback("/(<[^>]*['\"])(http[s]?\:\/\/[^'\"]*)(['\"][^<]*>)/Usi","AmpReplace",$tpl_output);
  //EOF - Dokuman - 2009-05-03 - Produce Valid Links
*/
//EOF - DokuMan - Removed "Produce Valid Links", since this became void with the modified shopstat_functions.php

//BOF - DokuMan - replace ampersands, rest is covered by the modified shopstat_functions.php
$tpl_output  = preg_replace("'&\s'","& ",$tpl_output);
//EOF - DokuMan - replace ampersands, rest is covered by the modified shopstat_functions.php

  return $tpl_output.$cop;
}

# SIE SIND IM BEGRIFF ETWAS ZU ÄNDERN, WAS NICHT FAIR IST. SIE MÖCHTEN MIT
# DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
# UND MONATE VERBRACHT DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
# DANKESCHÖN AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
# ODER KRATZEN SIE AUCH VON IHREN ELEKTROGERÄTEN IM HAUS DIE MARKENZEICHEN AB!!!!
?>