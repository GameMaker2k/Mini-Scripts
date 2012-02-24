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

    $FileInfo: adminmem.php - Last Update: 02/13/2012 Ver. 2.2.5 RC 1 - Author: cooldude2k $
*/
$File3Name = basename($_SERVER['SCRIPT_NAME']);
if ($File3Name=="adminmem.php"||$File3Name=="/adminmem.php") {
	chdir("../");
	require("./upc.php");
	exit(); }

if($_GET['act']=="deletemember"&&isset($_GET['id'])&&$_GET['id']>1) {
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."members\" WHERE \"id\"=".$_GET['id'].";");
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) {
$delupc = sqlite3_query($slite3, "DELETE FROM \"".$table_prefix."members\" WHERE \"id\"=".$_GET['id'].";"); 
sqlite3_query($slite3, "UPDATE \"".$table_prefix."items\" SET \"userid\"=0 WHERE \"userid\"=".$_GET['id'].";");
sqlite3_query($slite3, "UPDATE \"".$table_prefix."items\" SET \"edituserid\"=0 WHERE \"edituserid\"='".$_GET['id']."';");
sqlite3_query($slite3, "UPDATE \"".$table_prefix."pending\" SET \"userid\"=0 WHERE \"userid\"=".$_GET['id'].";"); 
sqlite3_query($slite3, "UPDATE \"".$table_prefix."modupc\" SET \"userid\"=0 WHERE \"userid\"=".$_GET['id'].";"); } }
if($_GET['act']=="deletemember") { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<title> <?php echo $sitename; ?>: AdminCP : Delete Member </title>
<?php echo $metatags; ?>
 </head>
 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Delete Member</h2>
   <?php
   $findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."members\" ORDER BY \"id\" DESC;"); 
   $numupc = sql_fetch_assoc($findupc);
   $numrows = $numupc['COUNT'];
   if($numrows>0) {
   $maxpage = $_GET['page'] * 20;
   if($maxpage>$numrows) { $maxpage = $numrows; }
   $pagestart = $maxpage - 20;
   if($pagestart<0) { $pagestart = 0; }
   $pagestartshow = $pagestart + 1;
   $findupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."members\" ORDER BY \"id\" ASC LIMIT ".$pagestart.", ".$maxpage.";"); 
   if($maxpage>20&&$_GET['page']>1) {
   $backpage = $_GET['page'] - 1;
   echo "<a href=\"".$website_url.$url_admin_file."?act=deletemember&amp;page=".$backpage."\">Prev</a> --\n"; }
   echo $numrows." members, displaying ".$pagestartshow." through ".$maxpage;
   if($pagestart<($numrows - 20)) {
   $nextpage = $_GET['page'] + 1;
   echo "\n-- <a href=\"".$website_url.$url_admin_file."?act=deletemember&amp;page=".$nextpage."\">Next</a>"; }
   ?>
   <div><br /></div>
   <table class="list">
   <tr><th>Delete Member</th><th>Email</th><th>Size/Weight</th><th>Last Mod</th></tr>
   <?php
   while ($meminfo = sql_fetch_assoc($findupc)) { ?>
   <tr valign="top">
   <td><a href="<?php echo $website_url.$url_admin_file; ?>?act=deletemember&amp;id=<?php echo $meminfo['id']; ?>&amp;page=1" onclick="if(!confirm('Are you sure you want to delete member <?php echo htmlspecialchars($meminfo['name'], ENT_HTML401, "UTF-8"); ?>?')) { return false; }"><?php echo htmlspecialchars($meminfo['name'], ENT_HTML401, "UTF-8"); ?></a></td>
   <td><?php echo htmlspecialchars($meminfo['email'], ENT_HTML401, "UTF-8"); ?></td>
   <td nowrap="nowrap"><?php echo $meminfo['ip']; ?></td>
   <td nowrap="nowrap"><?php echo date("j M Y, g:i A T", $meminfo['lastactive']); ?></td>
   </tr>
   <?php } echo "   </table>   <div><br /></div>"; }
   if($numrows>0) {
   if($maxpage>20&&$_GET['page']>1) {
   $backpage = $_GET['page'] - 1;
   echo "<a href=\"".$website_url.$url_admin_file."?act=deletemember&amp;page=".$backpage."\">Prev</a> --\n"; }
   echo $numrows." members, displaying ".$pagestartshow." through ".$maxpage;
   if($pagestart<($numrows - 20)) {
   $nextpage = $_GET['page'] + 1;
   echo "\n-- <a href=\"".$website_url.$url_admin_file."?act=deletemember&amp;page=".$nextpage."\">Next</a>"; } }
   ?>
  </center>
 </body>
</html>
<?php } if($_GET['act']=="validatemember"&&isset($_GET['id'])&&$_GET['id']>1) {
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."members\" WHERE \"id\"=".$_GET['id']." AND \"validated\"='no';");
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) { 
sqlite3_query($slite3, "UPDATE \"".$table_prefix."members\" SET \"validated\"='yes' WHERE \"id\"=".$_GET['id'].";"); } }
if($_GET['act']=="validatemember") { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<title> <?php echo $sitename; ?>: AdminCP : Validate Member </title>
<?php echo $metatags; ?>
 </head>
 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Validate Member</h2>
   <?php
   $findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."members\" WHERE \"validated\"='no' ORDER BY \"id\" DESC;"); 
   $numupc = sql_fetch_assoc($findupc);
   $numrows = $numupc['COUNT'];
   if($numrows>0) {
   $maxpage = $_GET['page'] * 20;
   if($maxpage>$numrows) { $maxpage = $numrows; }
   $pagestart = $maxpage - 20;
   if($pagestart<0) { $pagestart = 0; }
   $pagestartshow = $pagestart + 1;
   $findupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."members\" WHERE \"validated\"='no' ORDER BY \"id\" ASC LIMIT ".$pagestart.", ".$maxpage.";"); 
   if($maxpage>20&&$_GET['page']>1) {
   $backpage = $_GET['page'] - 1;
   echo "<a href=\"".$website_url.$url_admin_file."?act=validatemember&amp;page=".$backpage."\">Prev</a> --\n"; }
   echo $numrows." members, displaying ".$pagestartshow." through ".$maxpage;
   if($pagestart<($numrows - 20)) {
   $nextpage = $_GET['page'] + 1;
   echo "\n-- <a href=\"".$website_url.$url_admin_file."?act=validatemember&amp;page=".$nextpage."\">Next</a>"; }
   ?>
   <div><br /></div>
   <table class="list">
   <tr><th>Validate Member</th><th>Email</th><th>Size/Weight</th><th>Last Mod</th></tr>
   <?php
   while ($meminfo = sql_fetch_assoc($findupc)) { ?>
   <tr valign="top">
   <td><a href="<?php echo $website_url.$url_admin_file; ?>?act=validatemember&amp;id=<?php echo $meminfo['id']; ?>&amp;page=1"><?php echo htmlspecialchars($meminfo['name'], ENT_HTML401, "UTF-8"); ?></a></td>
   <td><?php echo htmlspecialchars($meminfo['email'], ENT_HTML401, "UTF-8"); ?></td>
   <td nowrap="nowrap"><?php echo $meminfo['ip']; ?></td>
   <td nowrap="nowrap"><?php echo date("j M Y, g:i A T", $meminfo['lastactive']); ?></td>
   </tr>
   <?php } echo "   </table>   <div><br /></div>"; }
   if($numrows>0) {
   if($maxpage>20&&$_GET['page']>1) {
   $backpage = $_GET['page'] - 1;
   echo "<a href=\"".$website_url.$url_admin_file."?act=validatemember&amp;page=".$backpage."\">Prev</a> --\n"; }
   echo $numrows." members, displaying ".$pagestartshow." through ".$maxpage;
   if($pagestart<($numrows - 20)) {
   $nextpage = $_GET['page'] + 1;
   echo "\n-- <a href=\"".$website_url.$url_admin_file."?act=validatemember&amp;page=".$nextpage."\">Next</a>"; } }
   ?>
  </center>
 </body>
</html>
<?php } if($_GET['act']=="editmember") { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<title> <?php echo $sitename; ?>: AdminCP : Edit Member </title>
<?php echo $metatags; ?>
 </head>
 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Edit Member</h2>
  </center>
 </body>
</html>
<?php } ?>