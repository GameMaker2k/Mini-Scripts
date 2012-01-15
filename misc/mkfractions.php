<?php
// Make fractions in html by Kazuki
// With help from Daniel0 at PHPFreaks.com
// http://www.phpfreaks.com/forums/index.php/topic,248351.new.html
function mkfraction($numerator,$denominator,$number=null,$altver=false,$fiximproper=true,$simplify=false) {
if($altver!==true&&$altver!==false) { $altver = false; } $returnvar = null;
if($fiximproper!==true&&$fiximproper!==false) { $fiximproper = true; }
if($simplify!==true&&$simplify!==false) { $simplify = false; }
if(($numerator===0||$numerator===null)&&($denominator===0||$denominator===null)&($number!==null&&$number!==0)) { 
$getnum = explode(" ",$number); 
if(!isset($getnum[1])) { $getnum[1] = $number; }
$number = (int) $getnum[0]; $getfrac = explode("/",$getnum[1]);
$numerator = (int) $getfrac[0]; $denominator = (int) $getfrac[1]; }
if(!is_numeric($number)) { $number = 0; }
if(is_numeric($number)&&is_string($number)) { $number = (int) $number; }
if(!is_numeric($numerator)) { $numerator = 0; }
if(is_numeric($numerator)&&is_string($numerator)) { $numerator = (int) $numerator; }
if(!is_numeric($denominator)) { $denominator = 0; }
if(is_numeric($denominator)&&is_string($denominator)) { $denominator = (int) $denominator; }
if ((int) $denominator===0&&$number===0) {
$returnvar = "<span style=\"font-size: 16px;\">".$number."</span>"; return $returnvar; }
if(!is_numeric($number)) { $number = null; }
if($denominator===0) { $numerator = 0; }
if($denominator!==0) {
if($fiximproper===true&&$numerator>=$denominator) {
if($number!==null&&$number!==0) {
$oldnumber = $number;
$number = (int) ($numerator / $denominator);
$numerator = $numerator - ($number * $denominator);
$number += $oldnumber; }
if($number===null||$number===0) {
$number = (int) ($numerator / $denominator); 
$numerator = $numerator - ($number * $denominator); } } }
if ($numerator < 0 && $denominator < 0) {
$numerator *= -1; $denominator *= -1; }
if (($numerator!==0&&$denominator!==0)&&($denominator % $numerator===0&&$simplify===true)) {
$denominator = $denominator / $numerator;
$numerator = $numerator / $numerator; }
if($number!==0) { $returnvar = "<span style=\"font-size: 16px;\">".$number."</span>"; }
if($numerator>0) { if($number!==0) { $returnvar .= " "; }
$returnvar .= "<span style=\"font-size: 10px;\"><sup>".$numerator."</sup></span>";
if($altver===true) { $returnvar .= "<span style=\"font-size: 16px;\">&frasl;</span>"; }
if($altver===false) { $returnvar .= "<span style=\"font-size: 16px;\">/</span>"; }
$returnvar .= "<span style=\"font-size: 10px;\"><sub>".$denominator."</sub></span>"; } 
return $returnvar; }
?>
