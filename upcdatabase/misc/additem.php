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

    $FileInfo: additem.php - Last Update: 02/13/2012 Ver. 2.2.5 RC 1 - Author: cooldude2k $
*/
$File3Name = basename($_SERVER['SCRIPT_NAME']);
if ($File3Name=="additem.php"||$File3Name=="/additem.php") {
	chdir("../");
	require("./upc.php");
	exit(); }

if($_GET['act']=="add"&&isset($_POST['upc'])&&
	 isset($_POST['description'])&&isset($_POST['sizeweight'])) {
$_POST['description'] = trim($_POST['description']);
$_POST['description'] = remove_spaces($_POST['description']);
$_POST['sizeweight'] = trim($_POST['sizeweight']);
$_POST['sizeweight'] = remove_spaces($_POST['sizeweight']);
if(strlen($_POST['description'])>150) {
header("Location: ".$website_url.$url_file."?act=add&upc=".$_GET['upc']); exit(); }
if(strlen($_POST['sizeweight'])>30) {
header("Location: ".$website_url.$url_file."?act=add&upc=".$_GET['upc']); exit(); }
if($_POST['description']==""||$_POST['description']==NULL) {
header("Location: ".$website_url.$url_file."?act=add&upc=".$_GET['upc']); exit(); }
if($_POST['sizeweight']==""||$_POST['sizeweight']==NULL) {
header("Location: ".$website_url.$url_file."?act=add&upc=".$_GET['upc']); exit(); }
$findusrinfo = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."members\" WHERE name='".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."';"); 
$getuserinfo = sql_fetch_assoc($findusrinfo); 
$newnumitems = $getuserinfo['numitems'];
$newnumpending = $getuserinfo['numpending'];
if($usersiteinfo['admin']=="yes") {
$newnumitems = $getuserinfo['numitems'] + 1; }
if($usersiteinfo['admin']=="no"&&$validate_items===true&&$usersiteinfo['validateitems']=="yes") {
$newnumpending = $getuserinfo['numpending'] + 1; }
if($usersiteinfo['admin']=="no"&&$validate_items===true&&$usersiteinfo['validateitems']=="no") {
$newnumitems = $getuserinfo['numitems'] + 1; }
if($usersiteinfo['admin']=="no"&&$validate_items===false) {
$newnumitems = $getuserinfo['numitems'] + 1; }
sqlite3_query($slite3, "UPDATE \"".$table_prefix."members\" SET \"lastactive\"='".time()."',\"numitems\"=".$newnumitems.", \"numpending\"=".$newnumpending.",\"ip\"='".$usersip."' WHERE \"name\"='".$_COOKIE['MemberName']."' AND \"id\"='".$_COOKIE['MemberID']."';");
$itemvalidated = "no";
if($_COOKIE['MemberID']==1) { $itemvalidated = "yes"; }
if($usersiteinfo['admin']=="yes") { $itemvalidated = "yes"; }
if($usersiteinfo['admin']=="no"&&$_COOKIE['MemberID']>1&&$validate_items===false) { $itemvalidated = "yes"; }
if($usersiteinfo['admin']=="no"&&$_COOKIE['MemberID']>1&&$validate_items===true&&
	$usersiteinfo['validateitems']=="yes") { $itemvalidated = "no"; }
if($usersiteinfo['admin']=="no"&&$_COOKIE['MemberID']>1&&$validate_items===true&&
	$usersiteinfo['validateitems']=="no") { $itemvalidated = "yes"; }
if($usersiteinfo['admin']=="yes") {
sqlite3_query($slite3, "INSERT INTO \"".$table_prefix."items\" (\"upc\", \"description\", \"sizeweight\", \"validated\", \"delrequest\", \"userid\", \"username\", \"timestamp\", \"lastupdate\", \"edituserid\", \"editname\", \"ip\", \"editip\") VALUES ('".sqlite3_escape_string($slite3, $_POST['upc'])."', '".sqlite3_escape_string($slite3, $_POST['description'])."', '".sqlite3_escape_string($slite3, $_POST['sizeweight'])."', '".sqlite3_escape_string($slite3, $itemvalidated)."', 'no', ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', ".time().", ".time().", ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', '".sqlite3_escape_string($slite3, $usersip)."', '".sqlite3_escape_string($slite3, $usersip)."');"); }
if($usersiteinfo['admin']=="no"&&$validate_items===false) {
sqlite3_query($slite3, "INSERT INTO \"".$table_prefix."items\" (\"upc\", \"description\", \"sizeweight\", \"validated\", \"delrequest\", \"userid\", \"username\", \"timestamp\", \"lastupdate\", \"edituserid\", \"editname\", \"ip\", \"editip\") VALUES ('".sqlite3_escape_string($slite3, $_POST['upc'])."', '".sqlite3_escape_string($slite3, $_POST['description'])."', '".sqlite3_escape_string($slite3, $_POST['sizeweight'])."', '".sqlite3_escape_string($slite3, $itemvalidated)."', 'no', ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', ".time().", ".time().", ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', '".sqlite3_escape_string($slite3, $usersip)."', '".sqlite3_escape_string($slite3, $usersip)."');"); }
if($usersiteinfo['admin']=="no"&&$validate_items===true&&$usersiteinfo['validateitems']=="yes") {
sqlite3_query($slite3, "INSERT INTO \"".$table_prefix."pending\" (\"upc\", \"description\", \"sizeweight\", \"validated\", \"delrequest\", \"userid\", \"username\", \"timestamp\", \"lastupdate\", \"ip\") VALUES ('".sqlite3_escape_string($slite3, $_POST['upc'])."', '".sqlite3_escape_string($slite3, $_POST['description'])."', '".sqlite3_escape_string($slite3, $_POST['sizeweight'])."', '".sqlite3_escape_string($slite3, $itemvalidated)."', 'no', ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', ".time().", ".time().", '".sqlite3_escape_string($slite3, $usersip)."');"); }
if($usersiteinfo['admin']=="no"&&$validate_items===true&&$usersiteinfo['validateitems']=="no") {
sqlite3_query($slite3, "INSERT INTO \"".$table_prefix."items\" (\"upc\", \"description\", \"sizeweight\", \"validated\", \"delrequest\", \"userid\", \"username\", \"timestamp\", \"lastupdate\", \"edituserid\", \"editname\", \"ip\", \"editip\") VALUES ('".sqlite3_escape_string($slite3, $_POST['upc'])."', '".sqlite3_escape_string($slite3, $_POST['description'])."', '".sqlite3_escape_string($slite3, $_POST['sizeweight'])."', '".sqlite3_escape_string($slite3, $itemvalidated)."', 'no', ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', ".time().", ".time().", ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', '".sqlite3_escape_string($slite3, $usersip)."', '".sqlite3_escape_string($slite3, $usersip)."');"); }
$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup&upc=".$_POST['upc']); exit(); }
if($_GET['act']=="add"&&isset($_POST['upc'])) { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<title> <?php echo $sitename; ?>: Add New Entry </title>
<?php echo $metatags; ?>
 </head>
 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Add New Entry</h2>
   <table>
   <?php if($upce!==NULL&&validate_upce($upce)===true) { ?>
   <tr><td>UPC-E</td><td width="50"></td><td><img src="<?php echo $website_url.$barcode_file; ?>?act=upce&amp;upc=<?php echo $upce; ?>" alt="<?php echo $upce; ?>" title="<?php echo $upce; ?>" /></td></tr>
   <?php } if($upca!==NULL&&validate_upca($upca)===true) { ?>
   <tr><td>UPC-A</td><td width="50"></td><td><img src="<?php echo $website_url.$barcode_file; ?>?act=upca&amp;upc=<?php echo $upca; ?>" alt="<?php echo $upca; ?>" title="<?php echo $upca; ?>" /></td></tr>
   <?php } if($ean13!==NULL&&validate_ean13($ean13)===true) { ?>
   <tr><td>EAN/UCC-13</td><td width="50"></td><td><img src="<?php echo $website_url.$barcode_file; ?>?act=ean13&amp;upc=<?php echo $ean13; ?>" alt="<?php echo $ean13; ?>" title="<?php echo $ean13; ?>" /></td></tr>
   <?php } ?>
   </table>
   <div><br /></div>
   <form action="<?php echo $website_url.$url_file; ?>?act=add" method="post">
    <table>
    <tr><td style="text-align: center;">Description: <input type="text" name="description" size="50" maxlength="150" /></td></tr>
    <tr><td style="text-align: center;">Size/Weight: <input type="text" name="sizeweight" size="30" maxlength="30" /></td></tr>
   </table>
   <input type="hidden" name="upc" value="<?php echo $_POST['upc']; ?>" />
   <div><br /><input type="submit" value="Save New Entry" /> <input type="reset" value="Clear" /></div>
   </form>
  </center>
 </body>
</html>
<?php } if($_GET['act']=="check"||$_GET['act']=="checkdigit") { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
<title> <?php echo $sitename; ?>: Check Digit Calculator </title>
<?php echo $metatags; ?>
 </head>
 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Check Digit Calculator</h2>
   <form method="post" action="<?php echo $website_url.$url_file."?act=checkdigit"; ?>">
   <b>EAN/UCC</b>: <input type="text" name="checkupc" size="15" maxlength="12" /><div><br /></div>
   <div><input type="submit" value="Calculate Check Digit" /></div>
   </form>
   <div><br /></div>
   <?php if(isset($_POST['checkupc'])&&is_numeric($_POST['checkupc'])&&
   (strlen($_POST['checkupc'])==7||strlen($_POST['checkupc'])==11||strlen($_POST['checkupc'])==12)) { 
   if(strlen($_POST['checkupc'])==7) {
   $check_upce = fix_upce_checksum($_POST['checkupc']);
   $check_upca = convert_upce_to_upca($check_upce);
   $check_ean13 = convert_upca_to_ean13($check_upca); }
   if(strlen($_POST['checkupc'])==11) {
   $check_upca = fix_upca_checksum($_POST['checkupc']);
   $check_upce = convert_upca_to_upce($check_upca);
   $check_ean13 = convert_upca_to_ean13($check_upca); }
   if(strlen($_POST['checkupc'])==12) {
   $check_ean13 = fix_ean13_checksum($_POST['checkupc']);
   $check_upca = convert_ean13_to_upca($check_ean13);
   $check_upce = convert_upca_to_upce($check_upca); }
   ?>
   <table>
   <?php if($check_ean13!==NULL&&validate_ean13($check_ean13)===true) { ?>
   <tr><td>EAN/UCC-13:</td><td><?php echo $check_ean13; ?></td></tr>
   <?php } if($check_upca!==NULL&&validate_upca($check_upca)===true) { ?>
   <tr><td>UPC-A:</td><td><?php echo $check_upca; ?></td></tr>
   <?php } if($check_upce!==NULL&&validate_upce($check_upce)===true) { ?>
   <tr><td>UPC-E:</td><td><?php echo $check_upce; ?></td></tr>
   <?php } ?>
   <tr><td colspan="2"><a href="<?php echo $website_url.$url_file."?act=lookup&amp;upc=".$check_ean13; ?>">Click here</a> to look up this UPC in the database.</td></tr>
   </table>
   <?php } ?>
  </center>
 </body>
</html>
<?php } ?>