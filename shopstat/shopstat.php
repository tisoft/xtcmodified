<?
/*-----------------------------------------------------------------------
    Description : xtC-ShopStat-Module by www.ShopStat.com (Hartmut König)
    Url         : http://www.shopstat.com
    Email       : info@shopstat.com
    Copyright   : © 2004 ShopStat.com All Rights Reserved.
    Version     : $Id: shopstat.php,v 1.2 2005/03/05 13:36:19 Administrator Exp $
------------------------------------------------------------------------*/
    //error_reporting(E_ALL);

    #-- Check Installation
    if(isset($_REQUEST['test']) && $_REQUEST['test'])
        {
        #-- is directory writable ?!
        if(@fopen(dirname(__FILE__)."/test.txt","a"))
            {
            @unlink(dirname(__FILE__)."/test.txt");
            }
        else{
            print "<br /><br /><font color=red><strong><center>Directory 'shopstat' is not writeable -> please do a chmod 777 !</center></strong></font><br /><br />";
            exit;
            }
        }

    $shopstat_debug = false;
    #-- Make Debugging available
    if( session_id() == '111')
        {
        $shopstat_debug     = true;
        $shopstat_debug_msg = "";
        }

    #-- Deny direct access
    if( preg_match("/".basename(__FILE__)."/",$_SERVER['SCRIPT_NAME']) ||
        preg_match("/".basename(__FILE__)."/",$_SERVER['PHP_SELF']) )
        {
if( $shopstat_debug ){$shopstat_debug_msg .= '\nRESULT:\n------------\nNOTLOGGED->DIRECT ACCESS!';print "<script>alert('".$shopstat_debug_msg."');</script>";}
        return;
        }

    #-- Allready logged, so we return
    if( isset($shoplog_islogged) &&
        $shoplog_islogged )
        {
if( $shopstat_debug ){$shopstat_debug_msg .= '\nRESULT:\n------------\nNOTLOGGED->ALLREADY LOGGED!';print "<script>alert('".$shopstat_debug_msg."');</script>";}
        return;
        }

    if(isset($_GET['action']))
        {
        $log_action = $_GET['action'];
        }
    elseif(isset($_POST['action']))
        {
        $log_action = $_POST['action'];
        }
    else{
        $log_action = "";
        }

    $logit = true;

//------------------------------------------------------------------------------
//-- START GETTING ACTION INFORMATION
//------------------------------------------------------------------------------
if( $shopstat_debug ){$shopstat_debug_msg .= '\nFILE:\n------------\n'.$shopstat_ref.'\n';$shopstat_debug_msg .= '\nREQUEST:\n------------\n';foreach($_REQUEST as $key=>$value){$shopstat_debug_msg .= $key.'='.$value.'\n';}print '<script>alert(\''.$shopstat_debug_msg.'\');</script>';}

    $shoplog_prodid = "";
    if( isset($_GET['products_id']) ||
        isset($_GET['BUYproducts_id']) )
        {
        switch($log_action)
        	{
        	case 'add_product':
        		$shoplog_method = 'add';
        	break;
        	case 'buy_now':
        		$shoplog_method = 'add';
            break;
           	default:
       		    $shoplog_method = 'showitem';
        	break;
            }

        if(isset($_GET['products_id']))
            {
            $shoplog_prodid     = xtc_get_prid($_GET['products_id']);
            }
        else{
        	$shoplog_prodid     = xtc_get_prid($_GET['BUYproducts_id']);
            }
        }
    else{
        if( preg_match("/checkout_shipping\.php/",$_SERVER['SCRIPT_NAME']) ||
            preg_match("/checkout_shipping\.php/",$_SERVER['PHP_SELF']) )
            {
            $shoplog_method = 'buy1';
            }
        elseif( preg_match("/checkout_payment\.php/",$_SERVER['SCRIPT_NAME']) ||
                preg_match("/checkout_payment\.php/",$_SERVER['PHP_SELF']) )
            {
            $shoplog_method = 'buy2';
            }
        elseif( preg_match("/checkout_confirmation\.php/",$_SERVER['SCRIPT_NAME']) ||
                preg_match("/checkout_confirmation\.php/",$_SERVER['PHP_SELF']) )
            {
            $shoplog_method = 'buy3';
            }
        elseif( preg_match("/checkout_process\.php/",$_SERVER['SCRIPT_NAME']) ||
                preg_match("/checkout_process\.php/",$_SERVER['PHP_SELF']) )
            {
            $shoplog_method = 'validate_and_send';
            }

       elseif( isset($_GET['keywords']) &&
              (preg_match("/search/",$_SERVER['SCRIPT_NAME']) || preg_match("/search/",$_SERVER['PHP_SELF']))  )
            {
            $shoplog_method = 'search';
            }
        elseif( $log_action == 'update_product' )
            {
            if(isset($_POST['cart_delete']))
    			{
    			$shoplog_method = 'delitem';
    			$shoplog_prodid = xtc_get_prid($_POST['cart_delete'][0]);
                }
            else{
                $shoplog_method = 'change';
    			}
            }
        elseif( isset($_SERVER['SCRIPT_NAME']) &&
                (preg_match("/shopping_cart\.php/",$_SERVER['SCRIPT_NAME']) ||
                 preg_match("/shopping_cart\.php/",$_SERVER['PHP_SELF'])) )
            {
            $shoplog_method = 'review';
            }
        elseif( isset($_GET['cPath']) ||
                (preg_match("/index\.php/",$_SERVER['SCRIPT_NAME']) ||
                 preg_match("/index\.php/",$_SERVER['PHP_SELF'])) )
            {
		    $shoplog_method = 'listitems';
		    }
        }

//------------------------------------------------------------------------------
//-- AFTER GETTING THE METHOD, WE KNOW WHAT TO DO
//------------------------------------------------------------------------------
    //-- This methods are logged later, so we return
    if( (   !(isset($shoplog_mode) && $shoplog_mode) ) &&
        (   (isset($shoplog_method) && $shoplog_method == 'buy1') ||
            (isset($shoplog_method) && $shoplog_method == 'buy2') ||
            (isset($shoplog_method) && $shoplog_method == 'buy3') ||
            (isset($shoplog_method) && $shoplog_method == 'listitems') ||
            (isset($shoplog_method) && $shoplog_method == 'showitem') ||
            (isset($shoplog_method) && $shoplog_method == 'search')) )
        {
if( $shopstat_debug ){$shopstat_debug_msg .= '\nMETHOD:\n------------\n'.$shoplog_method;$shopstat_debug_msg .= '\nRESULT:\n------------\nNOTLOGGED!';print "<script>alert('".$shopstat_debug_msg."');</script>";}
        return;
        }
    #-- If the method is not set, we return
    if(!(isset($shoplog_method) && $shoplog_method) )
        {
if( $shopstat_debug ){$shopstat_debug_msg .= '\nRESULT:\n------------\nNOTLOGGED!';print "<script>alert('".$shopstat_debug_msg."');</script>";}
        return;
        }
if( $shopstat_debug ){$shopstat_debug_msg .= '\nPARAS:\n------------\n';$shopstat_debug_msg .= 'MODE: '.$shoplog_mode.'\nMETHOD: '.$shoplog_method.'\nPRODID: '.$shoplog_prodid.'\n';$shopstat_debug_msg .= '\POST:\n------------\n';foreach($_POST as $key=>$value){if(is_array($value)){foreach($value as $k=>$v){$shopstat_debug_msg .= $k.'='.$v.'\n';}}else{$shopstat_debug_msg .= $key.'='.$value.'\n';}}$shopstat_debug_msg .= '\GET:\n------------\n';foreach($_GET as $key=>$value){if(is_array($value)){foreach($value as $k=>$v){$shopstat_debug_msg .= $k.'='.$v.'\n';}}else{$shopstat_debug_msg .= $key.'='.$value.'\n';}}$shopstat_debug_msg .= '\SERVER:\n------------\n';foreach($_SERVER as $key=>$value){$shopstat_debug_msg .= $key.'='.$value.'\n';}print '<script>alert(\''.$shopstat_debug_msg.'\');</script>';}
//------------------------------------------------------------------------------
//-- START GETTING BASE INFORMATION
//------------------------------------------------------------------------------
    require_once(DIR_FS_INC . 'shopstat_functions.inc.php');
    require_once(DIR_FS_INC . 'xtc_get_products_name.inc.php');

    //Parameter :
    //shoplog_softwareid : Name of the shopsystem
    $shoplog_softwareid = PROJECT_VERSION;

    //shoplog_ip : $REMOTE_ADDR
    $shoplog_ip 		= $_SERVER['REMOTE_ADDR'];

    //shoplog_useragent : $HTTP_USER_AGENT
    $shoplog_useragent 	= $_SERVER['HTTP_USER_AGENT'];

    //shoplog_id : user-ID oder sess_id
    $shoplog_id			= $_REQUEST[session_name()];

    //shoplog_referer : $HTTP_REFERER
    if(isset($_SERVER['HTTP_REFERER']))
    	{
    	//-- prevent (HTTP_REFERER) Hijacking as mentioned at:
    	//-- http://www.securiteam.com/unixfocus/5KP0G2K9FI.html
    	$shoplog_referer = htmlspecialchars(strip_tags($_SERVER['HTTP_REFERER']));
    	}
    else{
    	$shoplog_referer = "-";
    	}

    #-- Get the category path
    if( isset($_GET['cPath']) )
        {
        $shoplog_cat = shopstat_getRealPath($_GET['cPath']);
        }
    else{
        $shoplog_cat = shopstat_getRealPath(xtc_get_product_path($shoplog_prodid));
        }

    #-- Get products model
    //-- 23.04.2006
    //-- Falls eine Artikelnummer (oder Attributsartikelnr) vorhanden ist, wird
    //-- diese gespeichert ansonsten die ID
    $shoplog_prequery   = "SELECT products_model FROM ".TABLE_PRODUCTS." WHERE products_id = '".$shoplog_prodid."'";
    $shoplog_prequery   = xtDBquery($shoplog_prequery);
    $shoplog_products   = xtc_db_fetch_array($shoplog_prequery,true);
    (!empty($shoplog_products['products_model']))
        ? $shopstat_prodartnr = $shoplog_products['products_model']
        : $shopstat_prodartnr = $shoplog_prodid;

//------------------------------------------------------------------------------
//-- START GETTING DETAIL INFORMATION
//------------------------------------------------------------------------------
    //shoplog_query : Additional Info for each method
    #-- user add or delete a product (cartview or productview)
    if(	(isset($shoplog_method) && $shoplog_method == 'add') ||
    	(isset($shoplog_method) && $shoplog_method == 'delitem') )
    	{
    	#-- article# & articlename
    	$shoplog_query =    $shopstat_prodartnr."&amp;".
    	                    urlencode(strip_tags(xtc_get_products_name($shoplog_prodid,$languages_id)));
    	}
    #-- user look at a product
    elseif(isset($shoplog_method) && $shoplog_method == 'showitem')
    	{
    	#-- Art-Nr & Name & Kategorie
    	$shoplog_query =    $shopstat_prodartnr.'&amp;'.
    	                    urlencode(strip_tags(xtc_get_products_name($shoplog_prodid,$languages_id))).'&amp;'.
   	                    urlencode(strip_tags($shoplog_cat));
    	}
    #-- user browse through the products
    elseif(isset($shoplog_method) && ($shoplog_method == 'listitems'))
    	{
    	(isset($shoplog_mode) && $shoplog_mode)
    	    ? $shoplog_query = urlencode(strip_tags($shoplog_cat))
    	    : false;
    	}
    #-- user did a search in the shop
    elseif(isset($shoplog_method) && $shoplog_method == 'search')
    	{
   	    $shoplog_query = $_GET['keywords'];

        if(isset($listing_split->number_of_rows) && $listing_split->number_of_rows > 0)
            {
            $shoplog_method = 'search_found';
            $shoplog_query  .= '&amp;'.$listing_split->number_of_rows;
            }
    	}
    #-- user has placed an order
    elseif(isset($shoplog_method) && $shoplog_method == 'validate_and_send')
    	{
        require_once(DIR_WS_CLASSES . 'order.php');

        //-- [1.2]
        //-- Sicherheitsabfrage für die verschiedenen xtc-Versionen
        //-- Diese MUSS mit $xtPrice bleiben, da rückwärtskompatibel
        $order = new order('',$xtPrice);
        if(is_object($order))
            {
        	#-- Total & paymethod & shippingcosts ->
        	$shoplog_query	= 	((isset($order->info['total'])) ? $order->info['total'] : '').'&amp;'.
        						((isset($order->info['payment_method'])) ? $order->info['payment_method'] : '').'&amp;'.
        						((isset($order->info['shipping_cost'])) ? $order->info['shipping_cost'] : '').'->';

            #-- Ordered products
        	if(isset($order->products))
        		{
        		$init = 1;
        		foreach($order->products as $item)
        			{
                    #-- take apart the unique products
                    if(!$init){$shoplog_query .= '|';}

        			#-- art-nr & name & category & quantity & price
        			$shoplog_query	.= implode('&amp;', array( 	$item['model'],
        													urlencode(strip_tags($item['name'])),
        													urlencode(strip_tags(shopstat_getRealPath(xtc_get_product_path(xtc_get_prid($item['id']))))),
       													    $item['qty'],
        													$item['price']
        													)
        										);
                    $init=0;
        			}
        		}
            }
    	}
//------------------------------------------------------------------------------
//-- START LOGGING
//------------------------------------------------------------------------------
    if( $logit && !(isset($shoplog_islogged) && $shoplog_islogged) )
    	{
        (!isset($shoplog_merchantid)|| $shoplog_merchantid == "")   ? $shoplog_merchantid = "shoplog"           : false;
        (!isset($shoplog_softwareid)|| $shoplog_softwareid == "")   ? $shoplog_softwareid = "ShopLog General"   : false;
        (!isset($shoplog_ip) 		|| $shoplog_ip == "")           ? $shoplog_ip = "-"                         : false;
        (!isset($shoplog_useragent) || $shoplog_useragent == "")    ? $shoplog_useragent = "-"                  : false;
        (!isset($shoplog_referer) 	|| $shoplog_referer == "")      ? $shoplog_referer = "-"                    : false;
        (!isset($shoplog_id) 		|| $shoplog_id == "")           ? $shoplog_id = "-"                         : false;
        (!isset($shoplog_method) 	|| $shoplog_method == "")       ? $shoplog_method = "listitems"             : false;
        (!isset($shoplog_cat) 		|| $shoplog_cat == "")          ? $shoplog_cat = "-"                        : false;
        (!isset($shoplog_query) 	|| $shoplog_query == "")        ? $shoplog_query = "-"                      : false;

        #-- Make sure we get through
        ignore_user_abort(true);

        #-- Manage log-rotation
        $sl_path        = dirname(__FILE__)."/";
        $sl_weeknr      = strftime("%U");
        $sl_year        = strftime("%Y");
        $sl_datestr     = date("Y-m-d");
        $sl_timestr	    = date("H:i:s");
        $sl_logfilename = $sl_path.$shoplog_merchantid.'.log.'.$sl_year.$sl_weeknr;

        if (!@file_exists($sl_logfilename))
             {
             $sl_handle = @fopen($sl_logfilename,"w+");
             chmod($sl_logfilename, 0644);
             (function_exists("set_file_buffer"))
                ? @set_file_buffer($sl_handle,0)
                : false;
             @flock($sl_handle,2);
             @fputs($sl_handle,"#Software: ".$shoplog_softwareid."\n");
             @fputs($sl_handle,"#Version: 1.0\n");
             @fputs($sl_handle,"#Date: ".$sl_datestr."\n");
             @fputs($sl_handle,"#Fields  : date time c-ip cs(User-Agent) cs(Referer) cs-sess-id cs-method category cs-uri-query\n");
             @fclose($sl_handle);

             $sl_lastweek = $sl_weeknr-1;
             $sl_lastyear = $sl_year;
             while (true)
                {
                $sl_i++;
                if ($sl_lastweek<0)
                    {
                    $sl_lastweek=56;
                    $sl_lastyear=$sl_year-1;
                    }
                $sl_lastweek            = sprintf("%02d",$sl_lastweek);
                $sl_lastyear            = sprintf("%04d",$sl_lastyear);
                $sl_logfilename_last    = $sl_path.$shoplog_merchantid.'.log.'.$sl_lastyear.$sl_lastweek;

/*
                if(!@file_exists($sl_logfilename_last) ||
                    @file_exists($sl_logfilename_last.'.gz') ){break;}
*/
                if($sl_i > 20){break;}
                if (file_exists($sl_logfilename_last))
                    { # pack theold file ...
                    //@Exec("gzip $sl_logfilename_last");

                    $sl_dest   = $sl_logfilename_last.'.gz';
                    $sl_error  = false;
                    if($sl_fpout=gzopen($sl_dest,'wb9'))
                        {
                        if($sl_fpin=fopen($sl_logfilename_last,'rb'))
                            {
                            while(!feof($sl_fpin))gzwrite($sl_fpout,fread($sl_fpin,1024*512));
                            fclose($sl_fpin);
                            }else{$sl_error=true;}
                        gzclose($sl_fpout);
                        }else{$sl_error=true;}
                    if(!$sl_error){@unlink($sl_logfilename_last);}

                    /*
                    rename($sl_logfilename_last,$sl_logfilename_last.".pack");
                    $sl_handle=fopen($sl_logfilename_last.".pack","r");
                    if (function_exists("set_file_buffer")) @set_file_buffer($sl_handle,0);
                    $fz=gzopen($sl_logfilename_last.".gz","w+");
                    if (function_exists("set_file_buffer")) @set_file_buffer($fz,0);
                    while (! @feof($sl_handle))
                       {
                        $s=fgets($sl_handle,2048);
                        echo strlen($s)."<br />";flush();
                        gzputs($fz,$s);
                        }
                    gzclose($fz);
                    fclose($sl_handle);
                    unlink($sl_logfilename_last.".pack");
                    */
                    }

                $sl_lastweek--;
                }

/*
            $sl_errfile = @tempnam("/tmp","shoplog_error");
            @Exec("ls -at1 ".$sl_path.$shoplog_merchantid.".log* 2>".$sl_errfile,$ergs);
            @unlink($sl_errfile);
            for ($i=10;$i<sizeof($ergs);$i++)
                {
                @unlink($ergs[$i]);
                }
*/
            }

        #-- Log current action
        $sl_handle = @fopen($sl_logfilename,"a");
        (function_exists("set_file_buffer"))
            ? @set_file_buffer($sl_handle,0)
            : false;
        @flock($sl_handle,2);

        $sl_log = implode("\t", array(	$sl_datestr,
        								$sl_timestr,
        								$shoplog_ip,
        								str_replace(" ","+",$shoplog_useragent),
        								$shoplog_referer,
        								urlencode($shoplog_id),
        								$shoplog_method,
        								urlencode($shoplog_cat),
        								$shoplog_query ) )."\n";

        @fputs($sl_handle, $sl_log);
if( $shopstat_debug ){$shopstat_debug_msg .= '\nLOG:\n------------\n'.rtrim($sl_log);print "<script>alert('".$shopstat_debug_msg."');</script>";}
        @flock($sl_handle,3);
        @fclose($sl_handle);
        $shoplog_islogged = true;
        }
    else{
if( $shopstat_debug ){$shopstat_debug_msg .= '\nRESULT:\n------------\nNOTLOGGED!';print "<script>alert('".$shopstat_debug_msg."');</script>";}
        }


?>