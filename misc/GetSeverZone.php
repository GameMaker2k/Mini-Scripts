<?php
// Get Server offset
function GetSeverZone() {
$TestHour1 = date("H");
@putenv("OTZ=".@getenv("TZ"));
@putenv("TZ=GMT");
$TestHour2 = date("H");
@putenv("TZ=".@getenv("OTZ"));
$TestHour3 = $TestHour1-$TestHour2;
return $TestHour3; }
function SeverOffSet() {
$TestHour1 = date("H");
$TestHour2 = gmdate("H");
$TestHour3 = $TestHour1-$TestHour2;
return $TestHour3; }
function SeverOffSetNew() {
return gmdate("g",mktime(0,date("Z"))); }
function gmtime() { return time() - (int) date('Z'); }
?>
