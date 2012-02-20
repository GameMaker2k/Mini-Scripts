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

    $FileInfo: upc.php - Last Update: 02/13/2012 Ver. 2.2.5 RC 1 - Author: cooldude2k $
*/
$website_url = "http://localhost/upcdatabase/";
$url_file = "upc.php";
$url_admin_file = "admin.php";
$sdb_file = "upcdatabase.sdb";
$usehashtype = "sha256";
$validate_items = true;
$validate_members = true;
$sitename = "UPC Database";
$appname = htmlspecialchars("UPC/EAN Barcode Generator");
$appmakerurl = "https://github.com/KazukiPrzyborowski/UPC-A-EAN-13-Maker";
$appmaker = htmlspecialchars("Game Maker 2k");
$appver = array(2,2,5,"RC 1");
$upcdatabase = "http://www.upcdatabase.com/item/%s";
@ob_start();
@ini_set("date.timezone","UTC");
@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
if(function_exists("date_default_timezone_set")) { 
	@date_default_timezone_set("UTC"); }

$usersip = $_SERVER['REMOTE_ADDR'];
$basecheck = parse_url($iDBURLCHK);
$basedir = $basecheck['path'];
$cbasedir = $basedir;
$cookieDomain = $basecheck['host'];
$metatags = "  <meta name=\"generator\" content=\"".$sitename."\" />\n  <meta name=\"author\" content=\"".$siteauthor."\" />\n  <meta name=\"keywords\" content=\"".$sitekeywords."\" />\n  <meta name=\"description\" content=\"".$sitedescription."\" />\n";

function sqlite3_open($filename, $mode = 0666) {
   $handle = new SQLite3($filename);
   return $handle; }

function sqlite3_close($dbhandle) {
   $dbhandle->close();
   return true; }

function sqlite3_escape_string($dbhandle, $string) {
   $string = $dbhandle->escapeString($string);
   return $string; }

function sqlite3_query($dbhandle, $query) {
   $results = $dbhandle->query($query);
   return $results; }

function sqlite3_fetch_array($result,$result_type=SQLITE3_BOTH) {
	$row = $result->fetchArray($result_type);
	return $row; }

function sql_fetch_assoc($result) {
	$row = $result->fetchArray(SQLITE3_ASSOC);
	return $row; }

function sqlite3_last_insert_rowid($dbhandle) {
	$rowid = $dbhandle->lastInsertRowID();
	return $rowid; }

function sqlite3_libversion($dbhandle) {
	$dbversion = $dbhandle->version();
	return $dbversion['versionString']; }

function version_info($proname,$subver,$ver,$supver,$reltype,$svnver,$showsvn) {
	$return_var = $proname." ".$reltype." ".$subver.".".$ver.".".$supver;
	if($showsvn==false) { $showsvn = null; }
	if($showsvn==true) { $return_var .= " SVN ".$svnver; }
	if($showsvn!=true&&$showsvn!=null) { $return_var .= " ".$showsvn." ".$svnver; }
	return $return_var; }
$appversion = version_info($appname,$appver[0],$appver[1],$appver[2],$appver[3]." Ver.",null,false);
require("./functions.php");

$slite3 = sqlite3_open($sdb_file);
$tablecheck1 = @sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_members\""); 
if($tablecheck1===false) {
sqlite3_query($slite3, "PRAGMA auto_vacuum = 1;");
sqlite3_query($slite3, "PRAGMA encoding = \"UTF-8\";");
$query = "CREATE TABLE \"upcdatabase_members\" (\n".
"  \"id\" INTEGER PRIMARY KEY NOT NULL,\n".
"  \"name\" VARCHAR(150) UNIQUE NOT NULL default '',\n".
"  \"password\" VARCHAR(250) NOT NULL default '',\n".
"  \"hashtype\" VARCHAR(50) NOT NULL default '',\n".
"  \"email\" VARCHAR(256) UNIQUE NOT NULL default '',\n".
"  \"timestamp\" INTEGER NOT NULL default '0',\n".
"  \"lastactive\" INTEGER NOT NULL default '0',\n".
"  \"validated\" VARCHAR(20) NOT NULL default '',\n".
"  \"numitems\" INTEGER NOT NULL default '0',\n".
"  \"numpending\" INTEGER NOT NULL default '0',\n".
"  \"admin\" VARCHAR(20) NOT NULL default '',\n".
"  \"ip\" VARCHAR(50) NOT NULL default '',\n".
"  \"salt\" VARCHAR(50) NOT NULL default ''\n".
");";
sqlite3_query($slite3, $query); 
sqlite3_query($slite3, "VACUUM;"); }
$tablecheck2 = @sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_items\""); 
if($tablecheck2===false) {
sqlite3_query($slite3, "PRAGMA auto_vacuum = 1;");
sqlite3_query($slite3, "PRAGMA encoding = \"UTF-8\";");
$query = "CREATE TABLE \"upcdatabase_items\" (\n".
"  \"id\" INTEGER PRIMARY KEY NOT NULL,\n".
"  \"upc\" TEXT UNIQUE NOT NULL,\n".
"  \"description\" TEXT NOT NULL,\n".
"  \"sizeweight\" TEXT NOT NULL,\n".
"  \"validated\" VARCHAR(20) NOT NULL default '',\n".
"  \"delrequest\" VARCHAR(20) NOT NULL default '',\n".
"  \"userid\" INTEGER NOT NULL default '0',\n".
"  \"username\" VARCHAR(150) NOT NULL default '',\n".
"  \"timestamp\" INTEGER NOT NULL default '0',\n".
"  \"lastupdate\" INTEGER NOT NULL default '0',\n".
"  \"edituserid\" INTEGER NOT NULL default '0',\n".
"  \"editname\" VARCHAR(150) NOT NULL default '',\n".
"  \"ip\" VARCHAR(50) NOT NULL default '',\n".
"  \"editip\" VARCHAR(50) NOT NULL default ''\n".
");";
sqlite3_query($slite3, $query); 
sqlite3_query($slite3, "VACUUM;"); }
$tablecheck3 = @sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_pending\""); 
if($tablecheck3===false) {
sqlite3_query($slite3, "PRAGMA auto_vacuum = 1;");
sqlite3_query($slite3, "PRAGMA encoding = \"UTF-8\";");
$query = "CREATE TABLE \"upcdatabase_pending\" (\n".
"  \"id\" INTEGER PRIMARY KEY NOT NULL,\n".
"  \"upc\" TEXT UNIQUE NOT NULL,\n".
"  \"description\" TEXT NOT NULL,\n".
"  \"sizeweight\" TEXT NOT NULL,\n".
"  \"validated\" VARCHAR(20) NOT NULL default '',\n".
"  \"delrequest\" VARCHAR(20) NOT NULL default '',\n".
"  \"userid\" INTEGER NOT NULL default '0',\n".
"  \"username\" VARCHAR(150) NOT NULL default '',\n".
"  \"timestamp\" INTEGER NOT NULL default '0',\n".
"  \"lastupdate\" INTEGER NOT NULL default '0',\n".
"  \"ip\" VARCHAR(50) NOT NULL default ''\n".
");";
sqlite3_query($slite3, $query); 
sqlite3_query($slite3, "VACUUM;"); }
$tablecheck3 = @sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_modupc\""); 
if($tablecheck3===false) {
sqlite3_query($slite3, "PRAGMA auto_vacuum = 1;");
sqlite3_query($slite3, "PRAGMA encoding = \"UTF-8\";");
$query = "CREATE TABLE \"upcdatabase_modupc\" (\n".
"  \"id\" INTEGER PRIMARY KEY NOT NULL,\n".
"  \"upc\" TEXT UNIQUE NOT NULL,\n".
"  \"description\" TEXT NOT NULL,\n".
"  \"sizeweight\" TEXT NOT NULL,\n".
"  \"validated\" VARCHAR(20) NOT NULL default '',\n".
"  \"delrequest\" VARCHAR(20) NOT NULL default '',\n".
"  \"userid\" INTEGER NOT NULL default '0',\n".
"  \"username\" VARCHAR(150) NOT NULL default '',\n".
"  \"timestamp\" INTEGER NOT NULL default '0',\n".
"  \"lastupdate\" INTEGER NOT NULL default '0',\n".
"  \"ip\" VARCHAR(50) NOT NULL default ''\n".
");";
sqlite3_query($slite3, $query); 
sqlite3_query($slite3, "VACUUM;"); }

if(isset($_COOKIE['MemberName'])&&!isset($_COOKIE['MemberID'])&&!isset($_COOKIE['SessPass'])) {
	unset($_COOKIE['MemberName']); 
	setcookie("MemberName", NULL, -1, $cbasedir, $cookieDomain); }
if(!isset($_COOKIE['MemberName'])&&isset($_COOKIE['MemberID'])&&!isset($_COOKIE['SessPass'])) {
	unset($_COOKIE['MemberID']); 
	setcookie("MemberID", NULL, -1, $cbasedir, $cookieDomain); }
if(!isset($_COOKIE['MemberName'])&&!isset($_COOKIE['MemberID'])&&isset($_COOKIE['SessPass'])) {
	unset($_COOKIE['SessPass']); 
	setcookie("SessPass", NULL, -1, $cbasedir, $cookieDomain); }
if(isset($_COOKIE['MemberName'])&&isset($_COOKIE['MemberID'])&&isset($_COOKIE['SessPass'])) {
	$findme = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"upcdatabase_members\" WHERE name='".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."';");
	$numfindme = sql_fetch_assoc($findme);
	$numfmrows = $numfindme['COUNT'];
	if($numfmrows<1) {
	unset($_COOKIE['MemberName']); 
	setcookie("MemberName", NULL, -1, $cbasedir, $cookieDomain);
	unset($_COOKIE['MemberID']); 
	setcookie("MemberID", NULL, -1, $cbasedir, $cookieDomain);
	unset($_COOKIE['SessPass']); 
	setcookie("SessPass", NULL, -1, $cbasedir, $cookieDomain); }
	if($numfmrows>0) {
	$findme = sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_members\" WHERE name='".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."';"); 
	$userinfo = sql_fetch_assoc($findme); $usersiteinfo = $userinfo;
	if($userinfo['password']!=$_COOKIE['SessPass']) {
	unset($_COOKIE['MemberName']); 
	setcookie("MemberName", NULL, -1, $cbasedir, $cookieDomain);
	unset($_COOKIE['MemberID']); 
	setcookie("MemberID", NULL, -1, $cbasedir, $cookieDomain);
	unset($_COOKIE['SessPass']); 
	setcookie("SessPass", NULL, -1, $cbasedir, $cookieDomain); } } }
$adminlink = null;
if($usersiteinfo['admin']=="yes") { $adminlink = " | <a href=\"".$website_url.$url_admin_file."\">AdminCP</a>"; }
if($usersiteinfo['admin']=="yes") { $usersiteinfo['validated'] = "yes"; }
$navbar = "<div><a href=\"".$website_url.$url_file."?act=lookup\">Index Page</a> | ";
if(isset($_COOKIE['MemberName'])) { 
	$navbar = $navbar."Welcome: ".$_COOKIE['MemberName']." | <a href=\"".$website_url.$url_file."?act=logout\">Logout</a>".$adminlink."<br />"; }
if(!isset($_COOKIE['MemberName'])) { 
	$navbar = $navbar."Welcome: Guest | <a href=\"".$website_url.$url_file."?act=join\">Join</a> | <a href=\"".$website_url.$url_file."?act=login\">Login</a><br />"; }
$navbar = $navbar."</div>";
$upce = null; $upca = null; $ean13 = null;
if(!isset($_GET['act'])&&isset($_POST['act'])) { $_GET['act'] = $_POST['act']; }
if(!isset($_GET['act'])) { $_GET['act'] = "lookup"; 
	header("Location: ".$website_url.$url_file."?act=lookup"); }
if(!isset($_POST['upc'])&&isset($_GET['upc'])) { $_POST['upc'] = $_GET['upc']; }
if(isset($_POST['upc'])) {
if(strlen($_POST['upc'])==13&&validate_ean13($_POST['upc'])===true&&
	validate_upca(convert_ean13_to_upca($_POST['upc']))===true) {
	$_POST['upc'] = convert_ean13_to_upca($_POST['upc']); }
if(strlen($_POST['upc'])==12&&validate_upca($_POST['upc'])===true&&
	validate_upce(convert_upca_to_upce($_POST['upc']))===true) {
	$_POST['upc'] = convert_upca_to_upce($_POST['upc']); }
if(strlen($_POST['upc'])==8&&validate_upce($_POST['upc'])===true) { 
	$upce = $_POST['upc'];
	$_POST['upc'] = convert_upce_to_upca($_POST['upc']); }
if(strlen($_POST['upc'])==8&&validate_upce($_POST['upc'])===false&&validate_ean8($_POST['upc'])===true) {
	$_POST['upc'] = convert_ean8_to_upca($_POST['upc']); }
if(strlen($_POST['upc'])==12&&validate_upca($_POST['upc'])===true) {
	$upca = $_POST['upc'];
	$_POST['upc'] = convert_upca_to_ean13($_POST['upc']); }
if(strlen($_POST['upc'])==13&&validate_ean13($_POST['upc'])===true) {
	$ean13 = $_POST['upc']; }
if(strlen($_POST['upc'])==13&&validate_ean13($_POST['upc'])===false) {
	unset($_POST['upc']); } }
if(isset($_POST['upc'])&&!is_numeric($_POST['upc'])) {
	unset($_POST['upc']); }
if(isset($_POST['upc'])&&strlen($_POST['upc'])>13) {
	unset($_POST['upc']); }
if(isset($_GET['upc'])) {
if(strlen($_GET['upc'])==8&&validate_upce($_GET['upc'])===true) { 
	$upce = $_GET['upc'];
	$_GET['upc'] = convert_upce_to_upca($_GET['upc']); }
if(strlen($_GET['upc'])==8&&validate_upce($_GET['upc'])===false&&validate_ean8($_GET['upc'])===true) {
	$_GET['upc'] = convert_ean8_to_upca($_GET['upc']); }
if(strlen($_GET['upc'])==12&&validate_upca($_GET['upc'])===true) {
	$upca = $_GET['upc'];
	$_GET['upc'] = convert_upca_to_ean13($_GET['upc']); }
if(strlen($_GET['upc'])==13&&validate_ean13($_GET['upc'])===true) {
	$ean13 = $_GET['upc']; }
if(strlen($_GET['upc'])==13&&validate_ean13($_GET['upc'])===false) {
	unset($_GET['upc']); } }
if(isset($_GET['upc'])&&!is_numeric($_GET['upc'])) {
	unset($_GET['upc']); }
if(isset($_GET['upc'])&&strlen($_GET['upc'])>13) {
	unset($_GET['upc']); }
if(($_GET['act']=="upca"||$_GET['act']=="upce"||$_GET['act']=="ean13")&&
	!isset($_GET['upc'])) { $_GET['act'] = "lookup"; 
	header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="add"&&isset($_POST['upc'])) {
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"upcdatabase_items\" WHERE upc='".sqlite3_escape_string($slite3, $ean13)."';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) { $_GET['act'] = "lookup"; 
	header("Location: ".$website_url.$url_file."?act=lookup&upc=".$_POST['upc']); } }
if($_GET['act']=="add"&&!isset($_POST['upc'])) { $_GET['act'] = "lookup"; 
	header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="lookup"&&isset($_POST['upc'])&&strlen($_POST['upc'])==8&&
	validate_upce($_POST['upc'])===false) { 
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="add"&&isset($_POST['upc'])&&strlen($_POST['upc'])==8&&
	validate_upce($_POST['upc'])===false) { 
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="lookup"&&isset($_POST['upc'])&&strlen($_POST['upc'])==12&&
	validate_upca($_POST['upc'])===false) { 
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="add"&&isset($_POST['upc'])&&strlen($_POST['upc'])==12&&
	validate_upca($_POST['upc'])===false) { 
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="lookup"&&isset($_POST['upc'])&&strlen($_POST['upc'])==13&&
	validate_ean13($_POST['upc'])===false) { 
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="add"&&isset($_POST['upc'])&&strlen($_POST['upc'])==13&&
	validate_ean13($_POST['upc'])===false) { 
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="add"&&isset($_POST['upc'])&&
	(!preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { 
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup&upc=".$_POST['upc']); }
if($_GET['act']=="add"&&!isset($_COOKIE['MemberName'])&&!isset($_COOKIE['MemberID'])&&
	!isset($_COOKIE['SessPass'])) { $_GET['act'] = "lookup"; 
	header("Location: ".$website_url.$url_file."?act=lookup&upc=".$_POST['upc']); }
if($_GET['act']=="add"&&$usersiteinfo['validated']=="no") { $_GET['act'] = "lookup"; 
	header("Location: ".$website_url.$url_file."?act=lookup"); }
if(isset($_COOKIE['MemberName'])&&isset($_COOKIE['MemberID'])&&isset($_COOKIE['SessPass'])) {
if($_GET['act']=="login"||$_GET['act']=="signin"||$_GET['act']=="join"||$_GET['act']=="signup") {
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); } }
if($_GET['act']=="logout"||$_GET['act']=="signout") { 
	unset($_COOKIE['MemberName']); 
	setcookie("MemberName", NULL, -1, $cbasedir, $cookieDomain);
	unset($_COOKIE['MemberID']); 
	setcookie("MemberID", NULL, -1, $cbasedir, $cookieDomain);
	unset($_COOKIE['SessPass']); 
	setcookie("SessPass", NULL, -1, $cbasedir, $cookieDomain);
	$_GET['act'] = "login"; header("Location: ".$website_url.$url_file."?act=login"); }
if($_GET['act']=="lookup") { 
$lookupupc = null;
if(isset($_POST['upc'])&&is_numeric($_POST['upc'])) { $lookupupc = $_POST['upc']; }
if(isset($_POST['upc'])&&!is_numeric($_POST['upc'])) { $lookupupc = null; }
if(!isset($_POST['upc'])) { $lookupupc = null; } }
if(($_GET['act']=="login"||$_GET['act']=="signin")&&
	isset($_POST['username'])&&isset($_POST['password'])) {
	$findme = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"upcdatabase_members\" WHERE name='".sqlite3_escape_string($slite3, $_POST['username'])."';");
	$numfindme = sql_fetch_assoc($findme);
	$numfmrows = $numfindme['COUNT'];
	if($numfmrows<1) { $_GET['act'] = "login"; }
	if($numfmrows>0) {
	$findme = sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_members\" WHERE name='".sqlite3_escape_string($slite3, $_POST['username'])."';"); 
	$userinfo = sql_fetch_assoc($findme);
	if($userinfo['hashtype']=="md2") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"md2"); }
	if($userinfo['hashtype']=="md4") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"md4"); }
	if($userinfo['hashtype']=="md5") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"md5"); }
	if($userinfo['hashtype']=="sha1") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"sha1"); }
	if($userinfo['hashtype']=="sha224") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"sha224"); }
	if($userinfo['hashtype']=="sha256") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"sha256"); }
	if($userinfo['hashtype']=="sha384") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"sha384"); }
	if($userinfo['hashtype']=="sha512") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"sha512"); }
	if($userinfo['hashtype']=="ripemd128") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"ripemd128"); }
	if($userinfo['hashtype']=="ripemd160") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"ripemd160"); }
	if($userinfo['hashtype']=="ripemd256") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"ripemd256"); }
	if($userinfo['hashtype']=="ripemd320") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"ripemd320"); }
	if($userinfo['hashtype']=="salsa10") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"salsa10"); }
	if($userinfo['hashtype']=="salsa20") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"salsa20"); }
	if($userinfo['hashtype']=="snefru") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"snefru"); }
	if($userinfo['hashtype']=="snefru256") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"snefru256"); }
	if($userinfo['hashtype']=="gost") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"gost"); }
	if($userinfo['hashtype']=="joaat") { 
	$PasswordCheck = b64e_hmac($_POST['password'],$userinfo['timestamp'],$userinfo['salt'],"joaat"); }
	if($userinfo['password']!=$PasswordCheck) { $_GET['act'] = "login"; 
	header("Location: ".$website_url.$url_file."?act=login"); } 
	if($userinfo['password']==$PasswordCheck) {
	sqlite3_query($slite3, "UPDATE \"upcdatabase_members\" SET \"lastactive\"='".time()."',\"ip\"='".$usersip."' WHERE \"name\"='".$userinfo['name']."' AND \"id\"='".$userinfo['id']."';");
	setcookie("MemberName", $userinfo['name'], time() + (7 * 86400), $cbasedir, $cookieDomain);
	setcookie("MemberID", $userinfo['id'], time() + (7 * 86400), $cbasedir, $cookieDomain);
	setcookie("SessPass", $userinfo['password'], time() + (7 * 86400), $cbasedir, $cookieDomain); 
	$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); } } }
if(($_GET['act']=="join"||$_GET['act']=="signup")&&
	isset($_POST['username'])&&isset($_POST['email'])&&
	isset($_POST['password'])&&isset($_POST['passwordcheck'])&&
	$_POST['password']==$_POST['passwordcheck']) {
$UserJoined = time(); $HashSalt = salt_hmac();
if($usehashtype=="md2") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"md2"); }
if($usehashtype=="md4") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"md4"); }
if($usehashtype=="md5") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"md5"); }
if($usehashtype=="sha1") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"sha1"); }
if($usehashtype=="sha224") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"sha224"); }
if($usehashtype=="sha256") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"sha256"); }
if($usehashtype=="sha384") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"sha384"); }
if($usehashtype=="sha512") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"sha512"); }
if($usehashtype=="ripemd128") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"ripemd128"); }
if($usehashtype=="ripemd160") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"ripemd160"); }
if($usehashtype=="ripemd256") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"ripemd256"); }
if($usehashtype=="ripemd320") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"ripemd320"); }
if($usehashtype=="salsa10") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"salsa10"); }
if($usehashtype=="salsa20") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"salsa20"); }
if($usehashtype=="snefru") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"snefru"); }
if($usehashtype=="snefru256") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"snefru256"); }
if($usehashtype=="gost") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"gost"); }
if($usehashtype=="joaat") { 
	$NewPassword = b64e_hmac($_POST['password'],$UserJoined,$HashSalt,"joaat"); }
sqlite3_query($slite3, "INSERT INTO \"upcdatabase_members\" (\"name\", \"password\", \"hashtype\", \"email\", \"timestamp\", \"lastactive\", \"validated\", \"numitems\", \"numpending\", \"admin\", \"ip\", \"salt\") VALUES ('".sqlite3_escape_string($slite3, $_POST['username'])."', '".sqlite3_escape_string($slite3, $NewPassword)."', '".sqlite3_escape_string($slite3, $usehashtype)."', '".sqlite3_escape_string($slite3, $_POST['email'])."', ".sqlite3_escape_string($slite3, $UserJoined).", ".sqlite3_escape_string($slite3, $UserJoined).", 'no', 0, 0, 'no', '".sqlite3_escape_string($slite3, $usersip)."', '".sqlite3_escape_string($slite3, $HashSalt)."');"); 
$usersid = sqlite3_last_insert_rowid($slite3);
if($usersid>1&&$validate_members===false) { sqlite3_query($slite3, "UPDATE \"upcdatabase_members\" SET \"validated\"='yes' WHERE \"name\"='".$_POST['username']."' AND \"id\"=".$usersid.";"); }
if($usersid==1) { sqlite3_query($slite3, "UPDATE \"upcdatabase_members\" SET \"validated\"='yes',\"admin\"='yes' WHERE \"name\"='".$_POST['username']."' AND \"id\"=1;"); }
setcookie("MemberName", $_POST['username'], time() + (7 * 86400), $cbasedir, $cookieDomain);
setcookie("MemberID", $usersid, time() + (7 * 86400), $cbasedir, $cookieDomain);
setcookie("SessPass", $NewPassword, time() + (7 * 86400), $cbasedir, $cookieDomain);
$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup"); }
if($_GET['act']=="join"||$_GET['act']=="signup") { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> <?php echo $sitename; ?>: Create an Account </title>
<?php echo $metatags; ?>
 </head>

 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Create an Account</h2>
   <form action="<?php echo $website_url.$url_file; ?>?act=join" method="post">
    <table>
    <tr><td style="text-align: center;">Username:</td><td><input type="text" name="username" /></td></tr>
    <tr><td style="text-align: center;">Password:</td><td><input type="password" name="password" /></td></tr>
    <tr><td style="text-align: center;">Confirm Password:</td><td><input type="password" name="passwordcheck" /></td></tr>
    <tr><td style="text-align: center;">Email address:</td><td><input type="text" name="email" /></td></tr>
   </table>
   <div><br /><input type="submit" value="Sign Up!" /></div>
   </form>
  </center>
 </body>
</html>
<?php } if($_GET['act']=="login"||$_GET['act']=="signin") { ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> <?php echo $sitename; ?>: Log In </title>
<?php echo $metatags; ?>
 </head>

 <body>
  <center>
   <?php echo $navbar; ?>
   <h2>Log In</h2>
   <form action="<?php echo $website_url.$url_file; ?>?act=login" method="post">
    <table>
    <tr><td style="text-align: center;">Username:</td><td><input type="text" name="username" /></td></tr>
    <tr><td style="text-align: center;">Password:</td><td><input type="password" name="password" /></td></tr>
   </table>
   <div><br /><input type="submit" value="Log In!" /></div>
   </form>
  </center>
 </body>
</html>
<?php } if($_GET['act']=="add"&&isset($_POST['upc'])&&
	 isset($_POST['description'])&&isset($_POST['sizeweight'])) {
$findusrinfo = sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_members\" WHERE name='".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."';"); 
$getuserinfo = sql_fetch_assoc($findusrinfo); 
$newnumitems = $getuserinfo['numitems'];
$newnumpending = $getuserinfo['numpending'];
if($usersiteinfo['admin']=="yes") {
$newnumitems = $getuserinfo['numitems'] + 1; }
if($usersiteinfo['admin']=="no"&&$validate_items===true) {
$newnumpending = $getuserinfo['numpending'] + 1; }
if($usersiteinfo['admin']=="no"&&$validate_items===false) {
$newnumitems = $getuserinfo['numitems'] + 1; }
sqlite3_query($slite3, "UPDATE \"upcdatabase_members\" SET \"lastactive\"='".time()."',\"numitems\"=".$newnumitems.", \"numpending\"=".$newnumpending.",\"ip\"='".$usersip."' WHERE \"name\"='".$_COOKIE['MemberName']."' AND \"id\"='".$_COOKIE['MemberID']."';");
$itemvalidated = "no";
if($_COOKIE['MemberID']==1) { $itemvalidated = "yes"; }
if($usersiteinfo['admin']=="yes") { $itemvalidated = "yes"; }
if($usersiteinfo['admin']=="no"&&$_COOKIE['MemberID']>1&&$validate_items===false) { $itemvalidated = "yes"; }
if($usersiteinfo['admin']=="no"&&$_COOKIE['MemberID']>1&&$validate_items===true) { $itemvalidated = "no"; }
if($usersiteinfo['admin']=="yes") {
sqlite3_query($slite3, "INSERT INTO \"upcdatabase_items\" (\"upc\", \"description\", \"sizeweight\", \"validated\", \"delrequest\", \"userid\", \"username\", \"timestamp\", \"lastupdate\", \"edituserid\", \"editname\", \"ip\", \"editip\") VALUES ('".sqlite3_escape_string($slite3, $_POST['upc'])."', '".sqlite3_escape_string($slite3, $_POST['description'])."', '".sqlite3_escape_string($slite3, $_POST['sizeweight'])."', '".sqlite3_escape_string($slite3, $itemvalidated)."', 'no', ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', ".time().", ".time().", ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', '".sqlite3_escape_string($slite3, $usersip)."', '".sqlite3_escape_string($slite3, $usersip)."');"); }
if($usersiteinfo['admin']=="no"&&$validate_items===false) {
sqlite3_query($slite3, "INSERT INTO \"upcdatabase_items\" (\"upc\", \"description\", \"sizeweight\", \"validated\", \"delrequest\", \"userid\", \"username\", \"timestamp\", \"lastupdate\", \"edituserid\", \"editname\", \"ip\", \"editip\") VALUES ('".sqlite3_escape_string($slite3, $_POST['upc'])."', '".sqlite3_escape_string($slite3, $_POST['description'])."', '".sqlite3_escape_string($slite3, $_POST['sizeweight'])."', '".sqlite3_escape_string($slite3, $itemvalidated)."', 'no', ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', ".time().", ".time().", ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', '".sqlite3_escape_string($slite3, $usersip)."', '".sqlite3_escape_string($slite3, $usersip)."');"); }
if($usersiteinfo['admin']=="no"&&$validate_items===true) {
sqlite3_query($slite3, "INSERT INTO \"upcdatabase_pending\" (\"upc\", \"description\", \"sizeweight\", \"validated\", \"delrequest\", \"userid\", \"username\", \"timestamp\", \"lastupdate\", \"ip\") VALUES ('".sqlite3_escape_string($slite3, $_POST['upc'])."', '".sqlite3_escape_string($slite3, $_POST['description'])."', '".sqlite3_escape_string($slite3, $_POST['sizeweight'])."', '".sqlite3_escape_string($slite3, $itemvalidated)."', 'no', ".sqlite3_escape_string($slite3, $_COOKIE['MemberID']).", '".sqlite3_escape_string($slite3, $_COOKIE['MemberName'])."', ".time().", ".time().", '".sqlite3_escape_string($slite3, $usersip)."');"); }
$_GET['act'] = "lookup"; header("Location: ".$website_url.$url_file."?act=lookup&upc=".$_POST['upc']); }
if($_GET['act']=="lookup") { 
if(isset($_POST['upc'])) {
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"upcdatabase_items\" WHERE upc='".sqlite3_escape_string($slite3, $ean13)."';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) {
$findupc = sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_items\" WHERE upc='".sqlite3_escape_string($slite3, $ean13)."';"); 
$upcinfo = sql_fetch_assoc($findupc); }
$oldnumrows = $numrows;
if($oldnumrows<1) {
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"upcdatabase_pending\" WHERE upc='".sqlite3_escape_string($slite3, $ean13)."';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT']; 
if($numrows>0) {
$findupc = sqlite3_query($slite3, "SELECT * FROM \"upcdatabase_pending\" WHERE upc='".sqlite3_escape_string($slite3, $ean13)."';"); 
$upcinfo = sql_fetch_assoc($findupc); 
$upcinfo['validated'] = "no"; } } }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <?php if(!isset($_POST['upc'])) { ?>
  <title> <?php echo $sitename; ?>: Item Lookup </title>
  <?php } if(isset($_POST['upc'])&&$numrows>0&&$upcinfo['validated']=="yes"&&
	(!preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
  <title> <?php echo $sitename; ?>: Item Record </title>
  <?php } if(isset($_POST['upc'])&&$numrows>0&&$upcinfo['validated']=="no"&&
	(!preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
  <title> <?php echo $sitename; ?>: Item Found </title>
  <?php } if(isset($_POST['upc'])&&$numrows===0&&
	(!preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
  <title> <?php echo $sitename; ?>: Item Not Found </title>
  <?php } if(isset($_POST['upc'])&&
	(preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
  <title> <?php echo $sitename; ?>: Random Weight UPC </title>
  <?php } if(isset($_POST['upc'])&&
	(!preg_match("/^02/", $_POST['upc'])&&preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
  <title> <?php echo $sitename; ?>: Dummy UPC </title>
  <?php } ?>
<?php echo $metatags; ?>
 </head>

 <body>
  <center>
   <?php echo $navbar; ?>
   <?php if(isset($_POST['upc'])&&preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])) { 
   $RandWeight = get_upca_vw_info("207362401432");
   $price_split = str_split($RandWeight['price'], 2);
   $RandWeight['price'] = ltrim($price_split[0].".".$price_split[1], "0"); 
   ?>
   <h2>Random Weight UPC</h2>
   <div>Random weight (number system 2) UPCs are a way of price-marking an item. The first (number system) digit is always 2.<br />  The next 5 (6?) digits are locally assigned (meaning anybody can use them for whatever they want).<br /> The next 5 (4?) are the price (2 decimal places), and the last digit is the check digit, calculated normally.</div>
   <table>
   <tr><td width="125">UPC-A</td><td width="50"><img src="<?php echo $website_url; ?>barcode.php?act=upca&amp;upc=<?php echo $upca; ?>" alt="<?php echo $upca; ?>" title="<?php echo $upca; ?>" /></td></tr>
   <tr><td width="125">Product Code</td><td width="50"><?php echo $RandWeight['code']; ?></td></tr>
   <tr><td width="125">Price</td><td width="50"><?php echo $RandWeight['price']; ?></td></tr>
   </table>
   <div><br /></div>
   <?php } if(isset($_POST['upc'])&&preg_match("/^04/", $_POST['upc'])&&!preg_match("/^02/", $_POST['upc'])) { 
   ?>
   <h2>Dummy UPC</h2>
   <div>Dummy (number system 4) UPCs are for private use.<br />  This means anybody (typically a retailer) that needs to assign a UPC to an item that doesn't already have one, can use any number system 4 UPC it chooses.<br />  Most importantly, they can know that by doing so, they won't pick one that may already be used.<br />  So, such a UPC can and does mean something different depending on who you ask, and there's no reason to try to keep track of what products these correspond to.</div>
   <table>
   <tr><td width="125">UPC-A</td><td width="50"><img src="<?php echo $website_url; ?>barcode.php?act=upca&amp;upc=<?php echo $upca; ?>" alt="<?php echo $upca; ?>" title="<?php echo $upca; ?>" /></td></tr>
   </table>
   <div><br /></div>
   <?php } if(!isset($_POST['upc'])&&
	(!preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
   <h2>Item Lookup</h2>
   <?php } if(isset($_POST['upc'])&&$numrows>0&&$upcinfo['validated']=="yes"&&
	(!preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
   <h2>Item Record</h2>
   <table>
   <?php if($upce!==null&&validate_upce($upce)===true) { ?>
   <tr><td>UPC-E</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=upce&amp;upc=<?php echo $upce; ?>" alt="<?php echo $upce; ?>" title="<?php echo $upce; ?>" /></td></tr>
   <?php } if($upca!==null&&validate_upca($upca)===true) { ?>
   <tr><td>UPC-A</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=upca&amp;upc=<?php echo $upca; ?>" alt="<?php echo $upca; ?>" title="<?php echo $upca; ?>" /></td></tr>
   <?php } if($ean13!==null&&validate_ean13($ean13)===true) { ?>
   <tr><td>EAN/UCC-13</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=ean13&amp;upc=<?php echo $ean13; ?>" alt="<?php echo $ean13; ?>" title="<?php echo $ean13; ?>" /></td></tr>
   <?php } ?>
   <tr><td>Description</td><td width="50"></td><td><?php echo htmlspecialchars($upcinfo['description']); ?></td></tr>
   <tr><td>Size/Weight</td><td width="50"></td><td><?php echo htmlspecialchars($upcinfo['sizeweight']); ?></td></tr>
   <tr><td>Issuing Country</td><td width="50"></td><td><?php echo get_gs1_prefix($ean13); ?></td></tr>
   <tr><td>Last Modified</td><td width="50"></td><td><?php echo date("j M Y, g:i A T", $upcinfo['lastupdate']); ?></td></tr>
   <tr><td>Last Modified By</td><td width="50"></td><td><?php echo $upcinfo['username']; ?></td></tr>
   </table>
   <!--<div><br />-->
   <!--<a href="/neighbors.asp?upc=0012345000065">List Neighboring Items</a><br/>-->
   <!--<a href="/editform.asp?upc=0012345000065">Submit Modification Request</a><br/>-->
   <!--<a href="/deleteform.asp?upc=0012345000065">Submit Deletion Request</a><br/>-->
   <!--<br /><br /></div>-->
   <div><br /></div>
   <?php } if(isset($_POST['upc'])&&$numrows>0&&$upcinfo['validated']=="no"&&
	(!preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
   <h2>Item Found</h2>
   <div>The UPC you were looking for currently is in the database but has not been validated yet.<br /><br /></div>
   <table>
   <?php if($upce!==null&&validate_upce($upce)===true) { ?>
   <tr><td>UPC-E</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=upce&amp;upc=<?php echo $upce; ?>" alt="<?php echo $upce; ?>" title="<?php echo $upce; ?>" /></td></tr>
   <?php } if($upca!==null&&validate_upca($upca)===true) { ?>
   <tr><td>UPC-A</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=upca&amp;upc=<?php echo $upca; ?>" alt="<?php echo $upca; ?>" title="<?php echo $upca; ?>" /></td></tr>
   <?php } if($ean13!==null&&validate_ean13($ean13)===true) { ?>
   <tr><td>EAN/UCC-13</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=ean13&amp;upc=<?php echo $ean13; ?>" alt="<?php echo $ean13; ?>" title="<?php echo $ean13; ?>" /></td></tr>
   <?php } ?>
   </table>
   <div><br />Please try coming back later.<br /><br /></div>
   <?php } if(isset($_POST['upc'])&&$numrows===0&&
	(!preg_match("/^02/", $_POST['upc'])&&!preg_match("/^04/", $_POST['upc'])&&
	!preg_match("/^05/", $_POST['upc'])&&!preg_match("/^09/", $_POST['upc']))) { ?>
   <h2>Item Not Found</h2>
   <div>The UPC you were looking for currently has no record in the database.<br /><br /></div>
   <table>
   <?php if($upce!==null&&validate_upce($upce)===true) { ?>
   <tr><td>UPC-E</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=upce&amp;upc=<?php echo $upce; ?>" alt="<?php echo $upce; ?>" title="<?php echo $upce; ?>" /></td></tr>
   <?php } if($upca!==null&&validate_upca($upca)===true) { ?>
   <tr><td>UPC-A</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=upca&amp;upc=<?php echo $upca; ?>" alt="<?php echo $upca; ?>" title="<?php echo $upca; ?>" /></td></tr>
   <?php } if($ean13!==null&&validate_ean13($ean13)===true) { ?>
   <tr><td>EAN/UCC-13</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=ean13&amp;upc=<?php echo $ean13; ?>" alt="<?php echo $ean13; ?>" title="<?php echo $ean13; ?>" /></td></tr>
   <?php } ?>
   </table>
   <!--<div><br />Even though this item is not on file here, looking at some of its
   <a href="/neighbors.asp?upc=0099999000092">close neighbors</a>
   may give you an idea what this item might be, or who manufactures it.<br /></div>-->
   <div><br />If you know what this item is, and would like to contribute to the database
   by providing a description for this item, please
   <a href="<?php echo $website_url.$url_file; ?>?act=add&amp;upc=<?php echo $ean13; ?>">CLICK HERE</a>.<br /><br /></div>
   <?php } ?>
   <form action="<?php echo $website_url.$url_file; ?>?act=lookup" method="get">
    <input type="hidden" name="act" value="lookup" />
    <table>
    <tr><td style="text-align: center;"><input type="text" name="upc" size="16" maxlength="13" value="<?php echo $lookupupc; ?>" /> <input type="submit" value="Look Up UPC" /></td></tr>
   </table>
   </form>
  </center>
 </body>
</html>
<?php } if($_GET['act']=="add"&&isset($_POST['upc'])) { ?>
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
   <?php if($upce!==null&&validate_upce($upce)===true) { ?>
   <tr><td>UPC-E</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=upce&amp;upc=<?php echo $upce; ?>" alt="<?php echo $upce; ?>" title="<?php echo $upce; ?>" /></td></tr>
   <?php } if($upca!==null&&validate_upca($upca)===true) { ?>
   <tr><td>UPC-A</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=upca&amp;upc=<?php echo $upca; ?>" alt="<?php echo $upca; ?>" title="<?php echo $upca; ?>" /></td></tr>
   <?php } if($ean13!==null&&validate_ean13($ean13)===true) { ?>
   <tr><td>EAN/UCC-13</td><td width="50"></td><td><img src="<?php echo $website_url; ?>barcode.php?act=ean13&amp;upc=<?php echo $ean13; ?>" alt="<?php echo $ean13; ?>" title="<?php echo $ean13; ?>" /></td></tr>
   <?php } ?>
   </table>
   <div><br /></div>
   <form action="<?php echo $website_url.$url_file; ?>?act=add" method="post">
    <table>
    <tr><td style="text-align: center;">Description: <input type="text" name="description" size="50" maxlength="150" /></td></tr>
    <tr><td style="text-align: center;">Size/Weight: <input type="text" name="sizeweight" size="30" maxlength="25" /></td></tr>
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
   <?php if($check_ean13!==null&&validate_ean13($check_ean13)===true) { ?>
   <tr><td>EAN/UCC-13:</td><td><?php echo $check_ean13; ?></td></tr>
   <?php } if($check_upca!==null&&validate_upca($check_upca)===true) { ?>
   <tr><td>UPC-A:</td><td><?php echo $check_upca; ?></td></tr>
   <?php } if($check_upce!==null&&validate_upce($check_upce)===true) { ?>
   <tr><td>UPC-E:</td><td><?php echo $check_upce; ?></td></tr>
   <?php } ?>
   <tr><td colspan="2"><a href="<?php echo $website_url.$url_file."?act=lookup&amp;upc=".$check_ean13; ?>">Click here</a> to look up this UPC in the database.</td></tr>
   </table>
   <?php } ?>
  </center>
 </body>
</html>
<?php } sqlite3_close($slite3); ?>