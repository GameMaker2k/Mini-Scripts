<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.

    Copyright 2011-2012 Cool Dude 2k - http://idb.berlios.de/
    Copyright 2011-2012 Game Maker 2k - http://intdb.sourceforge.net/
    Copyright 2011-2012 Kazuki Przyborowski - https://github.com/KazukiPrzyborowski

    $FileInfo: listitem.php - Last Update: 02/13/2012 Ver. 2.2.5 RC 1 - Author: cooldude2k $
*/
$File3Name = basename($_SERVER['SCRIPT_NAME']);
if ($File3Name=="listitem.php"||$File3Name=="/listitem.php") {
	chdir("../");
	require("./upc.php");
	exit(); }

if($_GET['act']=="latest") { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<title> <?php echo $sitename; ?>: Latest Submissions </title>
<?php echo $metatags; ?>
 </head>
 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Latest Submissions</h2>
   <?php
   if(!isset($_GET['page'])) { $_GET['page'] = 1; }
   if(!is_numeric($_GET['page'])) { $_GET['page'] = 1; }
   $findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."items\" ORDER BY \"lastupdate\" DESC;"); 
   $numupc = sql_fetch_assoc($findupc);
   $numrows = $numupc['COUNT'];
   if($numrows>0) {
   $maxpage = $_GET['page'] * 20;
   if($maxpage>$numrows) { $maxpage = $numrows; }
   $pagestart = $maxpage - 20;
   if($pagestart<0) { $pagestart = 0; }
   $pagestartshow = $pagestart + 1;
   $findupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" ORDER BY \"lastupdate\" DESC LIMIT ".$pagestart.", ".$maxpage.";"); 
   if($maxpage>20&&$_GET['page']>1) {
   $backpage = $_GET['page'] - 1;
   echo "<a href=\"".$website_url.$url_file."?act=latest&amp;page=".$backpage."\">Prev</a> --\n"; }
   echo $numrows." items, displaying ".$pagestartshow." through ".$maxpage;
   if($pagestart<($numrows - 20)) {
   $nextpage = $_GET['page'] + 1;
   echo "\n-- <a href=\"".$website_url.$url_file."?act=latest&amp;page=".$nextpage."\">Next</a>"; }
   ?>
   <div><br /></div>
   <table class="list">
   <tr><th>EAN/UCC</th><th>Description</th><th>Size/Weight</th><th>Last Mod</th></tr>
   <?php
   while ($upcinfo = sql_fetch_assoc($findupc)) {
   ?>
   <tr valign="top">
   <td><a href="<?php echo $website_url.$url_file; ?>?act=lookup&amp;upc=<?php echo $upcinfo['upc']; ?>"><?php echo $upcinfo['upc']; ?></a></td>
   <td><?php echo htmlspecialchars($upcinfo['description'], ENT_HTML401, "UTF-8"); ?></td>
   <td nowrap="nowrap"><?php echo htmlspecialchars($upcinfo['sizeweight'], ENT_HTML401, "UTF-8"); ?></td>
   <td nowrap="nowrap"><?php echo date("j M Y, g:i A T", $upcinfo['lastupdate']); ?></td>
   </tr>
   <?php } echo "   </table>   <div><br /></div>"; }
   if($numrows>0) {
   if($maxpage>20&&$_GET['page']>1) {
   $backpage = $_GET['page'] - 1;
   echo "<a href=\"".$website_url.$url_file."?act=latest&amp;page=".$backpage."\">Prev</a> --\n"; }
   echo $numrows." items, displaying ".$pagestartshow." through ".$maxpage;
   if($pagestart<($numrows - 20)) {
   $nextpage = $_GET['page'] + 1;
   echo "\n-- <a href=\"".$website_url.$url_file."?act=latest&amp;page=".$nextpage."\">Next</a>"; } }
   ?>
   <div><br /></div>
   <form action="<?php echo $website_url.$url_file; ?>?act=lookup" method="get">
    <input type="hidden" name="act" value="lookup" />
    <table>
    <tr><td style="text-align: center;"><input type="text" name="upc" size="16" maxlength="13" value="<?php echo $lookupupc; ?>" /> <input type="submit" value="Look Up UPC" /></td></tr>
   </table>
   </form>
  </center>
 </body>
</html>
<?php } if(isset($_GET['upc'])&&($_GET['act']=="neighbor"||$_GET['act']=="neighbors")) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<title> <?php echo $sitename; ?>: Item Neighbors </title>
<?php echo $metatags; ?>
 </head>
 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Item Neighbors</h2>
   <?php
   if(!isset($_GET['page'])) { $_GET['page'] = 1; }
   if(!is_numeric($_GET['page'])) { $_GET['page'] = 1; }
   preg_match("/^(\d{7})/", $_GET['upc'], $fix_matches); 
   $findprefix = $fix_matches[1];
   $findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."items\" WHERE \"upc\" LIKE '".$findprefix."%';"); 
   $numupc = sql_fetch_assoc($findupc);
   $numrows = $numupc['COUNT'];
   if($numrows>0) {
   $maxpage = $_GET['page'] * 20;
   if($maxpage>$numrows) { $maxpage = $numrows; }
   $pagestart = $maxpage - 20;
   if($pagestart<0) { $pagestart = 0; }
   $pagestartshow = $pagestart + 1;
   $findupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" WHERE \"upc\" LIKE '".$findprefix."%';"); 
   if($maxpage>20&&$_GET['page']>1) {
   $backpage = $_GET['page'] - 1;
   echo "<a href=\"".$website_url.$url_file."?act=latest&amp;page=".$backpage."\">Prev</a> --\n"; }
   echo $numrows." items, displaying ".$pagestartshow." through ".$maxpage;
   if($pagestart<($numrows - 20)) {
   $nextpage = $_GET['page'] + 1;
   echo "\n-- <a href=\"".$website_url.$url_file."?act=latest&amp;page=".$nextpage."\">Next</a>"; }
   ?>
   <div><br /></div>
   <table class="list">
   <tr><th>EAN/UCC</th><th>Description</th><th>Size/Weight</th><th>Last Mod</th></tr>
   <?php
   while ($upcinfo = sql_fetch_assoc($findupc)) {
   ?>
   <tr valign="top">
   <td><a href="<?php echo $website_url.$url_file; ?>?act=lookup&amp;upc=<?php echo $upcinfo['upc']; ?>"><?php echo $upcinfo['upc']; ?></a></td>
   <td><?php echo htmlspecialchars($upcinfo['description'], ENT_HTML401, "UTF-8"); ?></td>
   <td nowrap="nowrap"><?php echo htmlspecialchars($upcinfo['sizeweight'], ENT_HTML401, "UTF-8"); ?></td>
   <td nowrap="nowrap"><?php echo date("j M Y, g:i A T", $upcinfo['lastupdate']); ?></td>
   </tr>
   <?php } echo "   </table>   <div><br /></div>"; }
   if($numrows>0) {
   if($maxpage>20&&$_GET['page']>1) {
   $backpage = $_GET['page'] - 1;
   echo "<a href=\"".$website_url.$url_file."?act=latest&amp;page=".$backpage."\">Prev</a> --\n"; }
   echo $numrows." items, displaying ".$pagestartshow." through ".$maxpage;
   if($pagestart<($numrows - 20)) {
   $nextpage = $_GET['page'] + 1;
   echo "\n-- <a href=\"".$website_url.$url_file."?act=latest&amp;page=".$nextpage."\">Next</a>"; } }
   ?>
   <div><br /></div>
   <form action="<?php echo $website_url.$url_file; ?>?act=lookup" method="get">
    <input type="hidden" name="act" value="lookup" />
    <table>
    <tr><td style="text-align: center;"><input type="text" name="upc" size="16" maxlength="13" value="<?php echo $lookupupc; ?>" /> <input type="submit" value="Look Up UPC" /></td></tr>
   </table>
   </form>
  </center>
 </body>
</html>
<?php } ?>