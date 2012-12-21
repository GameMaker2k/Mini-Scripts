<?php
$sqlite_version = 3;

if(extension_loaded("sqlite3")&&$sqlite_version==3) {
function sqlite3_open($filename, $mode = 0666) {
   global $site_encryption_key;
   if($site_encryption_key===null) {
   $handle = new SQLite3($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE); }
   if($site_encryption_key!==null) {
   $handle = new SQLite3($filename, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE, $site_encryption_key); }
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
function sqlite3_fetch_array($result, $result_type=SQLITE3_BOTH) {
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
	return $dbversion['versionString']; } }
if(extension_loaded("sqlite")&&$sqlite_version==2) {
function sqlite3_open($filename, $mode = 0666) {
   $handle = sqlite_open($filename, $mode);
   return $handle; }
function sqlite3_close($dbhandle) {
   $dbhandle = sqlite_close($dbhandle);
   return true; }
function sqlite3_escape_string($dbhandle, $string) {
   $string = sqlite_escape_string($string);
   return $string; }
function sqlite3_query($dbhandle, $query) {
   $results = sqlite_query($dbhandle, $query);
   return $results; }
function sqlite3_fetch_array($result, $result_type=SQLITE_BOTH) {
	$row = sqlite_fetch_array($result, $result_type=SQLITE_BOTH);
	return $row; }
function sql_fetch_assoc($result) {
	$row = sqlite_fetch_array($result, SQLITE_ASSOC);
	return $row; }
function sqlite3_last_insert_rowid($dbhandle) {
	$rowid = sqlite_last_insert_rowid($dbhandle);
	return $rowid; }
function sqlite3_libversion($dbhandle) {
	$dbversion = sqlite_libversion();
	return $dbversion; } }

$slite3 = sqlite3_open("./".$roomname.".sdb");
$tablecheck1 = @sqlite3_query($slite3, "SELECT * FROM \"".$sqlprefix."members\""); 
if($tablecheck1===false) {
sqlite3_query($slite3, "PRAGMA auto_vacuum = 1;");
sqlite3_query($slite3, "PRAGMA encoding = \"UTF-8\";");
$query = "CREATE TABLE \"".$sqlprefix."members\" (\n".
"  \"id\" INTEGER PRIMARY KEY NOT NULL,\n".
"  \"name\" VARCHAR(150) UNIQUE NOT NULL default '',\n".
"  \"password\" VARCHAR(250) NOT NULL default '',\n".
"  \"joined\" INTEGER NOT NULL default '0',\n".
"  \"lastactive\" INTEGER NOT NULL default '0',\n".
"  \"validated\" VARCHAR(20) NOT NULL default '',\n".
"  \"bantime\" INTEGER NOT NULL default '0',\n".
"  \"admin\" VARCHAR(20) NOT NULL default '',\n".
"  \"ip\" VARCHAR(50) NOT NULL default ''\n".
");";
sqlite3_query($slite3, $query); }
$tablecheck2 = @sqlite3_query($slite3, "SELECT * FROM \"".$sqlprefix."messages\""); 
if($tablecheck2===false) {
sqlite3_query($slite3, "PRAGMA auto_vacuum = 1;");
sqlite3_query($slite3, "PRAGMA encoding = \"UTF-8\";");
$query = "CREATE TABLE \"".$sqlprefix."messages\" (\n".
"  \"id\" INTEGER PRIMARY KEY NOT NULL,\n".
"  \"userid\" INTEGER NOT NULL default '0',\n".
"  \"username\" VARCHAR(150) NOT NULL default '',\n".
"  \"timestamp\" INTEGER NOT NULL default '0',\n".
"  \"message\" TEXT NOT NULL,\n".
"  \"ip\" VARCHAR(50) NOT NULL default ''\n".
");";
sqlite3_query($slite3, $query); }
?>