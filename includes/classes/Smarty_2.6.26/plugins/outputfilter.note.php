<?php
// SIE SIND IM BEGRIFF ETWAS ZU ÄNDERN, WAS NICHT FAIR IST. SIE MÖCHTEN MIT
// DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
// UND MONATE VERBRACHT, DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
// DANKESCHÖN AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
// ODER KRATZEN SIE AUCH VON IHREN ELEKTROGERÄTEN IM HAUS DIE MARKENZEICHEN AB!

/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2006 xt:Commerce (outputfilter.note.php); www.xtcommerce.com

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

function smarty_outputfilter_note($tpl_output, &$smarty) {

  $cop='<div class="copyright"><a href="http://www.xtc-modified.org" target="_blank">' . PROJECT_VERSION . '</a>' . '&nbsp;&copy;&nbsp;' . date('Y') . '&nbsp;provides no warranty and is redistributable under the <a href="http://www.fsf.org/licensing/licenses/gpl.txt" target="_blank">GNU General Public License</a><br />eCommerce Engine 2006 based on <a href="http://www.xt-commerce.com/" rel="nofollow" target="_blank">xt:Commerce</a></div>';

  return $tpl_output.$cop;
}

// SIE SIND IM BEGRIFF ETWAS ZU ÄNDERN, WAS NICHT FAIR IST. SIE MÖCHTEN MIT
// DIESER SOFTWARE GELD VERDIENEN ODER KUNDEN GEWINNEN. SIE HABEN NICHT STUNDEN 
// UND MONATE VERBRACHT, DIESE SOFTWARE ZU ENTWICKELN UND ZU VERBESSEREN. ALS
// DANKESCHÖN AN DIE ENTWICKLER UND CODER LASSEN SIE DIESE DATEI, WIE SIE IST 
// ODER KRATZEN SIE AUCH VON IHREN ELEKTROGERÄTEN IM HAUS DIE MARKENZEICHEN AB!
?>