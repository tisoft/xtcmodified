<?php
/* --------------------------------------------------------------
   $Id: banner_daily.php 899 2005-04-29 02:40:57Z hhgag $   

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   --------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(banner_daily.php,v 1.2 2002/05/09); www.oscommerce.com 
   (c) 2003	 nextcommerce (banner_daily.php,v 1.6 2003/08/18); www.nextcommerce.org

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

  require(DIR_WS_CLASSES . 'phplot.php');

  $year = (($_GET['year']) ? $_GET['year'] : date('Y'));
  $month = (($_GET['month']) ? $_GET['month'] : date('n'));

  $days = (date('t', mktime(0,0,0,$month))+1);
  $stats = array();
  for ($i=1; $i<$days; $i++) {
    $stats[] = array($i, '0', '0');
  }

  $banner_stats_query = xtc_db_query("select dayofmonth(banners_history_date) as banner_day, banners_shown as value, banners_clicked as dvalue from " . TABLE_BANNERS_HISTORY . " where banners_id = '" . $banner_id . "' and month(banners_history_date) = '" . $month . "' and year(banners_history_date) = '" . $year . "'");
  while ($banner_stats = xtc_db_fetch_array($banner_stats_query)) {
    $stats[($banner_stats['banner_day']-1)] = array($banner_stats['banner_day'], (($banner_stats['value']) ? $banner_stats['value'] : '0'), (($banner_stats['dvalue']) ? $banner_stats['dvalue'] : '0'));
  }

  $graph = new PHPlot(600, 350, 'images/graphs/banner_daily-' . $banner_id . '.' . $banner_extension);

  $graph->SetFileFormat($banner_extension);
  $graph->SetIsInline(1);
  $graph->SetPrintImage(0);

  $graph->SetSkipBottomTick(1);
  $graph->SetDrawYGrid(1);
  $graph->SetPrecisionY(0);
  $graph->SetPlotType('lines');

  $graph->SetPlotBorderType('left');
  $graph->SetTitleFontSize('4');
  $graph->SetTitle(sprintf(TEXT_BANNERS_DAILY_STATISTICS, $banner['banners_title'], strftime('%B', mktime(0,0,0,$month)), $year));

  $graph->SetBackgroundColor('white');

  $graph->SetVertTickPosition('plotleft');
  $graph->SetDataValues($stats);
  $graph->SetDataColors(array('blue','red'),array('blue', 'red'));

  $graph->DrawGraph();

  $graph->PrintImage();
?>