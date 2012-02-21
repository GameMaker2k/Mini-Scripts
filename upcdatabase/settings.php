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

    $FileInfo: settings.php - Last Update: 02/13/2012 Ver. 2.2.5 RC 1 - Author: cooldude2k $
*/
$File3Name = basename($_SERVER['SCRIPT_NAME']);

$website_url = "https://localhost/upcdatabase/";
$url_file = "upc.php";
$url_admin_file = "admin.php";
$sdb_file = "upcdatabase.sdb";
$usehashtype = "sha256";
$validate_items = true;
$validate_members = true;
$appname = htmlspecialchars("UPC Database");
$appmakerurl = "https://github.com/KazukiPrzyborowski/UPC-A-EAN-13-Maker";
$appmaker = htmlspecialchars("Game Maker 2k");
$appver = array(2,2,5,"RC 1");
$upcdatabase = "http://www.upcdatabase.com/item/%s";
$sitename = $appname;
$siteauthor = $appmaker;
$sitekeywords = null;
$sitedescription = null;

@ob_start();

if ($File3Name=="settings.php"||$File3Name=="/settings.php") {
	header("Location: ".$website_url.$url_file."?act=lookup");
	exit(); }

$usersip = $_SERVER['REMOTE_ADDR'];
$basecheck = parse_url($website_url);
$basedir = $basecheck['path'];
$cbasedir = $basedir;
$cookieDomain = $basecheck['host'];
$metatags = "<meta http-equiv=\"Content-Language\" content=\"en\" />\n  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n  <meta http-equiv=\"Content-Style-Type\" content=\"text/css\" />\n  <meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\" />\n  <meta name=\"generator\" content=\"".$sitename."\" />\n  <meta name=\"author\" content=\"".$siteauthor."\" />\n  <meta name=\"keywords\" content=\"".$sitekeywords."\" />\n  <meta name=\"description\" content=\"".$sitedescription."\" />\n";
$disfunc = @ini_get("disable_functions");
$disfunc = @trim($disfunc);
$disfunc = @preg_replace("/([\\s+|\\t+|\\n+|\\r+|\\0+|\\x0B+])/i", "", $disfunc);
if($disfunc!="ini_set") { $disfunc = explode(",",$disfunc); }
if($disfunc=="ini_set") { $disfunc = array("ini_set"); }

if(!in_array("ini_set", $disfunc)) {
@ini_set("html_errors", false);
@ini_set("track_errors", false);
@ini_set("display_errors", false);
@ini_set("report_memleaks", false);
@ini_set("display_startup_errors", false);
//@ini_set("error_log","logs/error.log"); 
//@ini_set("log_errors","On"); 
@ini_set("docref_ext", "");
@ini_set("docref_root", "http://php.net/");
@ini_set("date.timezone","UTC"); 
@ini_set("default_mimetype","text/html"); 
@ini_set("zlib.output_compression", false);
@ini_set("zlib.output_compression_level", -1);
//@ini_set("session.use_trans_sid", false);
//@ini_set("session.use_cookies", true);
//@ini_set("session.use_only_cookies", true);
@ini_set("url_rewriter.tags",""); 
@ini_set('zend.ze1_compatibility_mode', 0);
@ini_set("ignore_user_abort", 1); }
if(!defined("E_DEPRECATED")) { define("E_DEPRECATED", 0); }
@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
if(function_exists("date_default_timezone_set")) { 
	@date_default_timezone_set("UTC"); }

@header("Content-Type: text/html; charset=UTF-8");
@header("Content-Language: en");
@header("content-Style-Type: text/css");
@header("Content-Script-Type: text/javascript");
if(!isset($_SERVER['HTTP_USER_AGENT'])) {
	$_SERVER['HTTP_USER_AGENT'] = ""; }
if(strpos($_SERVER['HTTP_USER_AGENT'], "msie") && 
	!strpos($_SERVER['HTTP_USER_AGENT'], "opera")){
	@header("X-UA-Compatible: IE=Edge"); }
if(strpos($_SERVER['HTTP_USER_AGENT'], "chromeframe")) {
	@header("X-UA-Compatible: IE=Edge,chrome=1"); }
@header("Date: ".gmdate("D, d M Y H:i:s")." GMT");
@header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
@header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");

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
$navbar = "<h1><big>".$sitename."</big></h1>\n   <div>";
if(isset($_COOKIE['MemberName'])) { 
	$navbar = $navbar."Welcome: ".$_COOKIE['MemberName']." | <a href=\"".$website_url.$url_file."?act=lookup\">Index Page</a> | <a href=\"".$website_url.$url_file."?act=logout\">Logout</a>".$adminlink."<br />"; }
if(!isset($_COOKIE['MemberName'])) { 
	$navbar = $navbar."Welcome: Guest | <a href=\"".$website_url.$url_file."?act=lookup\">Index Page</a> | <a href=\"".$website_url.$url_file."?act=join\">Join</a> | <a href=\"".$website_url.$url_file."?act=login\">Login</a><br />"; }
$navbar = $navbar."</div>";

?>