<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   Debug log class by Franky

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

/* log is a logging class
 * @author Franky
 * Usage: put following expression anywhere in the code
 * $log->logfile($variable);
 */

class debug {
  /**
   * Initialize the class so that the data is in a known state.
   * @access public
   * @return void
   */
  function logfile($data) {
    $filename = DIR_FS_CATALOG.'/log/log.txt'; // log directory with chmod 777
    if (file_exists($filename)) {
      $f = @fopen($filename,'a+');
    } else {
      $f = @fopen($filename,'w+');
    }
    flock($f,2);
    fputs($f, $data."\n");
    flock($f,3);
    fclose($f);
    chmod ($filename, 0777);
  }
}
?>