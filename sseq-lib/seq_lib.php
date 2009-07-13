<?php
/* SEQ_LIB : Security library
 *
 * Main configuration
 *
 * This software is open-source. License: GNUv3
 * Autor: Erich Kachel; info@erich-kachel.de
 * http://www.erich-kachel.de/seq_lib
*/


/* $_SEQ_BASEDIR
 * Absolute path to seq_lib directory.
 * CAUTION: ALWAYS ENDS WITH SLASH (/)!
 */
$_SEQ_BASEDIR = $_SEQ_BASEDIR ? $_SEQ_BASEDIR : $_SERVER['DOCUMENT_ROOT'] . '/sseq-lib/';

/* $_SEQ_SECURE_COOKIES
 * Adds security checks to cookie data. Sets and checks absolute cookie lifetime.
 * Shortens cookie lifetime.
 * OPTIONS:
 * 1: secure sessions (recommended); 0: do not 
 */
$_SEQ_SECURE_COOKIES = 1;

/* $_SEQ_SECURE_SESSION
 * Adds security checks to session data. Sets and checks absolute session lifetime.
 * Sets and checks idletime of the session lifetime. Shortens cookie lifetime, sets httpOnly-Flag.
 * OPTIONS:
 * 1: secure sessions (recommended); 0: do not 
 */
$_SEQ_SECURE_SESSION = 1;

/* $_SEQ_ONERROR_REDIRECT_TO
 * If set, the user will be redirected there in case of an security error, invalid
 * tokens, detected attacks.
 * OPTIONS:
 * FILE: redirect to (recommended); EMPTY: do not redirect
 */
/* POSSIBLE SECURITY BREACH!! ALWAYS SET THIS, EVEN WHEN EMPTY!! */
$_SEQ_ONERROR_REDIRECT_TO = '/index.php'; 

/* $_SEQ_SESSION_HEADERSCHECK
 * The session is only valid, when the same webbrowser is used. A weak but
 * recommended protection against sessions stealing.
 * OPTIONS:
 * 1: check (recommended); 0: do not check 
 */
$_SEQ_SESSION_HEADERSCHECK = 1;

/* $_SEQ_ERRORS
 * Shows PHP error messages which have been supressed by the library. It also
 * shows errors which has been set with the function "SEQ_ERROR()".
 * OPTIONS:
 * 1: show; 0: hide (recommended)
 */
$_SEQ_ERRORS = 0;

/* $_SEQ_LOG
 * Error messages, detected attacks and token failure can be written to an logfile.
 * CAUTION: The logfile can get very big after some time.
 * OPTIONS:
 * 1: show (recommended); 0: hide
 */
$_SEQ_LOG = 1; /* 1: write log; 0: do not */

/* $_SEQ_SESSIONLIFETIME
 * Sets the session lifetime. Overwrites PHPs own settings. Session lifetime gets
 * renewed with every usage of the application.
 * OPTIONS:
 * 3600
 */  
$_SEQ_SESSIONLIFETIME = 7200; /* two hours */

/* $_SEQ_SESSIONABSOLUTELIFETIME
 * Sets the absolute session lifetime. Absolute lifetime cannot be renewed. After
 * this time, the current session will be deleted.
 * OPTIONS:
 * 21600
 */    
$_SEQ_SESSIONABSOLUTELIFETIME = 21600; /* six hours */

/* $_SEQ_TOKENLIFETIME
 * Sets the absolute lifetime of CSRF-tokens. Absolute lifetime cannot be renewed. After
 * this time, the token will be invalid.
 * OPTIONS:
 * 7200
 */  
$_SEQ_TOKENLIFETIME = $_SEQ_SESSIONLIFETIME;

/* $_SEQ_SESSIONREFRESH
 * Sets the interval to renew the session id. All data will be transcripted.
 * OPTIONS:
 * 0: refresh on each call
 */  
$_SEQ_SESSIONREFRESH = $_SEQ_SESSIONLIFETIME;

/* $_SEQ_IDS_ONATTACK_ACTION
 * What happens when an attack was detected.
 * "delay": Delays response for 50 seconds.
 * "logout": Deletes current session.
 * If "logout" is used with "$_SEQ_ONERROR_REDIRECT_TO", the user will be redirected
 * to there.
 * OPTIONS (separated by space):
 * delay, logout, redirect
 */                 
$_SEQ_IDS_ONATTACK_ACTION = 'logout redirect';






/****************************************************************
* No changes from here!
* MEASURE AGAINST INFORMATION DISCLOSURE IN CASE OF MALFUNCTION
*****************************************************************/

$_SEQ_START_OK = true;
if (!$_SEQ_ERRORS) {
    error_reporting(E_USER_ERROR | E_USER_WARNING);
    set_error_handler('seq_install_error_handler_');
}
function seq_install_error_handler_($code_ = '', $msg_ = '', $file_ = '', $line_ = '') {
    global $_SEQ_START_OK;
    if ($_SEQ_START_OK) {
        switch ($code_) {
        case E_ERROR:
        case E_WARNING:
            /* DON'T ADD $msg_, $file_ OR $line_ TO THIS MESSAGE! RISC OF INFORMATION DISCLOSURE! */
            echo ('$_SEQ_BASEDIR - path in SSEQ-LIB configuration seems to be wrong!');
            $_SEQ_START_OK = false;
            break;
        }
    }
}
/* DON'T CHANGE THIS PATH ! */
include_once($_SEQ_BASEDIR . 'seq_lib/seq_lib.php');
restore_error_handler();

?>