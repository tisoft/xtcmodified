<?php

// $Id: iclear_error.php,v 1.2 2006/07/21 04:29:04 dis Exp $

  class iclearError {

    var $error = array();

    function iclearError() {
    }

    function isError() {
      return $this->getErrorCount();
    }

    function addError($msg) {
      if(!$this->error) {
        $this->error = array();
      }
      array_push($this->error, $msg);
    }

    function getErrorCount() {
      $rc = 0;
      if(is_array($this->error)) {
        $rc = sizeof($this->error);
      }
      return $rc;
    }

    function dumpErrorList() {
      if(sizeof($this->error)) {
        print join("\n", $this->error);
      }
    }

    function getErrorString($lineBreak = "\n") {
      $error = '';
      if(sizeof($this->error)) {
        $error =  join($lineBreak, $this->error);
      }
      return $error;
    }

  }


?>