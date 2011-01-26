<?php
/* -----------------------------------------------------------------------------------------
  $Id$

  xtcModified - community made shopping
  http://www.xtc-modified.org

  Copyright (c) 2010 xtcModified
  -----------------------------------------------------------------------------------------
  based on:
  Debug log class by Franky
  Debug FirePHP by DokuMan - Issue output to "FirePHP" (FireFox-Extension),
                             make sure it is installed before using this function

  Released under the GNU General Public License
  ---------------------------------------------------------------------------------------*/

/**
 * debug
 *
 * @author Franky
 * @access public
 * log is a logging class
 * Initialize the class so that the data is in a known state.
 * class.debug.php is included in /includes/application_top.php
*/
class debug {

  var $firephp = "";

  /**
   * debug::logfile()
   *
   * Usage: put following expression anywhere in the code:
   * $log->logfile($variable);
   * @access public
   * @return void
   */
  function logfile($data) {
    $filename = DIR_FS_CATALOG.'/log/log.txt'; // log directory with chmod 777
    if (file_exists($filename)) {
      $f = @fopen($filename, 'a+');
    } else {
      $f = @fopen($filename, 'w+');
    }
    flock($f, 2);
    fputs($f, $data."\n");
    flock($f, 3);
    fclose($f);
    chmod($filename, 0777);
  }

  /**
   * debug::GetFirePHP()
   *
   * @access public
   * @return void
   */
  function GetFirePHP() {
    define('FIREPHPFILE', DIR_WS_INCLUDES.'classes/class.debug.firephp.php');

    if (empty($this->firephp)) {
      if (file_exists(DIR_FS_DOCUMENT_ROOT.FIREPHPFILE)) {
        include (FIREPHPFILE);
        ob_start();
        $this->firephp = FirePHP::getInstance(true);
        return $this->firephp;
      }
    } else {
      return $this->firephp;
    }
  }

  /**
   * debug::is_assoc()
   *
   * @return
   */
  function is_assoc($array) {
    return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
  }

  /**
   * debug::firephp_command()
   *
   * Usage: put following expression anywhere in the code:
   * $log->firephp_command('vardump', $_SERVER);
   * $log->firephp_command('trace');
   * $log->firephp_command('sqltime', $sql_query);
   * @return
   */
  function firephp_command($command, $variables = array()) {
    $firephp = $this->GetFirePHP();
    if (isset($firephp)) {
      switch ($command) {

        case 'vardump': {
            if (!is_array($variables)) {
              $firephp->log("vardump:  => ".$variables);
            } else {

              if ($this->is_assoc($variables)) {
                $firephp->group("vardump:  (associative array)");
                $firephp->log("(");
                foreach ($variables as $var => $value) {
                  $firephp->log("['".$var."'] => ".$value);
                }
              } else {
                $firephp->group("vardump:  (associative array)");
                $firephp->log("(");
                foreach ($variables as $var) {
                  $firephp->log($var);
                }
              }
              $firephp->log(")");
              $firephp->groupEnd();
            }
            break;
          }

        case 'trace': {
            $options = array('maxObjectDepth' => 10, 'maxArrayDepth' => 20, 'useNativeJsonEncode' => true, 'includeLineNumbers' => false);
            //$firephp->setObjectFilter('debug',array());
            $firephp->setOptions($options);
            $firephp->trace('Backtrace');
            break;
          }

        case 'sqltime': {
            $firephp->registerErrorHandler($throwErrorExceptions = false);
            $firephp->registerExceptionHandler();
            $firephp->registerAssertionHandler($convertAssertionErrorsToExceptions = true, $throwAssertionExceptions = false);

            try {
              $time_start = microtime(true);
              $result = xtc_db_query($variables);
              $time_end = microtime(true);
              $time = $time_end - $time_start;
              if (empty($result)) {
                throw new Exception('SQL-Result Error (no result)');
              } else {
                $firephp->info($time, 'SQL-query time');
                $firephp->info($result, 'SQL-Result');
                while ($row = xtc_db_fetch_array($result)) {
                  $firephp->log($row);
                }
              }

            }
            catch (Exception $e) {
              $firephp->error($e); // or FB::
            }

            break;
          }
      } // end switch
    } //end isset

  }
}
?>