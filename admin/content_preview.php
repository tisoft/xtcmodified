<?php
  /* -----------------------------------------------------------------------------------------
   $Id$

   xtcModified - community made shopping
   http://www.xtc-modified.org

   Copyright (c) 2010 xtcModified
   -----------------------------------------------------------------------------------------
   based on:
   (c) 2003   nextcommerce (content_preview.php,v 1.2 2003/08/25); www.nextcommerce.org
   (c) 2006 XT-Commerce (coupon_admin.php 1084 2005-07-23)

   Released under the GNU General Public License
   ---------------------------------------------------------------------------------------*/

  require('includes/application_top.php');

  if ($_GET['pID']=='media') {
    $content_query=xtc_db_query("SELECT
                                        content_file,
                                        content_name,
                                        file_comment
                                   FROM ".TABLE_PRODUCTS_CONTENT."
                                  WHERE content_id='".(int)$_GET['coID']."'");
    $content_data=xtc_db_fetch_array($content_query);
  } else {
    $content_query=xtc_db_query("SELECT
                                        content_title,
                                        content_heading,
                                        content_text,
                                        content_file
                                   FROM ".TABLE_CONTENT_MANAGER."
                                  WHERE content_id='".(int)$_GET['coID']."'");
    $content_data=xtc_db_fetch_array($content_query);
  }
?>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>" />
    <title><?php echo TITLE; ?></title>
    <link rel="stylesheet" type="text/css" href="includes/stylesheet.css" />
  </head>
  <body>
    <div class="pageHeading"><?php echo $content_data['content_heading']; ?></div>
    <br />
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="main">
          <?php
            if ($content_data['content_file']!=''){
              if (strpos($content_data['content_file'],'.txt'))
                echo '<pre>';
              if ($_GET['pID']=='media') {
                // display image
                if (preg_match('/.gif/i',$content_data['content_file']) or preg_match('/.jpg/i',$content_data['content_file']) or  preg_match('/.png/i',$content_data['content_file']) or  preg_match('/.tif/i',$content_data['content_file']) or  preg_match('/.bmp/i',$content_data['content_file'])) { // Hetfield - 2009-08-19 - replaced deprecated function eregi with preg_match to be ready for PHP >= 5.3
                  echo xtc_image(DIR_WS_CATALOG.'media/products/'.$content_data['content_file']);
                } else {
                  include(DIR_FS_CATALOG.'media/products/'.$content_data['content_file']);
                }
              } else {
                include(DIR_FS_CATALOG.'media/content/'.$content_data['content_file']);
              }
              if (strpos($content_data['content_file'],'.txt')) 
                echo '</pre>';
            } else {
              echo $content_data['content_text'];
            }
          ?>
        </td>
      </tr>
    </table>
  </body>
</html>