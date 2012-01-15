<?php
// Kill bad vars for some functions
function killbadvars($varname) {
$badphp1 = array('$'); $badphp2 = array(null);
$varname = str_replace($badphp1, $badphp2, $varname);
$varname = preg_replace("/(_SERVER|_ENV|_COOKIE|_SESSION)/i", null, $varname);
$varname = preg_replace("/(_GET|_POST|_FILES|_REQUEST|GLOBALS)/i", null, $varname);
$varname = preg_replace("/(HTTP_SERVER_VARS|HTTP_ENV_VARS)/i", null, $varname);
$varname = preg_replace("/(HTTP_COOKIE_VARS|HTTP_SESSION_VARS)/i", null, $varname);
$varname = preg_replace("/(HTTP_GET_VARS|HTTP_POST_VARS|HTTP_POST_FILES)/i", null, $varname);
	return $varname; }
// Change Path info to Get Vars :
function mrstring() {
$urlvar = explode('/',$_SERVER['PATH_INFO']);
$num=count($urlvar); $i=1;
while ($i < $num) {
//$urlvar[$i] = urldecode($urlvar[$i]);
if(!isset($_GET[$urlvar[$i]])) { $_GET[$urlvar[$i]] = null; }
if(!isset($urlvar[$i])) { $urlvar[$i] = null; }
if($_GET[$urlvar[$i]]==null&&$urlvar[$i]!=null) {
$fix1 = array(" ",'$'); $fix2  = array("_","_");
$urlvar[$i] = str_replace($fix1, $fix2, $urlvar[$i]);
$urlvar[$i] = killbadvars($urlvar[$i]);
	$_GET[$urlvar[$i]] = $urlvar[$i+1]; }
++$i; ++$i; } return true; }
mrstring();
?>
