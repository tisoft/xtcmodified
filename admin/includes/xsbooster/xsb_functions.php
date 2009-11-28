<?php
## Class-independent helper functions for xs:booster
## Klassenlose PHP-Hilfsfunktionen fuer den xs:booster
##
## Copyright (c) 2009 xt:booster Ltd.
## http://www.xsbooster.com
##
## Licensed under GNU/GPL
##


// Array in XML-String umwandeln
function xmlize($r){
        if(!isset($r)) return false;
        if(is_string($r)) return '<0>'.$r.'</0>';
        if(!is_array($r)) return false;
        $rr= Array();
        $rrstack= Array();
        $rrstack[0]=$r;
        $rr=$rrstack[0];
        $mykeys=Array();
        $mykeys[0]=array_keys($r);
        $istack=Array();
        $istack[0]=0;
        $i=$istack[0];
        $current_depth=0;
        $myresult='';
        while((isset($rr[$mykeys[$current_depth][$i]])) || $current_depth>0)
        {
                //keine blaetter mehr am ast, tag abschliessen
                if(!isset($rr[$mykeys[$current_depth][$i]]))
                {
                        --$current_depth;
                        $i=$istack[$current_depth];
                        $myresult .= '</'.$mykeys[$current_depth][$i].'>';
                        $rr=$rrstack[$current_depth];
                        $i++;
                        continue;
                }
                //tag oeffnen
                $myresult .= '<'.$mykeys[$current_depth][$i].'>';
                if(is_array($rr[$mykeys[$current_depth][$i]]))
                        {
                        //ast, heruntergehen
                         $mykeys[$current_depth+1]=Array();
                         $mykeys[$current_depth+1]=array_keys($rr[$mykeys[$current_depth][$i]]);
                         $rrstack[$current_depth+1]=$rr[$mykeys[$current_depth][$i]];
                         $rr=$rrstack[$current_depth+1];
                         $istack[$current_depth]=$i;
                         $current_depth++;
                         $i = 0;
                        }
                else
                        {
                        //blatt
                        $myresult .= $rr[$mykeys[$current_depth][$i]];
                        $myresult .= '</'.$mykeys[$current_depth][$i].'>';
                        $i++;
                        }
        }
        return $myresult;
}

// Funktioniert NUR fuer XML-Strings mit disjunkten Tags (vorher aus einem Array erzeugt)
function unXmlize($r)
{
  if(!isset($r)) return '';
  if((substr($r,0,1) != '<') || (substr($r,-1,1) != '>')) return $r;
  $result = Array();
  while(!empty($r))
  {
    $current_key = substr($r,1,strpos($r,'>')-1);
    $keylen = strlen($current_key);
    if(strpos($r,'</'.$current_key.'>', $keylen+2) === False)
    // kein xml tag sondern nur text der so aussieht
    { return $r; } 
    $current_part = substr($r, $keylen+2,strpos($r,'</'.$current_key.'>',$keylen+2)-$keylen-2);
    // product description nicht zerteilen
    if('DESCRIPTION' == $current_key)
    { $result["$current_key"] = $current_part; }
    else
    { $result["$current_key"] = unXmlize($current_part); }
    $r = substr($r,strpos($r,'</'.$current_key.'>')+$keylen+3);
  }
  return $result;
}

// String nach ISO-8859-1 umwandeln, wenn er Zeichen enthaelt die nicht in ISO-8859-1 vorkommen

function toIso8859_1($inputstring)
{
  $not_iso_chars = "Â¡Â¢Â£Â¤Â¥Â¦Â§Â¨Â©ÂªÂ«Â¬Â®?Â° Â± Â² Â³ Â´ Âµ Â¶ Â· Â¸ Â¹ Âº Â» Â¼ Â½ Â¾ Â¿Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã ÃÃ Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã Ã ÃÃ  Ã¡ Ã¢ Ã£ Ã¤ Ã¥ Ã¦ Ã§ Ã¨ Ã© Ãª Ã« Ã¬ Ã­ Ã® Ã¯Ã° Ã± Ã² Ã³ Ã´ Ãµ Ã¶ Ã· Ã¸ Ã¹ Ãº Ã» Ã¼ Ã½ Ã¾ Ã¿";

  if(strpbrk($inputstring,$not_iso_chars) !== False)
  { $inputstring = utf8_decode($inputstring); }
  return $inputstring;
}

// DB-Query mit retry falls 'MySQL Server has gone away'

function  xsb_db_query($query, $link = 'db_link')
{
    global $$link, $logger;

    if (STORE_DB_TRANSACTIONS == 'true') {
      if (!is_object($logger)) $logger = new logger;
      $logger->write($query, 'QUERY');
    }

    do {
    $result = mysql_query($query, $$link);
    } while (2006 == mysql_errno());

    if(0 != mysql_errno()) { xtc_db_error($query, mysql_errno(), mysql_error()); }

    if (STORE_DB_TRANSACTIONS == 'true') {
      if (mysql_error()) $logger->write(mysql_error(), 'ERROR');
    }

    return $result;
}

?>
