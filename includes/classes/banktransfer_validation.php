<?php 
/* -----------------------------------------------------------------------------------------
   $Id$   

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(banktransfer_validation.php,v 1.17 2003/02/18 18:33:15); www.oscommerce.com
   (c) 2003   nextcommerce (banktransfer_validation.php,v 1.4 2003/08/1); www.nextcommerce.org
   (c) 2004 - 2006 fmce.de
   (c) 2004 - 2006 discus24.de
   (c) 2006 xt:Commerce
   (c) 2004 - 2010 Frank Maroke
   (c) 2010 Christian Rothe (banktransfer_validation.php 2010-01-05)
   (c) 2010-2011 Nico Sommer

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contributions:
   OSC German Banktransfer v0.85a         Autor:  Dominik Guder <osc@guder.org>
   Extensioncode:               Marcel Bossert-Schwab <info@opensourcecommerce.de> (mbs)
   New methods 2005 - 2010:         Frank Maroke (FrankM) <info@fmce.de>
   New methods 2010 - 2011:         Nico Sommer <madness@gmx.de>

   Der Code dieser Klasse basiert auf dem Basis Klassenmodul "cpp_dd_de_check.php":
   Methoden der Deutschen Bundesbank zur PRZ - Pruefung. 
   Aktuelle Version der Pr�fverfahren http://www.bundesbank.de/zahlungsverkehr/zahlungsverkehr_pruefziffernberechnung.php
   Aktuelle Version der BLZ's: http://www.bundesbank.de/zahlungsverkehr/zahlungsverkehr_bankleitzahlen_download.php

   Revision includes all changes until: 03/2011

   Released under the GNU General Public License 
   ---------------------------------------------------------------------------------------*/

class AccountCheck {

/* Folgende Returncodes werden �bergeben                                      */
/*                                                                            */
/* 0 -> Kontonummer & BLZ OK                                                  */
/* 1 -> Kontonummer & BLZ passen nicht                                        */
/* 2 -> F�r diese Kontonummer ist kein Pr�fziffernverfahren definiert         */
/* 3 -> Dieses Pr�fziffernverfahren ist noch nicht implementiert              */
/* 4 -> Diese Kontonummer ist technisch nicht pr�fbar                         */
/* 5 -> BLZ nicht gefunden                                                    */
/* 8 -> Keine BLZ �bergeben                                                   */
/* 9 -> Keine Kontonummer �bergeben                                           */
/* 10 -> Kein Kontoinhaber �bergeben                                          */
/* 128 -> interner Fehler,der zeigt, das eine Methode nicht implementiert ist */
/*                                                                            */

var $Bankname; // Enth�lt den Namen der Bank bei der Suche nach BLZ
var $PRZ; //Enth�lt die Pr�fziffer

////
// Diese function gibt die Bankinformationen aus der csv-Datei zur�ck*/
  function csv_query($blz) {
    $cdata = -1;
    $fp = fopen(DIR_WS_INCLUDES . 'data/blz.csv', 'r');
    while ($data = fgetcsv($fp, 1024, ";")) {
      if ($data[0] == $blz){
        $cdata = array ('blz' => $data[0],
                        'bankname' => $data[1],
                        'prz' => $data[2]);
      }
    }
    return $cdata;
  }

////
// Diese function gibt die Bankinformationen aus der Datenbank zur�ck*/
  function db_query($blz) {
    $blz_query = xtc_db_query("SELECT * from banktransfer_blz WHERE blz = '" . $blz . "'");
    if (xtc_db_num_rows($blz_query)){
      $data = xtc_db_fetch_array ($blz_query);
    }else
      $data = -1;
    return $data;
  }

////
// Diese function gibt die Bankinformationen aus der Datenbank zur�ck*/
  function query($blz) {
    if (MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ == 'true' && defined(MODULE_PAYMENT_BANKTRANSFER_DATABASE_BLZ))
      $data = $this->db_query($blz);
    else
      $data = $this->csv_query($blz);
    return $data;
  }

////
  // Diese Funktion gibt die Einer einer Zahl zwischen 0 und 99 zurueck.
  function OnlyOne($Digit) {
    return $Digit = $Digit % 10;
  }  /* End of OnlyOne */

  // Diese Funktion berechnet die Quersumme einer Zahl zwischen 0 und 99.
  function CrossSum($Digit) {
    $CrossSum = $Digit;
    if ($Digit > 9) {
      $Help1 = substr($Digit,0,1);
      $Help2 = substr($Digit,1,1);
      $CrossSum = (int) $Help1 + (int) $Help2;
    }
    return $CrossSum;
  }  /* End of CrossSum */

  // Auff�llen der Konto-Nr. mit '0' auf 10 Stellen.
  function ExpandAccount($AccountNo) {
    $AccountNo = str_pad($AccountNo, 10, "0", STR_PAD_LEFT);

    while (strlen($AccountNo) > 10) {
      $AccountNo = substr($AccountNo, 1);
    }
    return $AccountNo;
  }  /* End of ExpandAccount */

  // Erweiterte ExpandAccount fuer Methode C5:
  // Entfernt die f�hrenden Nullen einer Kontonummer
  // und gibt den Integer zusammen mit der Laenge zurueck.
  function ExpandAccountExtended($AccountNo) {
    $AccountNoLong        = $this->ExpandAccount($AccountNo);
    $AccountNoShort       = ltrim($AccountNoLong, "0");
    $AccountNoShortLen    = strlen($AccountNoShort);
    $aAccountNo = array(
      'AccountNoLong'     => $AccountNoLong,
      'AccountNoShort'    => $AccountNoShort,
      'AccountNoShortLen' => $AccountNoShortLen,
    );
    return $aAccountNo;
  }  /* End of ExpandAccountExtended */


  /* --- Changed FrankM 20061206, 20070822, 20080717, 20100602 --- */
  function Method00($AccountNo, $Significance, $Checkpoint, $Modulator=10, $LeaveCheckpoint=0, $DoNotExpand=0) {
    $Help = 0;
    $Method00 = 1;

    // Methoden der Bundesbank C6 und D1 uebergeben die finale Kontonummer, $DoNotExpand = 1.
    if ($DoNotExpand == 0) {
      $AccountNo = $this->ExpandAccount($AccountNo);
    }

    // Pruefziffer ermitteln..
    $PNumber = substr($AccountNo, $Checkpoint-1, 1);

    // Sonderfall Methoden der Bundesbank C6 und D1, zur Pruefung letzte Stelle  entfernen.
    if ($Checkpoint == 16) {
      $AccountNo = substr($AccountNo, 0, -1);
  }


    if ($LeaveCheckpoint == 0) {
      for ($Run = 0; $Run < strlen($Significance); $Run++) {
        $Help += $this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
      }
    // Sonderfall fuer Methode 57
    }else{
      $HelpFirst = substr($AccountNo,0,2);
      $HelpCheckP = substr($AccountNo,($Checkpoint - 1),1);
      $HelpSecond = substr($AccountNo,3,7);
      $AccountNo = $HelpFirst . $HelpSecond . $HelpCheckP;
      for ($Run = 0; $Run < strlen($Significance); $Run++) {
        $Help += $this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
      }
    }

    $Help = $Help % $Modulator;
    $Checksum = $Modulator - $Help;

    if ($Checksum == $Modulator) {
      $Checksum = 0;
    }

    if ($Checksum == $PNumber) {
      $Method00 = 0;
    }

    return $Method00;
  }  /* End of Method00 */


  function Method01($AccountNo, $Significance) {
    $Help = 0;
     $Method01 = 1;
     $AccountNo = $this->ExpandAccount($AccountNo);
     for ($Run = 0; $Run < strlen($Significance); $Run++) {
       $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
     }
     $Help = $this->OnlyOne($Help);
     $Checksum = 10 - $Help;

     if ($Checksum == 10) {
       $Checksum = 0;
     }
     if ($Checksum == substr($AccountNo,-1)) {
       $Method01 = 0;
     }
     return $Method01;
  }  /* End of Method01 */


  function Method02($AccountNo , $Significance, $Modified) {
    $Help = 0;
     $Method02 = 1;
     $AccountNo = $this->ExpandAccount($AccountNo);
     switch ($Modified) {
       case FALSE :
        for ($Run = 0;$Run < strlen($Significance);$Run++) {
          $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
        }
        break;
      case TRUE :
        for ($Run = 0;$Run < strlen($Significance);$Run++) {
          $Help += substr($AccountNo,$Run,1) * HexDec(substr($Significance,$Run,1));
        }
        break;
    }
    $Help = $Help % 11;
    if ($Help == 0) {
      $Help = 11;
    }
    if ($Help <> 1) {
      $Checksum = 11 - $Help;
      if ($Checksum == substr($AccountNo,-1)) {
        $Method02 = 0;
      }
    }
     return $Method02;
  }  /* End of Method02 */

  /* --- Hotfix FrankM 20081208 --- */
  function Method06($AccountNo, $Significance, $Modified, $Checkpoint, $Modulator) {
    $Help = 0;
    $Method06 = 1;
     $AccountNo = $this->ExpandAccount($AccountNo);
     switch ($Modified) {
       case FALSE :
        for ($Run = 0; $Run < strlen($Significance);$Run++) {
          $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
        }
        break;
      case TRUE  :
        for ($Run = 0; $Run < strlen($Significance);$Run++) {
          $Help += (substr($AccountNo,$Run,1) * HexDec(substr($Significance,$Run,1)));
        }
        break;
    }
    $Help = $Help % $Modulator;
    $Checksum = $Modulator - $Help;
  /* --- Changed Nico Sommer 20110202 --- */
    // Bedingung bei Modulator 7 - wenn kein Rest
    if ($Help < 1 && $Modulator == 7) {
      $Checksum = 0;
    }
  /* --- Changed Nico Sommer 20110202 --- */
  // Bedingung bei Modulator 11 - wenn kein Rest
    if ($Help < 1 && $Modulator == 11) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,$Checkpoint-1,1)) {
      $Method06 = 0;
    }
    return $Method06;
  }  /* End of Method06 */

  function Method16($AccountNo , $Significance, $Checkpoint) {
    $Help = 0;
    $Method16 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,$Checkpoint-1,1)) {
       $Method16 = 0;
     }
     if ($Help == 1) {
       if ($Checksum == substr($AccountNo,Checkpoint - 2,1)) {
         $Method16 = 0;
       }
     }
     return $Method16;
   }  /* End of Method16 */

  function Method90($AccountNo , $Significance ,$Checkpoint, $Modulator) {
    $Help = 0;
    $Method90 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % $Modulator;
    $Checksum = $Modulator - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,$Checkpoint -1, 1)) {
      $Method90 = 0;
    }
    return $Method90;
  } /* End of Method90 */


  /* ----- Endgueltige Funktionen der einzelnen Berechnungsmethoden. ---- */

  function Mark00($AccountNo) {
    $Mark00 = $this->Method00($AccountNo, '212121212', 10);
    return $Mark00;
  }  /* End of Mark00 */

  function Mark01($AccountNo) {
    $Mark01 = $this->Method01($AccountNo, '173173173');
    return $Mark01;
  }  /* End of Mark01 */

  function Mark02($AccountNo) {
    $Mark02 = $this->Method02($AccountNo, '298765432', FALSE);
    return $Mark02;
  }  /* End of Mark02 */

  function Mark03($AccountNo) {
    $Mark03 = $this->Method01($AccountNo, '212121212');
    return $Mark03;
  }  /* End of Mark03 */

  function Mark04($AccountNo) {
    $Mark04 = $this->Method02($AccountNo, '432765432', FALSE);
    return $Mark04;
  }  /* End of Mark04 */

  function Mark05($AccountNo) {
    $Mark05 = $this->Method01($AccountNo, '137137137');
    return $Mark05;
  }  /* End of Mark05 */

  function Mark06($AccountNo) {
    $Mark06 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
    return $Mark06;
  }  /* End of Mark06 */

  function Mark07($AccountNo) {
    $Mark07 = $this->Method02($AccountNo, 'A98765432', TRUE);
    return $Mark07;
  }  /* End of Mark07 */

  function Mark08($AccountNo) {
    $Mark08 = 1;
    if ($AccountNo > 60000) {
      $Mark08 = $this->Method00($AccountNo, '212121212', 10);
    }
    return $Mark08;
  }  /* End of Mark08 */

  // Kein Pruefziffernverfahren vorhanden.
  // Kontonummer ist aktuell bei der Implementierung immer als RICHTIG zu beurteilen.
  function Mark09($AccountNo) {
    $Mark09 = 2;
    return $Mark09;
  }  /* End of Mark09 */

  function Mark10($AccountNo) {
    $Mark10 = $this->Method06($AccountNo, 'A98765432', TRUE, 10, 11);
    return $Mark10;
  }  /* End of Mark10 */

  function Mark11($AccountNo) {
    $Significance = 'A98765432';
    $Help = 0;
    $Mark11 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * HexDec(substr($Significance,$Run,1)));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Help == 1) {
      $Checksum = 9;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark11 = 0;
    }
    return $Mark11;
  }  /* End of Mark11 */

  function Mark12($AccountNo) {
    $Mark12 = $this->Method01($AccountNo, '731731731');
    return $Mark12;
  }  /* End of Mark12 */

  function Mark13($AccountNo) {
    $Help = $this->Method00($AccountNo, '0121212', 8);
    if ($Help == 1) {
      if (Substr($AccountNo,-2) <> '00') {
        $Help = $this->Method00(substr($this->ExpandAccount($AccountNo), 2) . '00', '0121212', 8);
      }
    }
    $Mark13 = $Help;
    return $Mark13;
  }  /* End of Mark13 */

  function Mark14($AccountNo) {
    $Mark14 = $this->Method02($AccountNo, '000765432', FALSE);
    return $Mark14;
  }  /* End of Mark14 */

  function Mark15($AccountNo) {
    $Mark15 = $this->Method06($AccountNo, '000005432', FALSE, 10, 11);
    return $Mark15;
  }  /* End of Mark15 */

  function Mark16($AccountNo) {
    $Mark16 = $this->Method16($AccountNo, '432765432', 10);
    return $Mark16;
  }  /* End of Mark16 */

  function Mark17($AccountNo) {
    $Significance = '0121212';
    $Help = 0;
    $Help2 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += ($this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1)));
    }
    $Help = $Help -1;
    $Checksum = $Help % 11;
    $Checksum = 10 - $Checksum;
    if ($Checksum == 10) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,7,1)) {
      $Help2 = 0;
    }
    $Mark17 = $Help2;
    return $Mark17;
  }  /* End of Mark17 */

  function Mark18($AccountNo) {
    $Mark18 = $this->Method01($AccountNo, '317931793');
    return $Mark18;
  }  /* End of Mark18 */

  function Mark19($AccountNo) {
    $Mark19 = $this->Method06($AccountNo, '198765432', FALSE, 10, 11);
    return $Mark19;
  }  /* End of Mark19 */

  function Mark20($AccountNo) {
    $Mark20 = $this->Method06($AccountNo, '398765432', FALSE, 10, 11);
    return $Mark20;
  }  /* End of Mark20 */

  // --- Fix FrankM 20080717 ---
  function Mark21($AccountNo) {
    // Initialisierung
    $Significance = '212121212';
    $Help = 0;
    $Mark21 = 1;
    // Kontonummer auf zehn Stellen auffuellen.
    $AccountNo = $this->ExpandAccount($AccountNo);
    // Quersumme aus Produkten bilden.
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += $this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    // Daraus erneut Quersumme bilden bis diese einstellig ist.
    while (strlen($Help) > 1){
      $Help = $this->CrossSum($Help);
    }
    // Quersumme von 10 subtrahieren = Pruefziffer
    $Checksum = 10 - $Help;
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark21 = 0;
    }
    return $Mark21;
  }  /* End of Mark21 */

  function Mark22($AccountNo) {
    $Significance = '313131313';
    $Help = 0;
    $Mark22 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Zwischenwert = (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
       $Help += $this->OnlyOne($Zwischenwert);
     }
     $Checksum = ceil($Help/10)*10 - $Help;
     if ($Checksum == substr($AccountNo,-1)) {
      $Mark22 = 0;
    }
    return $Mark22;
  }  /* End of Mark22 */

  function Mark23($AccountNo) {
    $Mark23 = $this->Method16($AccountNo, '765432', 7);
    return $Mark23;
  }  /* End of Mark23 */

  function Mark24($AccountNo) {
    $Significance = '123123123';
    $Help = 0;
    $Mark24 = 1;
    switch (substr($AccountNo,0,1)) {
      case 3 :
      case 4 :
      case 5 :
      case 6 :
        // deaktiviert, da die Postbank diese Definition nicht einhaelt.
        //$AccountNo = Substr($AccountNo,1);
        break;
      case 9 :
      //  $AccountNo = SubStr($AccountNo,3);
        break;
    }
    while (substr($AccountNo,0,1)==0){
      $AccountNo = substr($AccountNo,1);
    }

    for ($Run = 0;$Run < strlen($AccountNo)-1;$Run++ ) {
      $ZwischenHilf = substr($AccountNo,$Run,1) * substr($Significance,$Run,1) + substr($Significance,$Run,1);
      $Help += $ZwischenHilf % 11;
    }

    $Checksum = $this->OnlyOne($Help);

    if ($Checksum == substr($AccountNo,-1)) {
      $Mark24 = 0;
    }
    return $Mark24;
  }  /* End of Mark24 */

  function Mark25($AccountNo) {
    $Significance = '098765432';
    $Falsch = FALSE;
    $Help = 0;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Checksum == 11) {
      $Checksum = 0;
    }
    if ($Checksum == 10) {
      $Checksum = 0;
      if ((substr($AccountNo,1,1) <> '8') and (substr($AccountNo,1,1) <> '9')) {
        $Mark25 = 1;
        $Falsch = TRUE;
      }
    }
    if ($Falsch == FALSE) {
      if ($Checksum == substr($AccountNo,-1)) {
        $Mark25 = 0;
      } else {
        $Mark25 = 1;
      }
    }
    return $Mark25;
  }  /* End of Mark25 */

  function Mark26($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (Substr($AccountNo,-2) == '00') {
      $AccountNo = Substr($AccountNo,2) . '00';
    }
    $Mark26 = $this->Method06($AccountNo,'2345672',FALSE,8,11);
    return $Mark26;
  }  /* End of Mark26 */

  function Mark27($AccountNo) {
    if ((int)$AccountNo <= 999999999.) {
      $Mark27 = $this->Method00($AccountNo, '212121212', 10);
    } else {
      $Mark27 = $this->Mark29($AccountNo);
    }
    return $Mark27;
  }  /* End of Mark27 */

  function Mark28($AccountNo) {
    $Mark28 = $this->Method06($AccountNo, '8765432', FALSE, 8, 11);
    return $Mark28;
  }  /* End of Mark28 */

  /* --- Hotfix FrankM 20081208 --- */
  function Mark29($AccountNo) {
    $Transform = '143214321';
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Help = 0;
    for ($Run = 0;$Run < strlen($Transform);$Run++) {
      $ToAdd = 0;
      switch (substr($Transform,$Run,1)) {
        case '1' :
          switch (substr($AccountNo,$Run,1)) {
            Case '0' :
              $ToAdd = 0;
              break;
            Case '1' :
              $ToAdd = 1;
              break;
            Case '2' :
              $ToAdd = 5;
              break;
            Case '3' :
              $ToAdd = 9;
              break;
            Case '4' :
              $ToAdd = 3;
              break;
            Case '5' :
              $ToAdd = 7;
              break;
            Case '6' :
              $ToAdd = 4;
              break;
            Case '7' :
              $ToAdd = 8;
              break;
            Case '8' :
              $ToAdd = 2;
              break;
            Case '9' :
              $ToAdd = 6;
              break;
          }
          break;
        case '2' :
          switch (substr($AccountNo,$Run,1)) {
            Case '0' :
              $ToAdd = 0;
              break;
            Case '1' :
              $ToAdd = 1;
              break;
            Case '2' :
              $ToAdd = 7;
              break;
            Case '3' :
              $ToAdd = 6;
              break;
            Case '4' :
              $ToAdd = 9;
              break;
            Case '5' :
              $ToAdd = 8;
              break;
            Case '6' :
              $ToAdd = 3;
              break;
            Case '7' :
              $ToAdd = 2;
              break;
            Case '8' :
              $ToAdd = 5;
              break;
            Case '9' :
              $ToAdd = 4;
              break;
          }
          break;
        case '3' :
          switch (substr($AccountNo,$Run,1)) {
            Case '0' :
              $ToAdd = 0;
              break;
            Case '1' :
              $ToAdd = 1;
              break;
            Case '2' :
              $ToAdd = 8;
              break;
            Case '3' :
              $ToAdd = 4;
              break;
            Case '4' :
              $ToAdd = 6;
              break;
            Case '5' :
              $ToAdd = 2;
              break;
            Case '6' :
              $ToAdd = 9;
              break;
            Case '7' :
              $ToAdd = 5;
              break;
            Case '8' :
              $ToAdd = 7;
              break;
            Case '9' :
              $ToAdd = 3;
              break;
          }
          break;
        case '4' :
          switch (substr($AccountNo,$Run,1)) {
            Case '0' :
              $ToAdd = 0;
              break;
            Case '1' :
              $ToAdd = 1;
              break;
            Case '2' :
              $ToAdd = 2;
              break;
            Case '3' :
              $ToAdd = 3;
              break;
            Case '4' :
              $ToAdd = 4;
              break;
            Case '5' :
              $ToAdd = 5;
              break;
            Case '6' :
              $ToAdd = 6;
              break;
            Case '7' :
              $ToAdd = 7;
              break;
            Case '8' :
              $ToAdd = 8;
              break;
            Case '9' :
              $ToAdd = 9;
              break;
          }
          break;
      }
      $Help += $ToAdd;
    }
    $Help = $this->OnlyOne($Help);
    $Checksum = 10 - $Help;
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark29 = 0;
    } else {
      $Mark29 = 1;
    }
    return $Mark29;
  }

  function Mark30($AccountNo) {
    $Significance = '200001212';
    $Help = 0;
    $Mark30 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++){
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $this->OnlyOne($Help);
    $Checksum = 10 - $Help;
    if ($Checksum == 10) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark30 = 0;
    }
    return $Mark30;
  }  /* End of Mark30 */

  function Mark31($AccountNo) {
    $Significance = '123456789';
    $Help = 0;
    $Mark31 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++){
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Checksum = $Help % 11;
    if($Checksum == substr($AccountNo,-1)) {
      $Mark31 = 0;
    }
    return $Mark31;
  }  /* End of Mark31 */

  function Mark32($AccountNo) {
    $Mark32 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    return $Mark32;
  }  /* End of Mark32 */

  function Mark33($AccountNo) {
    $Mark33 = $this->Method06($AccountNo, '000065432', FALSE, 10, 11);
    return $Mark33;
  }  /* End of Mark33 */

  function Mark34($AccountNo) {
    $Mark34 = $this->Method06($AccountNo, '79A5842', TRUE, 8, 11);
    return $Mark34;
  }  /* End of Mark34 */

  function Mark35($AccountNo) {
    $Mark35 = 3;
    return $Mark35;
  }  /* End of Mark35 */

  function Mark36($AccountNo) {
    $Mark36 = $this->Method06($AccountNo, '000005842', FALSE, 10, 11);
    return $Mark36;
  }  /* End of Mark36 */

  function Mark37($AccountNo) {
    $Mark37 = $this->Method06($AccountNo, '0000A5842', TRUE, 10, 11);
    return $Mark37;
  }  /* End of Mark37 */

  function Mark38($AccountNo) {
    $Mark38 = $this->Method06($AccountNo, '0009A5842', TRUE, 10, 11);
    return $Mark38;
  }  /* End of Mark38 */

  function Mark39($AccountNo) {
    $Mark39 = $this->Method06($AccountNo, '0079A5842', TRUE, 10, 11);
    return $Mark39;
  }  /* End of Mark39 */

  function Mark40($AccountNo) {
    $Mark40 = $this->Method06($AccountNo, '6379A5842', TRUE, 10, 11);
    return $Mark40;
  }  /* End of Mark40 */

  function Mark41($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,3,1) == '9') {
      $AccountNo = '000'. substr($AccountNo,3);
    }
    $Mark41 = $this->Method00($AccountNo,'212121212',10);
    return $Mark41;
  }  /* End of Mark41 */

  function Mark42($AccountNo) {
    $Mark42 = $this->Method06($AccountNo, '098765432', FALSE, 10, 11);
    return $Mark43;
  }  /* End of Mark42 */

  function Mark43($AccountNo) {
    $Significance = '987654321';
    $Help = 0;
    $Mark43 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 10;
    $Checksum = 10 - $Help;
    if ($Checksum == 10) {
      $Checksum = 0;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark43 = 0;
    }
    return $Mark43;
  }  /* End of Mark43 */

  function Mark44($AccountNo) {
    $Mark44 = $this->Method06($AccountNo, '0000A5842', TRUE, 10, 11);
    return $Mark44;
  }  /* End of Mark44 */

  function Mark45($AccountNo) {
    if (substr($AccountNo,0,1)=='0' or substr($AccountNo,4,1)=='1'){
      $Mark45 = 2;
    } else {
      $Mark45 = $this->Method00($AccountNo, '212121212', 10);
    }
    return $Mark45;
  }  /* End of Mark45 */

  function Mark46($AccountNo) {
    $Mark46 = $this->Method06($AccountNo, '0065432', FALSE, 8, 11);
    return $Mark46;
  }  /* End of Mark46 */

  function Mark47($AccountNo) {
    $Mark47 = $this->Method06($AccountNo, '00065432', FALSE, 9, 11);
    return $Mark47;
  }  /* End of Mark47 */

  function Mark48($AccountNo) {
    $Mark48 = $this->Method06($AccountNo, '00765432', FALSE, 9, 11);
    return $Mark48;
  }  /* End of Mark48 */

  function Mark49($AccountNo) {
    $Mark49=$this->Mark00($AccountNo);
    if ($Mark49 == 0)
      return $Mark49;
    $Mark49=$this->Mark01($AccountNo);
    return $Mark49;
  }  /* End of Mark49 */

  function Mark50($AccountNo) {
    $Help = $this->Method06($AccountNo, '765432', FALSE, 7, 11);
    if ($Help == 1) {
      if (strlen($AccountNo) < 7) {
        $Help = $this->Method06($AccountNo . '000', '765432', FALSE, 7, 11);
      }
    }
    $Mark50 = $Help;
    return $Mark50;
  }  /* End of Mark50 */


  /* --- Hotfix FrankM 20081208 --- */
  function Mark51($AccountNo) {
      $AccountNo = $this->ExpandAccount($AccountNo);
      // Normale Berechnung, keine Sachkonten
      if (substr($AccountNo, 2, 1) != '9') {
        // Methode A: Modulus 11, Gewichtung 2, 3, 4, 5, 6, 7
        $Help = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
          if ($Help == 1) {
            // Methode B: Modulus 11, Gewichtung 2, 3, 4, 5, 6 (= Mark33)
            $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 11);
            if ($Help == 1) {
              // 10. Stelle 7, 8 oder 9 = ungueltig
              switch (substr($AccountNo, -1)) {
                case '7' :
                case '8' :
                case '9' :
                  $Help = 1;
                  break;
                default :
                  // Methode C: Modulus 7, Gewichtung 2, 3, 4, 5, 6
                  $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7);
                  break;
              }
           }
        }
      // Ausnahme fuer Sachkonten, 3. Stelle der Kontonummer = 9
      } else {
        // Variante 1 zur Ausnahme, Modulus 11, Gewichtung 2, 3, 4, 5, 6, 7, 8
        $Help = $this->Method06($AccountNo, '008765432', FALSE, 10, 11);
        if ($Help == 1 ){
          // Variante 2 zur Ausnahme, Modulus 11, Gewichtung 2, 3, 4, 5, 6, 7, 8, 9, 10
          $Help = $this->Method06($AccountNo, 'A98765432', TRUE, 10, 11);
        }
      }
      return $Help;
    }  /* End of Mark51 */

  function Mark52($AccountNo,$BLZ ) {
    $Significance = '4216379A5842';
    if ((strlen($AccountNo) == 10) && (substr($AccountNo,0,1) == '9')){
        $Correct = $this->Mark20($AccountNo);

    } else {
      $Help = 0;
      $Rest = 0;
      $AltKonto = substr($BLZ,-4).substr($AccountNo,0,2);

      $AccountNo = Substr($AccountNo,2);
      while (substr($AccountNo,0,1) == '0') {
        $AccountNo = Substr($AccountNo,1);
      }
      $AltKonto = $AltKonto . $AccountNo;

      $Checksum = substr($AltKonto,5,1);

      $AltKonto = substr($AltKonto,0,5).'0'.substr($AltKonto,6);

      $Laenge = strlen($AltKonto);

      $Significance=substr($Significance,(12 - $Laenge));
      for ($Run = 0; $Run < $Laenge;$Run++) {
        $Help += substr($AltKonto,$Run,1) * HexDec(substr($Significance,$Run,1));
      }
      $Rest = $Help % 11;
      $Gewicht = HexDec(substr($Significance,5,1));

      $PZ = -1;
      do {
        $PZ++;
        $Help2 = $Rest + ($PZ * $Gewicht);
        if ($PZ == 9) {
            break;
        }
      } while ($Help2 % 11 <>10);
      if ($Help2 % 11 == 10) {
         if ($PZ == $Checksum) {
           $Correct = 0;
         } else {
           $Correct = 1;
         }
       } else {
         $Correct = 1;
       }
     }

    return $Correct;
  }  /* End of Mark52 */

  function Mark53($AccountNo,$BLZ ) {
    $Significance = '4216379A5842';
    if (strlen($AccountNo) == 10) {
      if (substr($AccountNo,0,1) == '9') {
        $Correct = $this->Mark20($AccountNo);
      }
    } else {
      $Help = 0;
      $Rest = 0;

      $AltKonto = substr($BLZ,-4,2) . substr($AccountNo,1,1) . substr($BLZ,-1). substr($AccountNo,0,1) . substr($AccountNo,2,1);

      $AccountNo = Substr($AccountNo,3);

      while (substr($AccountNo,0,1) == '0') {
        $AccountNo = Substr($AccountNo,1);
      }

      $AltKonto = $AltKonto . $AccountNo;

      while (strlen($AltKonto) < 12){
        $AltKonto = "0" . $AltKonto;
      }

      $Checksum = substr($AltKonto, 5, 1);
      $AltKonto = substr($AltKonto, 0, 5) . '0' . substr($AltKonto, 6);
      $Laenge = strlen($AltKonto);

      for ($Run = 0; $Run < $Laenge;$Run++) {
        $Help += substr($AltKonto, $Run, 1) * HexDec(substr($Significance, $Run, 1));
      }

      $Rest = $Help % 11;

      $Gewicht = HexDec(substr($Significance,5, 1));
      $PZ = -1;
      do {
        $PZ++;
        $Help2 = $Rest + ($PZ * $Gewicht);
       } while ($Help2 % 11 <> 10 or $PZ > 9);

      if ($Help2 % 11 == 10) {
        if ($PZ == $Checksum) {
          $Correct = 0;
        } else {
          $Correct = 1;
        }
      } else {
        $Correct = 1;
      }
    }
    return $Correct;
  }  /* End of Mark53 */

  function Mark54($AccountNo) {
    $Mark54 = 3;
    return $Mark54;
  }  /* End of Mark54 */

  function Mark55($AccountNo) {
    $Mark55 = $this->Method06($AccountNo, '878765432', FALSE, 10, 11);
    return $Mark55;
  }  /* End of Mark55 */

  function Mark56($AccountNo) {
    $Significance = '432765432';
    $Mark56 = 1;
    $Help = 0;
    $Correct = 0;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Help = 11 - $Help;
    $Checksum = $Help;
    switch (substr($AccountNo,1,1)) {
      case '9' :
        if ($Help == 11) {
          $Checksum = 8;
        }
        if ($Help == 10) {
          $Checksum = 7;
        }
      default :
        if ($Help == 11) {
          $Correct = 1;
        }
        if ($Help == 10) {
          $Correct = 1;
        }
    }
    if ($Correct == 0) {
      if ($Checksum == substr($AccountNo,-1)) {
        $Mark56 = 0;
      }
    }
    return $Mark56;
  }  /* End of Mark56 */

  /* Alte Methode 57 - ungueltig.
  function Mark57($AccountNo) {
    $Correct = 1;

    $AccountNo = $this->ExpandAccount($AccountNo);

    $help = substr($AccountNo,0,2);

    switch (true){
      case ($help <= 50):
      case ($help == 91):
      case ($help >= 96 && $help <= 99):
        return 0;
        break;
      default:
    }

    if (preg_match("/[87]{6}/", $AccountNo)) {
      return 0;
    }

    $Mark57 = $this->Method00($AccountNo, '121212121', 10);
    return $Mark57;

  }
  */

  /* --- Neue Methode 57 --- */
  /* --- Changed FrankM 20061206, 20070822 --- */
  function Mark57($AccountNo) {

    // Auffuellen mit Nullen auf 10 Stellen.
    $AccountNo = $this->ExpandAccount($AccountNo);

    // Pruefstellen zum ermitteln der Varianten.
    $help01 = substr($AccountNo,0,2);

    // Genutzte Berechnungsvariante ermitteln.
    switch (true){
      case ($help01 == 51):
      case ($help01 == 55):
      case ($help01 == 61):
      case ($help01 >= 64 && $help01 <= 66):
      case ($help01 == 70):
      case ($help01 == 73):
      case ($help01 >= 75 && $help01 <= 82):
      case ($help01 == 88):
      case ($help01 >= 94 && $help01 <= 95):
        // Variante 1: Modulus 10, Gewichtung 1,2,1,2,1,2,1,2,1, Pruefziffer Stelle 10.
        $PResult = $this->Method00($AccountNo, '121212121', 10);
        // Ausnahme: Wenn die ersten 6 Stellen 777777 oder 888888 dann richtig.
        $help02 = substr($AccountNo,0,6);
        if ($help02 == 777777) {
            $PResult = 0;
          }
        if ($help02 == 888888) {
            $PResult = 0;
          }
        break;
      case ($help01 >= 32 && $help01 <= 39):
      case ($help01 >= 41 && $help01 <= 49):
      case ($help01 >= 52 && $help01 <= 54):
      case ($help01 >= 56 && $help01 <= 60):
      case ($help01 >= 62 && $help01 <= 63):
      case ($help01 >= 67 && $help01 <= 69):
      case ($help01 >= 71 && $help01 <= 72):
      case ($help01 == 74):
      case ($help01 >= 83 && $help01 <= 87):
      case ($help01 >= 89 && $help01 <= 90):
      case ($help01 >= 92 && $help01 <= 93):
      case ($help01 >= 96 && $help01 <= 98):
        // Variante 2: Modulus 10, Gewichtung 1,2,1,2,1,2,1,2,1, Pruefziffer Stelle 3,
        // Pruefziffer bei der Berechnung auslassen.
        $PResult = $this->Method00($AccountNo, '121212121', 3, 10, -1);
        break;
      case ($help01 == 40):
      case ($help01 == 50):
      case ($help01 == 91):
      case ($help01 == 99):
        // Variante 3: Methode 09 (Keine Berechnung).
        $PResult = $this->Mark09($AccountNo);;
        break;
      case ($help01 >= 01 && $help01 <= 31):
        // Variante 4: Dritte und vierte Stelle zwischen 01 und 12
        // -UND- siebte bis neunte Stelle kleiner 500.
        $help03 = substr($AccountNo,2,2);
        $help04 = substr($AccountNo,6,3);
        $PResult = 1;
        if ($help03 >= 01 && $help03 <= 12) {
          if ($help04 < 500) {
            $PResult = 0;
          }
        }
        // Ausnahme: Diese Kontonummer ist als richtig zu bewerten.
        if ($AccountNo == 0185125434) {
          $PResult = 0;
        }
        break;
      default:
        // Kontonummern die mit 00 beginnen sind falsch.
        $PResult = 1;
        break;
    }

    // Der Ordnung halber...
    $Mark57 = $PResult;
    return $Mark57;

  }  /* End of Mark57 */

  function Mark58($AccountNo) {
    $Mark58 = $this->Method02($AccountNo, '000065432', FALSE);
    return $Mark58;
  }  /* End of Mark58 */

  function Mark59($AccountNo) {
    $Mark59 = 1;
      if (strlen($AccountNo) > 8) {
        $Mark59 = $this->Method00($AccountNo, '212121212', 10);
      }
      return $Mark59;
    }  /* End of Mark59 */

  function Mark60($AccountNo) {
    $Mark60 = $this->Method00($AccountNo, '002121212', 10);
    return $Mark60;
  }  /* End of Mark60 */

  function Mark61($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,8,1) == '8') {
      $Mark61 = $this->Method00($AccountNo, '2121212012', 8);
    } else {
      $Mark61 = $this->Method00($AccountNo, '2121212', 8);
    }
    return $Mark61;
  }  /* End of Mark61 */

  function Mark62($AccountNo) {
    $Mark62 = $this->Method00($AccountNo,'0021212',8);
    return $Mark62;
  }  /* End of Mark62 */

  function Mark63($AccountNo) {
    $Help = $this->Method00($AccountNo,'0121212',8);
    if ($Help == 1) {
      $Help = $this->Method00($AccountNo,'000121212',10);
    }
    $Mark63 = $Help;
    return $Mark63;
  }  /* End of Mark63 */

  function Mark64($AccountNo) {
    $Mark64 = $this->Method06($AccountNo, '9A5842', TRUE, 7, 11);
    return $Mark64;
  }  /* End of Mark64 */

  function Mark65($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,8,1) == '9') {
      $Mark65 = $this->Method00($AccountNo, '2121212012', 8);
    } else {
      $Mark65 = $this->Method00($AccountNo, '2121212', 8);
    }
    return $Mark65;
  }  /* End of Mark65 */

  function Mark66($AccountNo) {
    $Significance = '000065432';
    $Help = 0;
    $Mark66 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Help == 1) {
      $Checksum = 1;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark66 = 0;
    }
    return $Mark66;
  }  /* End of Mark66 */

  function Mark67($AccountNo) {
    $Mark67 = $this->Method00($AccountNo, '2121212', 8);
    return $Mark67;
  }  /* End of Mark67 */

  function Mark68($AccountNo) {
    $Correct = 0;
    $Significance = '212121212';
    if (strlen($AccountNo) == 9) {
      if (substr($AccountNo,1,1) == '4') {
        $Correct = 4;
      }
    }
    if (strlen($AccountNo) == 10) {
      $Significance = '000121212';
    }
    if ($Correct == 0) {
      $Correct = $this->Method00($AccountNo,$Significance,10);
      if ($Correct == 1) {
        $Correct = $this->Method00($AccountNo,'210021212',10);
      }
    }
    $Mark68 = $Correct;
    return $Mark68;
  }  /* End of Mark68 */

  function Mark69($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Correct = 0;
    if (Substr($AccountNo,0,2) == '93') {
      $Correct = 2;
    }
    if ($Correct == 0) {
      $Correct = $this->Mark28($AccountNo);
      if ($Correct == 1) {
        $Correct = $this->Mark29($AccountNo);
      } elseif (Substr($AccountNo,0,2) == '97'){
         $Correct = $this->Mark29($AccountNo);
      }
    }
    $Mark69 = $Correct;
    return $Mark69;
  }  /* End of Mark69 */

  function Mark70($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,3,1) == '5'){
      $Mark70 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    } elseif (Substr($AccountNo,3,2) == '69') {
      $Mark70 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    } else {
      $Mark70 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
   }
    return $Mark70;
  }  /* End of Mark70 */

  function Mark71($AccountNo) {
    $Significance='0654321';
    $Help = 0;
    $Mark71 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    $Checksum = 11 - $Help;
    if ($Help == 0) {
      $Checksum = 0;
    }
    if ($Help == 1) {
      $Checksum = 1;
    }
    if ($Checksum == substr($AccountNo,-1)) {
      $Mark71 = 0;
    }
    return $Mark71;
  }  /* End of Mark71 */

  function Mark72($AccountNo) {
    $Mark72 = $this->Method00($AccountNo, '000121212', 10);
    return $Mark72;
  }   /* End of Mark72 */

/*
  function Mark73($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) == '9') {
      $Mark73 = $this->Method06($AccountNo, 'A98765432', TRUE, 10, 11);
    } else {
      $Mark73 = $this->Method00($AccountNo, '000121212', 10);
    }
    return $Mark73;
  }
 */


  function Mark73($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) != '9') {
      $Mark73 = $this->Method00($AccountNo, '000121212', 10); //Variante 1
      if ($Mark73 != 0) {
        $Mark73 = $this->Method00($AccountNo, '000021212', 10); //Variante 2
        if ($Mark73 != 0) {
          $Mark73 = $this->Method00($AccountNo, '000021212', 10, 7); //Variante 3
        }
      }
    } else {
//      $Mark73 = $this->Method06($AccountNo, 'A98765432', TRUE, 10, 11);
      $Mark73 = $this->Mark51($AccountNo);
    }
    return $Mark73;
  } /* End of Mark73 */


  // Letzte Ueberpruefung: 22.06.2007 FrankM
  function Mark74($AccountNo) {
    $Help = 0;
    $V2 = 0;
    // Wenn Kontonummer sechstellig, Variante 2 beachten!
    if (strlen($AccountNo) == 6) {
      $V2 = 1;
    }
    $Correct = $this->Method00($AccountNo, '212121212', 10);
    if ($Correct == 1) {
      // Wenn Variante 2...
      if ($V2 == 1) {
        $Significance = '212121212';
        $Correct = 1;
        $AccountNo = $this->ExpandAccount($AccountNo);
        for ($Run = 0; $Run < strlen($Significance); $Run++) {
          $Help += $this->CrossSum(substr($AccountNo, $Run,1) * substr($Significance, $Run,1));
        }
        $Help = $this->OnlyOne($Help);
        $Help = 5 - $Help;
        if ($Help < 0) {
          $Help = 10 + $Help;
        }
        //echo "HELP = " . $Help . "<br>";
        //echo "PRUE = " . substr($AccountNo,-1) . "<br>";
        $Checksum = $Help;
        // Wenn Checksumme = Pruefziffer, dann richtig
        if ($Checksum == substr($AccountNo,-1)) {
          $Correct = 0;
        }
      } // Ende Variante 2
    }
    //echo "ERG = " . $Correct . "<br>";
    return $Correct;
  }  /* End of Mark74 */


  /* --- Fixed FrankM 20070822 --- */
  function Mark75($AccountNo) {
    $Help = 1;
    switch (strlen($AccountNo)) {
      case 6 :
      case 7 :
        $Help = $this->Method00($AccountNo,'000021212',10);
        break;
      case 9 :
        if (substr($AccountNo,0,1) == '9') {
          $Help = $this->Method00($AccountNo,'0021212',8);
        } else {
          $Help = $this->Method00($AccountNo,'021212',7);
        }
        break;
      case 10 :
        $Help = $this->Method00($AccountNo,'021212',7);
        break;
    }
    return $Help;
  }  /* End of Mark75 */

  function Mark76($AccountNo) {
    $Help = 0;
    $Correct = 1;
    $Significance = '0765432';
    $AccountNo = $this->ExpandAccount($AccountNo);
    for ($Run = 0;$Run < strlen($Significance);$Run++) {
      $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
    }
    $Help = $Help % 11;
    if ($Help == 10) {
      $Correct = 4;
    } else {
      if ($Help == substr($AccountNo,-3,1)) {
        $Correct = 0;
      } else {
        $Help=0;
        $Significance = '000765432';
        for ($Run = 0;$Run < strlen($Significance);$Run++) {
          $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
        }
        $Help = $Help % 11;
        if ($Help == 10) {
          $Correct = 4;
        } else {
          if ($Help == substr($AccountNo,-1)) {
            $Correct = 0;
          } else {
            $Correct = 1;
          }
        }
      }
    }
    return $Correct;
  }  /* End of Mark76 */

  function Mark77($AccountNo) {
    $Help = 0;
    $Mark77 = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Significance = '54321';
    for ($Run = 4;$Run == 9;$Run++) {
      $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
    }
    $Help = $Help % 11;
    if ($Help <> 0) {
      $Help = 0;
      $Significance = '54345';
      for ($Run = 4;$Run < 10;$Run++) {
        $Help += (substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
      }
      $Help = $Help % 11;
      if ($Help = 0) {
        $Mark77 = 0;
      }
    }
    return $Mark77;
  }  /* End of Mark77 */

  function Mark78($AccountNo) {
    if (strlen($AccountNo) == 8) {
      $Mark78 = 4;
    } else {
      $Mark78 = $this->Method00($AccountNo, '212121212', 10);
    }
    return $Mark78;
  }  /* End of Mark78 */

  function Mark79($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    switch (substr($AccountNo,0,1)) {
      case '0' :
      case '7' :
      case '8' :
        $Mark79 = 1;
        break;
      case '1' :
      case '2' :
      case '9' :
        $Mark79 = $this->Method00($AccountNo, '12121212', 9);
        break;
      case '3' :
      case '4' :
      case '5' :
      case '6' :
        $Mark79 = $this->Method00($AccountNo, '212121212', 10);
      default :
        $Mark79 = 1;
    }
    return $Mark79;
  }  /* End of Mark79 */

  function Mark80($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);

    if (substr($AccountNo,2,1) == '9') {
      $Mark80 = $this->Mark51($AccountNo);
    } else {
      $Mark80 = $this->Method00($AccountNo, '000021212', 10);
    }
    if ($Mark80 != 0){
      $Significance='000021212';
      $Help = 0;
       $Mark80 = 1;

       for ($Run = 0; $Run < strlen($Significance); $Run++) {
         $Help += $this->CrossSum(substr($AccountNo,$Run,1) * substr($Significance,$Run,1));
       }
      $Help = $Help % 7;
       $Checksum = 7 - $Help;

       if ($Checksum == 10) {
         $Checksum = 0;
       }
       if ($Checksum == substr($AccountNo,9,1)) {
         $Mark80 = 0;
      }
    }
    return $Mark80;
  }  /* End of Mark80 */

  function Mark81($AccountNo) {
        $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) == '9') {
      $Mark81 = $this->Mark10($AccountNo);
    } else {
      $Mark81 = $this->Mark51($AccountNo);
    }
    return $Mark81;
  }  /* End of Mark81 */

  function Mark82($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
     if (substr($AccountNo,2, 2) == '99') {
       $Mark82 = $this->Mark10($AccountNo);
     } else {
       $Mark82 = $this->Mark33($AccountNo);
     }
     return $Mark82;
   }  /* End of Mark82 */

  // --- Fix FrankM 20081208 ---
  function Mark83($AccountNo) {
    // Methode A
    $Help = $this->Mark32($AccountNo);
    if ($Help == 1) {
      // Methode B
      $Help = $this->Mark33($AccountNo);
      if ($Help == 1) {
        // 10. Stelle 7, 8 oder 9 = ungueltig
        switch (substr($AccountNo, -1)) {
          case '7' :
          case '8' :
          case '9' :
            $Help = 1;
            break;
          default :
            // Methode C: Modulus 7, Gewichtung 2, 3, 4, 5, 6
            $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7);
            break;
        }
      }
    }
    $Mark83 = $Help;
    return $Mark83;
  }  /* End of Mark83 */

  function Mark84($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo, 2, 1) != '9') {
      $Help = $this->Mark33($AccountNo);
      if ($Help == 1) {
        $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7);
      }
    } else {
        $Help = $this->Mark51($AccountNo);
    }
    return $Help;
  }  /* End of Mark84 */

  // --- Fix FrankM 20080717 ---
  function Mark85($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    // Zuerst Typ A = Modifizierte Methode 6.
    $Help = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    if ($Help == 0) {
      return $Help;
    // Wenn falsch, dann Typ B = Methode 33 (Modifizierte Methode 6).
    } else {
      $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 11);
    }
    if ($Help == 0) {
      return $Help;
    // Wenn falsch, dann Typ C.
    } else {
      // Wenn zehnte Stelle = 7, 8, oder 9 dann Kontonummer ungueltig.
      if ($AccountNo[9] == '7' or $AccountNo[9] == '8' or $AccountNo[9] == '9') {
        return $Help;
      } else {
        // Methode 33 (Modifizierte Methode 6 mit Divisor 7).
        if (substr($AccountNo, 2, 2) != '99') {
          $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7);
        // Wenn 3. und 4. Stelle = 99, dann Modifizierte Methode 2.
        } else {
          $Help = $this->Method02($AccountNo, '008765432', FALSE);
        }
      }
    }
    return $Help;
  }  /* End of Mark85 */

  function Mark86($AccountNo) {
    $Help = $this->Method00($AccountNo, '000121212', 10);
    if ($Help == 1) {
      if (substr($AccountNo,2,1) == '9') {
        $Help = $this->Method06($AccountNo, 'A98765432', TRUE, 10, 11);
      } else {
        $Help = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
      }
    }
    $Mark86 = $Help;
    return $Mark86;
  }  /* End of Mark86 */

  function Mark87($AccountNo) {
    $Tab1[0] = 0;
    $Tab1[1] = 4;
    $Tab1[2] = 3;
    $Tab1[3] = 2;
    $Tab1[4] = 6;

    $Tab2[0] = 7;
    $Tab2[1] = 1;
    $Tab2[2] = 5;
    $Tab2[3] = 9;
    $Tab2[4] = 8;

    $Result = 1;
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) == '9') {
      $Result = $this->Mark10($AccountNo);
    } else {
      for ($Run = 0; $Run < strlen($AccountNo); $Run++) {
        // $AccountNoTemp[$Run + 1] = (int) substr($AccountNo,$Run,1);
        $AccountNoTemp[$Run] = (int) substr($AccountNo,$Run,1);
      }

      $i = 4;
      while ($AccountNoTemp[$i] == 0) {
        $i++;
      }

      $C2 = $i % 2;
      $D2 = 0;
      $A5 = 0;
      while ($i < 10) {
        switch ($AccountNoTemp[$i]) {
          case 0 :
            $AccountNoTemp[$i] = 5;
            break;
          case 1 :
            $AccountNoTemp[$i] = 6;
            break;
          case 5:
            $AccountNoTemp[$i] = 10;
            break;
          case 6:
            $AccountNoTemp[$i] = 1;
            break;
        }
        if ($C2 == $D2) {
          if ($AccountNoTemp[$i] > 5) {
            if(($C2 == 0) AND ($D2 == 0)) {
              $C2 = 1;
              $D2 = 1;
              $A5 = $A5 + 6 - ($AccountNoTemp[$i] - 6);
            } else {
              $C2 = 0;
              $D2 = 0;
              $A5 = $A5 + $AccountNoTemp[$i];
            } //end if(($C2 == 0) AND ($D2 == 0))
          } else {
            if (($C2 == 0) AND ($D2 == 0)) {
              $C2 = 1;
              $A5 = $A5 + $AccountNoTemp[$i];
            } else {
              $C2 = 0;
              $A5 = $A5 + $AccountNoTemp[$i];
            }
          }
        } else {
          if ($AccountNoTemp[$i] > 5) {
            if ($C2 == 0) {
              $C2 = 1;
              $D2 = 0;
              $A5 = $A5 - 6 + ($AccountNoTemp[$i] - 6);
            } else {
              $C2 = 0;
              $D2 = 1;
              $A5 = $A5 - $AccountNoTemp[$i];
            }
          } else {
            if ($C2 == 0) {
              $C2 = 1;
              $A5 = $A5 - $AccountNoTemp[$i];
            } else {
              $C2 = 0;
              $A5 = $A5 - $AccountNoTemp[$i];
            }
          }
        }
        $i++;
      }
      while (($A5 < 0) OR ($A5 > 4)) {
        if ($A5 > 4) {
          $A5 = $A5 - 5;
        } else {
          $A5 = $A5 + 5;
        }
      }
      if ($D2 == 0) {
        $P = $TAB1[$A5];
      } else {
        $P = $TAB2[$A5];
      }
      if ($P == $AccountNoTemp[10]) {
        $Result = 0;
      } else {
        if ($AccountNoTemp[4] == 0) {
          if ($P > 4) {
            $P = $P - 5;
          } else {
            $P = $P + 5;
          }
          if ($P == $AccountNoTemp[10] ) {
            $Result = 0;
          }
        }
      }
      if ($Result <> 0 ) {
        $Result = $this->Mark33($AccountNo);
        if ($Result <> 0 ) {
          $Result = $this->Method06($AccountNo,'000065432',FALSE,10,7);
        }
      }
    }
    return $Result;
  }  /* End of Mark87 */

  function Mark88($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,2,1) == '9') {
      $Mark88 = $this->Method06($AccountNo, '008765432', FALSE, 10, 11);
    } else {
      $Mark88 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    }
    return $Mark88;
  }  /* End of Mark88 */

  function Mark89($AccountNo) {
    $Correct = 1;
    switch (strlen($AccountNo)) {
      case 1 :
      case 2 :
      case 3 :
      case 4 :
      case 5 :
      case 6 :
      case 10 :
        $Correct = 4;
        break;
      case 7 :
      case 9 :
        $AccountNo = $this->ExpandAccount($AccountNo);
        $Correct = $this->Method06($AccountNo,'098765432',FALSE,10,11);
        break;
      default :
        if ((((int)$AccountNo > 32000005) and ((int)$AccountNo < 38999995)) or (((int)$AccountNo >1999999999) AND ((int)$AccountNo <400000000))) {
          $Correct = $this->Mark10($AccountNo);
        }
    }
    return $Correct;
  }  /* End of Mark89 */

  function Mark90($AccountNo) {
    $Help = $this->Method06($AccountNo, '000765432', FALSE, 10, 11); // Methode A
      if ($Help != 0) {
        $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 11); // Methode B
        if ($Help != 0) {
          switch (substr($AccountNo,-1)) {
            case '7' :
            case '8' :
            case '9' :
              $Help = 4;
              break;
            default :
              $Help = $this->Method06($AccountNo, '000065432', FALSE, 10, 7); //Methode C
              break;
          }
        }
        if ($Help != 0) {
          $Help = $this->Method06($AccountNo, '000065432',FALSE, 10, 9);  //Methode D
        }
        if ($Help != 0) {
              $Help = $this->Method06($AccountNo, '000021212',FALSE, 10, 10); //Methode E
        }
      }
    return $Help;
  }  /* End of Mark90 */

  function Mark91($AccountNo) {
    $Help = $this->Method06($AccountNo, '765432', FALSE, 7, 11);
    if ($Help == 1) {
      $Help = $this->Method06($AccountNo, '234567', FALSE, 7, 11);
      if ($Help == 1) {
        $Help = $this->Method06($AccountNo, 'A987650432', TRUE, 7, 11);
        if ($Help == 1) {
          $Help = $this->Method06($AccountNo, '9A5842', TRUE, 7, 11);
        }
      }
    }
    return $Help;;
  }  /* End of Mark91 */

  function Mark92($AccountNo) {
    $Mark92 = $this->Method01($AccountNo, '000173173');
    return $Mark92;
  }  /* End of Mark92 */

  function Mark93($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Correct = 1;
      if (substr($AccountNo,0,4) == '0000') {
        $Correct = $this->Method06($AccountNo,'000065432',FALSE,10,11);
      } else {
        $Correct = $this->Method06($AccountNo,'65432',FALSE,10,11);
    }
      if ($Correct == 1) {
        if (substr($AccountNo,0,4) == '0000') {
          $Correct = $this->Method06($AccountNo,'000065432',FALSE,10,7);
        } else {
          $Correct = $this->Method06($AccountNo,'65432',FALSE,10,7);
      }
    }
    $Mark93 = $Correct;
    return $Mark93;
  }  /* End of Mark93 */

  function Mark94($AccountNo) {
    $Mark94 = $this->Method00($AccountNo, '121212121', 10);
    return $Mark94;
  }  /* End of Mark94 */

  function Mark95($AccountNo) {
    if (strlen($AccountNo) == 10) {
      if ((substr($AccountNo,3,1) == '5') Or (substr($AccountNo, 3, 2) == '69')) {
        $Mark95 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
      } else {
        $Mark95 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
      }
    } else {
      $Mark95 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
      if ((int) $AccountNo < 2000000) {
        $Mark95 = 4;
      }
      if ((int) $AccountNo > 699999999) {
        if ((int) $AccountNo < 800000000) {
          $Mark95 = 4;
        }
      }
      if ((int) $AccountNo > 395999999) {
        if ((int) $AccountNo < 500000000) {
          $Mark95 = 4;
        }
      }
    }
    return $Mark95;
  }  /* End of Mark95 */

  function Mark96($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Help = $this->Mark19($AccountNo);
      if ($Help == 1) {
        $Help = $this->Method00($AccountNo, '212121212', 10);
        if ($Help == 1) {
          if ((int) $AccountNo >1299999) {
            if ((int) $AccountNo < 99400000) {
              $Help = 0;
            }
          }
        }
      }
     return $Help;
    }  /* End of Mark96 */

  function Mark97($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Help = (int) substr($AccountNo, 0, 9) % 11;
      if ($Help == 10) {
        $Help = 0;
      }
      if (substr($AccountNo,-1) == $Help) {
        $Mark97 = 0;
      } else {
        $Mark97 = 1;
      }
      return $Mark97;
    }  /* End of Mark97 */

  function Mark98($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $Correct = $this->Method01($AccountNo,'003713713');
    if ($Correct == 1) {
      $Correct = $this->Mark32($AccountNo);
    }
    $Mark98 = $Correct;
    return $Mark98;
  }  /* End of Mark98 */

  function Mark99($AccountNo) {
    $Mark99 = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
    if ((int) $AccountNo >= 396000000 && (int) $AccountNo <= 499999999) {
      $Mark99 = 4;
     }
    return $Mark99;
  }  /* End of Mark99 */

  function MarkA1($AccountNo) {
    if (strlen($AccountNo) == 8 OR strlen($AccountNo)==10){
      $AccountNo = $this->ExpandAccount($AccountNo);
      $MarkA1 = $this->Method00($AccountNo, '002121212', 10);
    } else {
      $MarkA1 = 1;
    }
    return $MarkA1;
  }  /* End of MarkA1 */

  function MarkA2($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $MarkA2 = $this->Method00($AccountNo, '212121212', 10);
    if ($MarkA2 != 0){
      $MarkA2 = $this->Mark04($AccountNo);
    }
    return $MarkA2;
  }  /* End of MarkA2 */

  function MarkA3($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Method00($AccountNo, '212121212', 10);
    if ($RetVal != 0){
      $RetVal = $this->Mark10($AccountNo);
    }
    return $RetVal;
  }  /* End of MarkA3 */

  function MarkA4($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if ((int) substr($AccountNo, 2,2) != 99){
      /* Variante 1 */
      $MarkA4 = $this->Method06($AccountNo,'000765432',FALSE, 10,11);
      if ($MarkA4 !=0){
        /* Variante 2 */
        $Significance='000765432';
        $MarkA4 = 1;
        $Help = 0;
        for ($Run = 0; $Run < strlen($Significance); $Run++) {
          $Help += substr($AccountNo,$Run,1) * substr($Significance,$Run,1);
        }
        $Help = $Help % 7;
        $Checksum = 7 - $Help;

        if ($Help == 0) {
          $Checksum = 0;
        }
        if ($Checksum == substr($AccountNo,-1)) {
         $MarkA4 = 0;
        }
      }
      if ($MarkA4 != 0){
        /* Variante 4 */
        $MarkA4 = $this->Mark93($AccountNo);
      }
    } else {
      /* Variante 3 */
      $MarkA4 = $this->Method06($AccountNo,'000065432',FALSE,10,11);
      if ($MarkA4 != 0){
        /* Variante 4 */
       $MarkA4 = $this->Mark93($AccountNo);
      }
    }
    return $MarkA4;
  }  /* End of MarkA4 */

  function MarkA5($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $MarkA5 = $this->Method00($AccountNo, '212121212', 10);
    if ($MarkA5 != 0){
      if (substr($AccountNo,1,1) != "9"){
        $MarkA5 = $this->Mark10($AccountNo);
      } else {
        $MarkA5 = 1;
      }
    }
    return $MarkA5;
  }  /* End of MarkA5 */

  function MarkA6($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,1,1) != "8"){
      $RetVal = $this->Method01($AccountNo, '173173173');
    } else {
      $RetVal = $this->Method00($AccountNo, '212121212', 10);
    }
    return $RetVal;
  }  /* End of MarkA6 */

  function MarkA7($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Method00($AccountNo, '212121212', 10);
    if ($RetVal != 0){
      $RetVal = $this->Mark03($AccountNo);
    }
    return $RetVal;
  }  /* End of MarkA7 */

  function MarkA8($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Mark81($AccountNo);
    if ($RetVal != 0){
      if (substr($AccountNo,2,1) != "9"){
        $RetVal = $this->Mark73($AccountNo);
      } else {
        $RetVal = 1;
      }
    }
    return $RetVal;
  }  /* End of MarkA8 */

  /* --- Fixed FrankM 20050408 --- */
  function MarkA9($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Method01($AccountNo, '173173173');
    if ($RetVal != 0){
      $RetVal = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
    }
    return $RetVal;
  }  /* End of MarkA9 */


  /* --- Added FrankM 20050408 --- */
  /* --- Wird aktuell von keiner Bank benutzt (09/2007 - 12/2007) ---*/
  function MarkB0($AccountNo) {
    if (strlen($AccountNo) != 10 OR substr($AccountNo,0,1) == "8"){
      $RetVal = 1;
    } else {
      if (substr($AccountNo,9,1) == "1" OR substr($AccountNo,9,1) == "2" OR substr($AccountNo,9,1) == "3" OR substr($AccountNo,9,1) == "6"){
        $RetVal = 0;
      } else {
        $RetVal = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
      }
    }
    return $RetVal;
  }  /* End of MarkB0 */

  /* --- Added FrankM 20050413 --- */
  function MarkB1($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Method01($AccountNo, '137137137');
    if ($RetVal != 0){
      $RetVal = $this->Method01($AccountNo, '173173173');
    }
    return $RetVal;
  }  /* End of MarkB1 */

  /* --- Added FrankM 20050415 --- */
  function MarkB2($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,0,1) <= "7"){
      $RetVal = $this->Method02($AccountNo, '298765432', FALSE);
    } else {
      $RetVal = $this->Method00($AccountNo, '212121212', 10);
    }
    return $RetVal;
  }  /* End of MarkB2 */

  /* --- Added FrankM 20050415 --- */
  function MarkB3($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,0,1) <= "8"){
      $RetVal = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
    } else {
      $RetVal = $this->Method06($AccountNo, '432765432', FALSE, 10, 11);
    }
    return $RetVal;
  }  /* End of MarkB3 */

  /* --- Added FrankM 20050415 --- */
  function MarkB4($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,0,1) == "9"){
      $RetVal = $this->Method00($AccountNo, '212121212', 10);
    } else {
      $RetVal = $this->Method02($AccountNo, '298765432', FALSE);
    }
    return $RetVal;
  }  /* End of MarkB4 */

  /* --- Added FrankM 20050727 --- */
  function MarkB5($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Method01($AccountNo, '137137137');
    if ($RetVal != 0){
      if ((substr($AccountNo,0,1) == '8') Or (substr($AccountNo,0,1) == '9')) {
        return $RetVal;
      }
      $RetVal = $this->Method00($AccountNo, '212121212', 10);
    }
    return $RetVal;
  }  /* End of MarkB5 */

  /* --- Added FrankM 20060112 ---
     --- Hotfix 20070717 ---
     --- Hotfix FrankM 20080110 --- */
  function MarkB6($AccountNo, $BLZ) {
    // Wenn Laenge = 9 dann Kontonummer fuer Methode 53 merken, Hotfix 20080110.
    if (strlen($AccountNo) == 9) {
       $AccountNoShort = $AccountNo;
    }
    // Mit fuehrenden Nullen auf 10 erweitern.
    $AccountNo = $this->ExpandAccount($AccountNo);
    // Kontonummern, die an der 1. Stelle den Wert 1-9 beinhalten, nach Methode 20 pruefen.
    if (intval(substr($AccountNo,0 ,1)) <= 9 && intval(substr($AccountNo,0 ,1)) > 0 ){
      $RetVal = $this->Mark20($AccountNo);
    } else {
      // Fuer Methode 53 muss die Laenge der Kontonummer = 9 sein
      if (strlen($AccountNoShort) == 9) {
        $RetVal = $this->Mark53($AccountNoShort, $BLZ);
      } else {
        $RetVal = 1;
      }
    }
    return $RetVal;
  }  /* End of MarkB6 */

  /* --- Added FrankM 20060112 --- */
  function MarkB7($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $AccountFloat = doubleval($AccountNo);
    if (($AccountFloat >= 1000000) And ($AccountFloat <= 5999999)) {
      $RetVal = $this->Method01($AccountNo, '173173173');
    } elseif (($AccountFloat >= 700000000) And ($AccountFloat <= 899999999)) {
      $RetVal = $this->Method01($AccountNo, '173173173');
    } else {
      $RetVal = 2;
    }
    return $RetVal;
  }  /* End of MarkB7 */

  /* --- Added FrankM 20060112 --- */
  function MarkB8($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = $this->Mark20($AccountNo);
    if ($RetVal != 0){
      $RetVal = $this->Mark20($AccountNo);
    }
    return $RetVal;
  }  /* End of MarkB8 */

  /* --- Added FrankM 20060124 --- */
  /* --- Benutzt von Hanseatic Bank, Hamburg --- */
  function MarkB9($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $RetVal = 1;

    // Variante 1 - Zwei f�hrende Nullen
    if ((substr($AccountNo,0,2) == "00")And (substr($AccountNo,2,1) != "0")){
      $Significance = '1231231';
      for ($Run = 0;$Run < strlen($Significance);$Run++) {
        $Step1 = (substr($AccountNo,$Run + 2,1) * substr($Significance,$Run,1));
        $Step2 = $Step1 + substr($Significance,$Run,1);
        $Step3 += $Step2 % 11;
      }
      $Checksum = $Step3 % 10;
      if ($Checksum == substr($AccountNo,-1)) {
        $RetVal = 0;
      } else {
        $Checksum = $Checksum + 5;
        if ($Checksum > 10) {
          $Checksum = $Checksum - 10;
        }
        if ($Checksum == substr($AccountNo,-1)) {
          $RetVal = 0;
        }
      }

    // Variante 2 - Drei f�hrende Nullen
    } elseif ((substr($AccountNo,0,3) == "000")And (substr($AccountNo,3,1) != "0")){
      $Significance = '654321';
      for ($Run = 0;$Run < strlen($Significance);$Run++) {
        $Step1 += (substr($AccountNo,$Run + 3,1) * substr($Significance,$Run,1));
      }
      $Checksum = $Step1 % 11;
      if ($Checksum == substr($AccountNo,-1)) {
        $RetVal = 0;
      } else {
        $Checksum = $Checksum + 5;
        if ($Checksum > 10) {
          $Checksum = $Checksum - 10;
        }
        if ($Checksum == substr($AccountNo,-1)) {
          $RetVal = 0;
        }
      }
    }
    return $RetVal;
  }  /* End of MarkB9 */

  /* --- Added FrankM 20060112 ---
     --- Fix FrankM 20061103 ---
     --- Hotfix FrankM 20080110 --- */
  function MarkC0($AccountNo, $BLZ) {
    // Wenn Laenge = 8 dann Kontonummer fuer Methode 52 merken, Hotfix 20080110.
    if (strlen($AccountNo) == 8) {
       $AccountNoShort = $AccountNo;
    }
    // Mit fuehrenden Nullen auf 10 erweitern.
    $AccountNo = $this->ExpandAccount($AccountNo);
    // Pruefen nach Methode 52 (achtstellig)
    if ((substr($AccountNo,0,2) == "00") And (substr($AccountNo,0,3)!= "000")) {
      // Fuer Methode 52 muss die Laenge der Kontonummer = 8 sein.
      if (strlen($AccountNoShort) == 8) {
        $RetVal = $this->Mark52($AccountNoShort, $BLZ);
      } else {
        $RetVal = 1;
      }
      // Wenn falsch, dann Methode 20
      if ($RetVal != 0){
        $RetVal = $this->Mark20($AccountNo);
      }
    // Alles andere nach Methode 20
    } else {
      $RetVal = $this->Mark20($AccountNo);
    }
    return $RetVal;
  }  /* End of MarkC0 */

  /* --- Added 20060703 --- */
  function MarkC1($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $markC1 = 1;
    if($AccountNo{0} != '5') { // Variante 1
      // Methode 17, Modulus 11, Gewichtung 1, 2, 1, 2, 1, 2
      $markC1 = $this->Mark17($AccountNo);
    } else { // Variante 2
      $weights = '121212121';
      $sum = 0;
      for($i = 0; $i < 9; $i++) {
        $sum += $this->CrossSum($AccountNo{$i} * $weights{$i});
      }
      $sum--;
      $prz = $sum % 11;
      if(0 < $prz) {
        $prz = 10 - $prz;
      }
      if($prz == $AccountNo{9}) { // 10. Stelle ist PRZ
        $markC1 = 0;
      }
    }
    return $markC1;
  }

  /* --- Added 20060703 --- */
  function MarkC2($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $markC2 = $this->Mark22($AccountNo);
    if($markC2 != 0) {
      $markC2 = $this->Mark00($AccountNo);
    }
  return $markC2;
  }

  /* --- Added FrankM 20070305 --- */
  function MarkC3($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,0,1) != "9") {
      $markC3 = $this->Mark00($AccountNo);
    } else {
      $markC3 = $this->Mark58($AccountNo);
    }
    return $markC3;
  }

  /* --- Added FrankM 20070305 --- */
  function MarkC4($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if (substr($AccountNo,0,1) != "9") {
      $markC4 = $this->Mark15($AccountNo);
    } else {
      $markC4 = $this->Mark58($AccountNo);
    }
    return $markC4;
  }

  /* --- Added FrankM 20070822 --- */
  function MarkC5($AccountNo) {
    $aAccountNo = $this->ExpandAccountExtended($AccountNo);
    $AccountNo = $aAccountNo['AccountNoLong'];

    // Berechnungsvariante nach Kontonummernlaenge.
    switch ($aAccountNo['AccountNoShortLen']) {
      case 6:
        // Variante 1, sechsstellige Kontonummer
        if (intval(substr($AccountNo,4 ,1)) <= 8 && intval(substr($AccountNo,4 ,1)) >= 1 ){
          $markC5 = $this->Mark75($aAccountNo['AccountNoShort']);
        } else {
          $markC5 = 1;
        }
        break;
      case 8:
        // Variante 4, achtstellige Kontonummer.
        if ((substr($AccountNo,2,1) == "3") or (substr($AccountNo,2,1) == "4") or (substr($AccountNo,2,1) == "5")) {
          $markC5 = $this->Mark09($AccountNo);
        } else {
          $markC5 = 1;
        }
        break;
      case 9:
        // Variante 1, neunstellige Kontonummer
        if (intval(substr($AccountNo,1 ,1)) <= 8 && intval(substr($AccountNo,1 ,1)) >= 1 ){
          $markC5 = $this->Mark75($aAccountNo['AccountNoShort']);
        } else {
          $markC5 = 1;
        }
        break;
      case 10:
        // Variante 4, zehnstellige Kontonummer.
        if ((substr($AccountNo,0,2) == "70") or (substr($AccountNo,0,2) == "85")) {
          $markC5 = $this->Mark09($AccountNo);
          return $markC5;
        // Variante 2, zehnstellige Kontonummer.
        } elseif ((substr($AccountNo,0,1) == "1") or (substr($AccountNo,0,1) == "4") or (substr($AccountNo,0,1) == "5") or (substr($AccountNo,0,1) == "6") or (substr($AccountNo,0,1) == "9")) {
          $markC5 = $this->Mark29($AccountNo);
        // Variante 3, zehnstellige Kontonummer.
        } elseif ((substr($AccountNo,0,1) == "3")) {
          $markC5 = $this->Mark00($AccountNo);
        } else {
          $markC5 = 1;
        }
        break;
      default:
        $markC5 = 1;
    } // End switch.
    return $markC5;
  }

  /* --- Added FrankM 20070822 ---
     --- Changed FrankM 20090206, 20100602 --- */
  function MarkC6($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $FirstLeftDigit = substr($AccountNo, 0, 1);
    // Erste Ziffer Ziffer von links auswerten.
    // 4, 5, 6, 8 an erster Stelle von links = falsch.
    if ((substr($AccountNo, 0, 1) == "4") or (substr($AccountNo, 0, 1) == "5") or (substr($AccountNo, 0, 1) == "6") or (substr($AccountNo, 0, 1) == "8")) {
      $markC6 = 1;
    // 0, 1, 2, 3, 7, 9 an erster Stelle von links = mit Methode 00 pruefen.
    } else {
      // Je nach vorhandener erster Stelle von links die Konstante zuordnen.
      switch ($FirstLeftDigit) {
        case 0:
          $Help = '4451970';
          break;
        case 1:
          $Help = '4451981';
          break;
        case 2:
          $Help = '4451992';
          break;
        case 3:
          $Help = '4451993';
          break;
        case 7:
          $Help = '5499570';
         break;
        case 9:
          $Help = '5499579';
         break;
      }  /* end switch */
      // Fuer Berechnung der Pruefziffer die Konstante
      // zur Kontonummer hinzu fuegen.
      $Help .= substr($AccountNo, 1);

      // Methode 00, 16. Stelle Pruefziffer, Modulator 10,
      // Pruefziffer NICHT verschieben, ExpandAccount NICHT anwenden.
      $markC6 = $this->Method00($Help, '212121212121212', 16, 10, 0, 1);
    }
    return $markC6;
  }

  /* --- Added FrankM 20071009 --- */
  function MarkC7($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    // Zuerst mit Methode 63 pruefen.
    $markC7 = $this->Mark63($AccountNo);
    // Wenn Pruefzifferfehler, dann Methode 06
    if ($markC7 == 1) {
      $markC7 = $this->Mark06($AccountNo);
    }
    return $markC7;
  }

  /* --- Added FrankM 20080519 --- */
  function MarkC8($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    // Zuerst mit Methode 00 pruefen.
    $markC8 = $this->Mark00($AccountNo);
    // Wenn Pruefzifferfehler, dann Methode 04
    if ($markC8 == 1) {
      $markC8 = $this->Mark04($AccountNo);
      // Wenn Pruefzifferfehler, dann Methode 07
      if ($markC8 == 1) {
        $markC8 = $this->Mark07($AccountNo);
      }
    }
    return $markC8;
  }

  /* --- Added FrankM 20080519 --- */
  function MarkC9($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    // Zuerst mit Methode 00 pruefen.
    $markC9 = $this->Mark00($AccountNo);
    // Wenn Pruefzifferfehler, dann Methode 07
    if ($markC9 == 1) {
      $markC9 = $this->Mark07($AccountNo);
    }
    return $markC9;
  }

  /* --- Added FrankM 20080717 --- */
  function MarkD0($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    // Wenn die erste und zweite Stelle = 57, dann keine Pruefung.
    if (substr($AccountNo, 0, 2) == "57") {
      $markD0 = $this->Mark09($AccountNo);
    // Andernfalls Methode 20 (modifizierte Methode 06).
    } else {
      $markD0 = $this->Method06($AccountNo, '398765432', FALSE, 10, 11);
    }
    return $markD0;
  }

  /* --- Added FrankM 20080717 ---
     --- Changed FrankM 20100602 ---
   --- Changed Nico Sommer 20110120 --- */
   
  function MarkD1($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $FirstLeftDigit = substr($AccountNo, 0, 1);
    $AccountSequence = substr($AccountNo, 1, 9);
    if ((substr($AccountNo, 0, 1) == "2") or (substr($AccountNo, 0, 1) == "7") or (substr($AccountNo, 0, 1) == "8")) {
      $markD1 = 1;
    } else {
  // Je nach vorhandener erster Stelle von links die Konstante zuordnen.
      switch ($FirstLeftDigit) {
        case 0:
          $Help = '4363380' . $AccountSequence;
          break;
        case 1:
          $Help = '4363381' . $AccountSequence;
          break;
        case 3:
          $Help = '4363383' . $AccountSequence;
          break;
        case 4:
          $Help = '4363384' . $AccountSequence;
          break;
        case 5:
          $Help = '4363385' . $AccountSequence;
         break;
        case 6:
          $Help = '4363386' . $AccountSequence;
         break;
        case 9:
          $Help = '4363389' . $AccountSequence;
         break;
      }  /* end switch */
      // Methode 00, 16. Stelle Pruefziffer, Modulator 10,
      // Pruefziffer NICHT verschieben, ExpandAccount NICHT anwenden.
      $markD1 = $this->Method00($Help, '212121212121212', 16, 10, 0, 1);
    }
    return $markD1;
  }

  /* --- Added FrankM 20081208 --- */
  function MarkD2($AccountNo) {
    // Zuerst mit Methode 95 pruefen.
    $markD2 = $this->Mark95($AccountNo);
    // Wenn Pruefzifferfehler, dann mit Methode 00 pruefen.
    if ($markD2 == 1) {
      $markD2 = $this->Mark00($AccountNo);
      // Wenn Pruefzifferfehler, dann mit Methode 68 pruefen.
      if ($markD2 == 1) {
        $markD2 = $this->Mark68($AccountNo);
      }
    }
    return $markD2;
  }

  /* --- Added FrankM 20081208 --- */
  function MarkD3($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    $markD3 = $this->Mark00($AccountNo);
    // Wenn Pruefzifferfehler, dann mit Methode 27 pruefen.
    if ($markD3 == 1) {
      $markD3 = $this->Mark27($AccountNo);
    }
    return $markD3;
  }

  /* --- Added FrankM 20100602 --- */
  function MarkD4($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
    if ((substr($AccountNo, 0, 1) == "0") or (substr($AccountNo, 0, 1) == "1") or (substr($AccountNo, 0, 1) == "2") or (substr($AccountNo, 0, 1) == "6") or (substr($AccountNo, 0, 1) == "7") or (substr($AccountNo, 0, 1) == "8")) {
      $markD4 = 1;
    } else {
      $Help = '428259' . $AccountNo;

      // Methode 00, 16. Stelle Pruefziffer, Modulator 10,
      // Pruefziffer NICHT verschieben, ExpandAccount NICHT anwenden.
      $markD4 = $this->Method00($Help, '212121212121212', 16, 10, 0, 1);
    }
    return $markD4;
  }

  /* --- Added Nico Sommer 20110120 --- */
  function MarkD5($AccountNo) {
    $AccountNo = $this->ExpandAccount($AccountNo);
  // Wenn 3. und 4. Stelle = 99, dann nur Variante 1 = Methode 06
  if (substr($AccountNo, 2, 2) == "99") {
       $markD5 = $this->Method06($AccountNo, '008765432', FALSE, 10, 11);
     return $markD5;
  // Wenn 3. und 4. Stelle keine 99, dann Varianten 2, 3 und 4
    } else {
         $markD5 = $this->Method06($AccountNo, '000765432', FALSE, 10, 11);
        if ($markD5 == 1) {
       // Wenn falsch, dann Variante 3
      $markD5 = $this->Method06($AccountNo, '000765432', FALSE, 10, 7);
        if ($markD5 == 1) {
        // Wenn falsch, dann Variante 4
        $markD5 = $this->Method06($AccountNo, '000765432', FALSE, 10, 10);
        }
     }
      }
    return $markD5;
  }  /* End of MarkD5 */
  
   /* --- Added Nico Sommer 20110120 --- */
   function MarkD6($AccountNo) {
   $markD6 = $this->Method02($AccountNo, 'A98765432', TRUE);  
    if ($markD6 == 1) {
          $markD6 = $this->Method01($AccountNo, '212121212');
      if ($markD6 == 1) {
        $markD6 = $this->Method00($AccountNo, '212121212', 10);
    }
    }
    return $markD6;  
   } /* End of MarkD6 */

/* ----- Ende Endgueltige Funktionen der einzelnen BerechnungsMethoden. ---- */

/* -------- Dies ist die wichtigste function ---------- */
  function CheckAccount($banktransfer_number, $banktransfer_blz) {
    $KontoNR = ereg_replace('[^0-9]', '', $banktransfer_number);
    $BLZ = ereg_replace('[^0-9]', '', $banktransfer_blz);

    $Result = 0;
    if ($BLZ == '' || strlen($BLZ) < 8) {
      return 8;  /* Keine BLZ �bergeben */
    }
    if ($KontoNR == '') {
      return 9;  /* Keine Kontonummer �bergeben */
    }

/*     Beginn Implementierung */
    $adata = $this->query($BLZ);
    if ($adata == -1) {
      $Result = 5; // BLZ nicht gefunden;
      $PRZ = -1;
      $this->PRZ = $PRZ;
      $this->banktransfer_number=ltrim($banktransfer_number,"0");
      $this->banktransfer_blz=$banktransfer_blz;
    } else {
      $this->Bankname = $adata['bankname'];
      $this->PRZ = str_pad ($adata['prz'], 2, "0", STR_PAD_LEFT);
      $this->banktransfer_number=ltrim($banktransfer_number,"0");
      //$this->banktransfer_number=$this->ExpandAccount($banktransfer_number);
      $this->banktransfer_blz=$banktransfer_blz;

      $PRZ = $adata['prz'];

      switch ($PRZ) {
        case "52" : $Result = $this->Mark52($KontoNR, $BLZ); break;
        case "53" : $Result = $this->Mark53($KontoNR, $BLZ); break;
        /* --- Added FrankM 20060112 --- */
        case "B6" : $Result = $this->MarkB6($KontoNR, $BLZ); break;
        case "C0" : $Result = $this->MarkC0($KontoNR, $BLZ); break;
        default:
          $MethodName = "Mark$PRZ";
          if (method_exists($this, $MethodName)){
            $Result = call_user_func (array($this, $MethodName), $KontoNR);
          } else {
            $Result = 3;
          }
        }  /* end switch */

      } /* end if num_rows */

      return $Result;
    }  /* End of CheckAccount */
  }  /* End Class AccountCheck */
?>