<?php
/* --------------------------------------------------------------
$Id: credits.php 1263 2005-09-30 10:14:08Z mz $

XT-Commerce - community made shopping
http://www.xt-commerce.com

Copyright (c) 2003 XT-Commerce
--------------------------------------------------------------
based on:
(c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
(c) 2002-2003 osCommercecoding standards (a typical file) www.oscommerce.com
(c) 2003	 nextcommerce ( start.php,v 1.6 2003/08/19); www.nextcommerce.org

Released under the GNU General Public License
--------------------------------------------------------------*/

require('includes/application_top.php');

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['language_charset']; ?>">
		<title><?php echo TITLE; ?></title>
		<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
		<style type="text/css">
			#credits {
				margin: 5px;
				padding: 0px 20px;
				background-color: #F7F7F7;
				font-family: Verdana, Arial, sans-serif;
				font-size: 12px;
			}
			#contentHead dt {
				float: right;
			}
			#contentHead dd {
				padding-left: 35px;				
			}
			#credits dl dt {
				color: #D68000;
				font-size: 12px;
				font-weight: bold;
			}
			dl#person dt {
				color: black;
				font-weight: bold;
				float: left;
				font-size: 12px;
			}

			dl#person dd {
				margin-left: 90px;
				font-size: 12px;
			}
			a:link, a:hover, a:visited, a:active {
			  font-size: 12px;
			  text-decoration: underline;			  
			}			
		</style>
	</head>
	<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF">
		<!-- header //-->
		<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
		<!-- header_eof //-->

		<!-- body //-->
		<table border="0" width="100%" cellspacing="2" cellpadding="2">
			<tr>
				<td class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
					<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
						<!-- left_navigation //-->
						<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
						<!-- left_navigation_eof //-->
					</table>
				</td>
				<td class="boxCenter" width="100%" valign="top">
				  <table border="0" width="100%" cellspacing="0" cellpadding="0">
				  <tr><td>
					<div id="credits">
						<dl id="contentHead">
							<dt style="float: left"><?php echo xtc_image(DIR_WS_ICONS.'heading_news.gif'); ?></dt>
							<dd><span class="pageHeading">Credits</span><br /><span class="main">Danksagung</span></dd>
						</dl>
						<font color="D68000" size="2" face="Verdana, Arial, Helvetica, sans-serif"><strong><?php echo PROJECT_VERSION; ?></strong></font>
						<br />
						<br />
						Released under the GNU General Public License<br /><br />
						This program is distributed in the hope that it will be useful, but <strong>WITHOUT ANY WARRANTY</strong>;<br />without even the implied warranty of <strong>MERCHANTABILITY</strong> or <strong>FITNESS FOR A PARTICULAR PURPOSE</strong>.<br />See the GNU General Public License for more details. You should have received a copy of the<br />GNU General Public License along with this program; if not, write to the<br />Free Software Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.<br />See <a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">http://www.gnu.org/copyleft/gpl.html</a> for details. <br />
						<br />
						<p>Wir danken allen Programmieren und Entwicklern, die an diesem Projekt mitarbeiten. Sollten wir jemanden in der unten stehenden Auflistung vergessen haben, so bitten wir um Mitteilung über das <a href="http://www.xtc-modified.org/forum/" target="_blank">Forum</a> oder an einen der genannten Entwickler.</p>
						<p>Dieses Programm wurde ver&ouml;ffentlich, in der Hoffnung hilfreich zu sein. Wir geben jedoch keinerlei Garantie auf die fehlerfreie Implementierung.</p>
						<hr />
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
							<tr>
								<td width="50%" valign="top">
									<dl>
										<dt>Entwickler der xtcModified eCommerce Shopsoftware:</dt>
										<dd>
											<dl id="person">
												<dt>Tomcraft</dt><dd>&lt;tomcraft1980@users.sourceforge.net&gt;</dd>
												<dt>Hetfield</dt><dd>&lt;jhetfield@users.sourceforge.net&gt;</dd>
												<dt>DokuMan</dt><dd>&lt;dokuman@users.sourceforge.net&gt;</dd>
												<dt>web28</dt><dd>&lt;web28@users.sourceforge.net&gt;</dd>
												<dt>Pufaxx</dt><dd>&lt;info@gunnart.de&gt;</dd>
												<dt>Christian</dt><dd>&lt;hallo@jungcreative.de&gt;</dd>
											</dl>
										</dd>
									</dl>
								</td>
								<td width="50%" valign="top">
									<dl>
										<dt>Unterstützen Sie die Weiterentwicklung:</dt>
										<dd>
											<dl id="person">
												<dt>Spenden:</dt>
												<dd>xtcModified ist ein OpenSource-Projekt, trotzden stecken wir jede Menge Arbeit und Freizeit in dieses Projekt und würden uns daher über eine Spende als kleine Anerkennung freuen.</dd>
												<dt>&nbsp;</dt><dd>&nbsp;</dd>
												<dt>&nbsp;</dt>
												<dd>
													<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=6302373" target="_blank"><img src="https://www.paypal.com/de_DE/DE/i/btn/btn_donateCC_LG.gif" alt="Unterstützen Sie dieses Projekt mit Ihrer Spende" border="0"></a>
												</dd>
											</dl>
										</dd>
									</dl>
								</td>
							</tr>
						</table>
						<hr />
						<dl>
							<dt style="color: #d68000; font-weight: bold;">Die Shopsoftware basiert auf:</dt>
							<dd>
								<ul style="list-style: none; padding-left: 0px;">
									<li><?php echo '&copy;'.date('Y').'&nbsp;'; echo PROJECT_VERSION; ?> | http://www.xtc-modified.org/</li>
									<li>&copy;2006 xt:Commerce V3.0.4 SP2.1 | http://www.xtcommerce.de/</li>
									<li>&copy;2003 neXTCommerce</li>
									<li>&copy;2002-2003 osCommerce (Milestone2) by Harald Ponce de Leon | http://www.oscommerce.com/</li>
									<li>&copy;2000-2001 The Exchange Project by Harald Ponce de Leon | http://www.oscommerce.com/</li>
								</ul>
							</dd>
						</dl>
					</div>
				  </td></tr></table>
				</td>
				<!-- body_text_eof //-->
			</tr>
		</table>
		<!-- body_eof //-->

		<!-- footer //-->
		<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
		<!-- footer_eof //-->
	</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>