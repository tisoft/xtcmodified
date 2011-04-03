<?php
/* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2011 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2011 WEB-Shop Software (function.piwik.php 1871) http://www.webs.de/

   Add the Piwik tracking code (and the possibility to track the order details as well)

   Usage: Put one of the following tags into the templates\yourtemplate\index.html at the bottom
   {piwik url=piwik.example.com id=1} or
   {piwik url=piwik.example.com id=1 goal=1}
   where "id=1" is the domain-ID you want to track (see your Piwik configuration for details)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/
function smarty_function_piwik($params, &$smarty) {

  $url = isset($params['url']) ? $params['url'] : false;
  $id = isset($params['id']) ? (int)$params['id'] : false;
  $goal = isset($params['goal']) ? (int)$params['goal'] : false;

  if (!$url || !$id) {
    return false;
  }

  $url = str_replace(array('http://', 'https://'), '', $url);
  $url = trim($url, '/');

  $beginCode = '<script type="text/javascript">
    var _paq = _paq || [];
    (function(u,d,t){
      _paq.push([\'setSiteId\', '.$id.'],[\'setTrackerUrl\', u + \'piwik.php\']);
      _paq.push([\'trackPageView\']);
  ';

  $endCode = '    var g = d.createElement(t),
      s = d.getElementsByTagName(t)[0];
      g.async = true;
      g.src = u + \'piwik.js\';
      s.parentNode.insertBefore(g,s);
    })(\'//'.$url.'/\', document, \'script\');
  </script>
  <noscript><p><img src="http://'.$url.'/piwik.php?idsite=1&rec=1" style="border:0" alt="" /></p></noscript>
  ';

  $orderCode = null;
  if ((strpos($_SERVER['PHP_SELF'], '/checkout_success.php') !== false) && ($goal > 0)) {
    $orderCode = getOrderDetailsPiwik($goal);
  }
  return $beginCode . $orderCode . $endCode;
}

/**
 * Get the order details
 *
 * @global <type> $last_order
 * @param mixed $goal
 * @return string Code for the eCommerce tracking
 */
function getOrderDetailsPiwik($goal) {
  global $last_order; // from checkout_success.php

  $query = xtc_db_query("-- function.piwik.php
    SELECT value
    FROM " . TABLE_ORDERS_TOTAL . "
    WHERE orders_id = '" . $last_order . "' AND class='ot_total'");
  $orders_total = xtc_db_fetch_array($query);

  return "_paq.push(['trackGoal', '" . $goal . "', '" . $orders_total['value'] . "' ]);\n";
}