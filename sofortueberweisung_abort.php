<?php
/**
 *
 *
 * @version Sofortüberweisung 1.9  05.06.2007
 * @author Henri Schmidhuber  info@in-solution.de
 * @copyright 2006 - 2007 Henri Schmidhuber
 * @link http://www.in-solution.de
 * @link http://www.xt-commerce.com
 * @link http://www.sofort-ueberweisung.de
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; version 2 of the License
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307
 * USA
 *
 ***********************************************************************************
 * this file contains code based on:
 * (c) 2000 - 2001 The Exchange Project
 * (c) 2001 - 2003 osCommerce, Open Source E-Commerce Solutions
 * (c) 2003	 nextcommerce (account_history_info.php,v 1.17 2003/08/17); www.nextcommerce.org
 * (c) 2003 - 2006 XT-Commerce
 * Released under the GNU General Public License
 ***********************************************************************************
 *
 */

  include( 'includes/application_top.php');
   // create smarty elements
  $smarty = new Smarty;
  $smarty->assign('language', $_SESSION['language']);
  // include boxes
  require(DIR_FS_CATALOG .'templates/'.CURRENT_TEMPLATE. '/source/boxes.php');
   require(DIR_WS_LANGUAGES . $_SESSION['language'] . '/sofortueberweisung_abort.php');
  // include needed functions
  require_once(DIR_FS_INC . 'xtc_draw_checkbox_field.inc.php');
  require_once(DIR_FS_INC . 'xtc_image_button.inc.php');

 $breadcrumb->add(NAVBAR_TITLE);
 require(DIR_WS_INCLUDES . 'header.php');

  $smarty->caching = 0;

  $smarty->assign('language', $_SESSION['language']);
  $smarty->assign('SOFORTUEBERWEISUNG_TITLE', NAVBAR_TITLE);
  $smarty->assign('TEXT_INFORMATION', TEXT_INFORMATION);
  $smarty->assign('BUTTON_CONTINUE','<a href="' . xtc_href_link(FILENAME_DEFAULT) . '">' . xtc_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>');
  $main_content=$smarty->fetch(CURRENT_TEMPLATE . '/module/sofortueberweisung_abort.html');
  $smarty->assign('main_content',$main_content);
  $smarty->caching = 0;
  if (!defined(RM)) $smarty->load_filter('output', 'note');
  $smarty->display(CURRENT_TEMPLATE . '/index.html');
?>