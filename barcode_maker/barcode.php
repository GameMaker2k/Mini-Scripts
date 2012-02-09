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

    $FileInfo: barcode.php - Last Update: 02/09/2012 Ver. 2.1.7 RC 2 - Author: cooldude2k $
*/

/*
UPC Resources and Info
http://en.wikipedia.org/wiki/Universal_Product_Code
http://en.wikipedia.org/wiki/Global_Trade_Item_Number
http://en.wikipedia.org/wiki/Barcode
http://www.ucancode.net/CPP_Library_Control_Tool/Draw-Print-encode-UPCA-barcode-UPCE-barcode-EAN13-barcode-VC-Code.htm
http://en.wikipedia.org/wiki/International_Article_Number
http://www.upcdatabase.com/docs/
http://www.accipiter.org/projects/cat.php
http://www.accipiter.org/download/kittycode.js
http://uscan.sourceforge.net/upc.txt
http://www.adams1.com/upccode.html
http://www.documentmedia.com/Media/PublicationsArticles/QuietZone.pdf
http://zxing.org/w/decode.jspx
http://code.google.com/p/zxing/
http://www.terryburton.co.uk/barcodewriter/generator/
http://en.wikipedia.org/wiki/Interleaved_2_of_5
http://www.gs1au.org/assets/documents/info/user_manuals/barcode_technical_details/ITF_14_Barcode_Structure.pdf
*/

// str_split for php 4 by rlpvandenberg at hotmail dot com
// http://us2.php.net/manual/en/function.str-split.php#79921
if(!function_exists('str_split')) {
function str_split($text, $split = 1){
    //place each character of the string into and array
    $array = array();
    for ($i=0; $i < strlen($text); $i++){
        $key = "";
        for ($j = 0; $j < $split; $j++){
            $key .= $text[$i+$j]; 
        }
        $i = $i + $j - 1;
        array_push($array, $key);
    }
    return $array;
} }

// Code for validating UPC/EAN by Kazuki Przyborowski
require("./inc/validate.php");
// Code for converting UPC/EAN by Kazuki Przyborowski
require("./inc/convert.php");
// Code for making EAN-2 supplement by Kazuki Przyborowski
require("./inc/ean2.php");
// Code for making EAN-5 supplement by Kazuki Przyborowski
require("./inc/ean5.php");
// Code for making UPC-A by Kazuki Przyborowski
require("./inc/upca.php");
// Code for making UPC-E by Kazuki Przyborowski
require("./inc/upce.php");
// Code for making EAN-13 by Kazuki Przyborowski
require("./inc/ean13.php");
// Code for making EAN-8 by Kazuki Przyborowski
require("./inc/ean8.php");
// Code for making Interleaved 2 of 5 by Kazuki Przyborowski
require("./inc/itf.php");
// Code for making ITF-14 by Kazuki Przyborowski
require("./inc/itf14.php");
// Code for making Code 39 by Kazuki Przyborowski
require("./inc/code39.php");
// Code for making Code 93 by Kazuki Przyborowski
require("./inc/code93.php");
// Code for decoding CueCat codes by Neil McNab
require("./inc/cuecat.php");
// Shortcut Codes by Kazuki Przyborowski
function validate_barcode($upc,$return_check=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==8) { return validate_upce($upc,$return_check); }
	if(strlen($upc)==12) { return validate_upca($upc,$return_check); }
	if(strlen($upc)==13) { return validate_ean13($upc,$return_check); } 
	if(strlen($upc)==14) { return validate_itf14($upc,$return_check); } 
	return false; }
function create_barcode($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if(strlen($upc)==7||strlen($upc)==8) { 
		return create_upce($upc,$imgtype,$outputimage,$resize,$resizetype,$outfile,$hidecd); }
	if(strlen($upc)==11||strlen($upc)==12) { 
		return create_upca($upc,$imgtype,$outputimage,$resize,$resizetype,$outfile,$hidecd); }
	if(strlen($upc)==13) { return create_ean13($upc,$imgtype,$outputimage,$resize,$resizetype,$outfile,$hidecd); } 
	if(strlen($upc)==14) { return create_itf14($upc,$imgtype,$outputimage,$resize,$resizetype,$outfile,$hidecd); } 
	return false; }

if(!isset($argv[1])) {
echo "action list: \nvalidate: validate barcode\ncheck: get check digit\ncreate: create barcode\nconvert: convert barcode\nEnter action to do: ";
$act = strtolower(trim(fgets(STDIN), "\x00..\x1F")); }
if(isset($argv[1])) { $act = $argv[1]; }
if($act!="validate"&&
	$act!="check"&&
	$act!="create"&&
	$act!="convert") {
		$act="create"; }
if($act=="validate") {
if(!isset($argv[2])) {
echo "barcode types\nupca: UPC-A\nupce: UPC-E\nean13: EAN-13\nean8: EAN-8\nitf14: ITF-14\nEnter barcode type to validate: ";
$bartype = strtolower(trim(fgets(STDIN), "\x00..\x1F")); }
if(isset($argv[2])) { $bartype = $argv[2]; }
if($bartype!="upca"&&
	$bartype!="upce"&&
	$bartype!="ean13"&&
	$bartype!="ean8"&&
	$bartype!="itf14") {
		$bartype="upcae"; }
if(!isset($argv[3])) {
echo "Enter barcode: ";
$barcode = trim(fgets(STDIN), "\x00..\x1F"); }
if(isset($argv[3])) { $barcode = $argv[3]; }
if($bartype=="upca"&&validate_upca($barcode, false)===true) { echo $barcode." is valid.\n"; }
if($bartype=="upca"&&validate_upca($barcode, false)===false) { echo $barcode." is invalid.\n"; }
if($bartype=="upce"&&validate_upce($barcode, false)===true) { echo $barcode." is valid.\n"; }
if($bartype=="upce"&&validate_upce($barcode, false)===false) { echo $barcode." is invalid.\n"; }
if($bartype=="ean13"&&validate_ean13($barcode, false)===true) { echo $barcode." is valid.\n"; }
if($bartype=="ean13"&&validate_ean13($barcode, false)===false) { echo $barcode." is invalid.\n"; }
if($bartype=="ean8"&&validate_ean8($barcode, false)===true) { echo $barcode." is valid.\n"; }
if($bartype=="ean8"&&validate_ean8($barcode, false)===false) { echo $barcode." is invalid.\n"; }
if($bartype=="itf14"&&validate_itf14($barcode, false)===true) { echo $barcode." is valid.\n"; }
if($bartype=="itf14"&&validate_itf14($barcode, false)===false) { echo $barcode." is invalid.\n"; } }
if($act=="check") {
echo "barcode types\nupca: UPC-A\nupce: UPC-E\nean13: EAN-13\nean8: EAN-8\nitf14: ITF-14\nEnter barcode type to validate: ";
if(!isset($argv[2])) {
$bartype = strtolower(trim(fgets(STDIN), "\x00..\x1F")); }
if(isset($argv[2])) { $bartype = $argv[2]; }
if($bartype!="upca"&&
	$bartype!="upce"&&
	$bartype!="ean13"&&
	$bartype!="ean8"&&
	$bartype!="itf14") {
		$bartype="upcae"; }
if(!isset($argv[3])) {
echo "Enter barcode: ";
$barcode = trim(fgets(STDIN), "\x00..\x1F"); }
if(isset($argv[3])) { $barcode = $argv[3]; }
if($bartype=="upca"&&strlen($barcode)==11) { echo $barcode.validate_upca($barcode, true)."\n"; }
if($bartype=="upce"&&strlen($barcode)==7) { echo $barcode.validate_upce($barcode, true)."\n"; }
if($bartype=="ean13"&&strlen($barcode)==12) { echo $barcode.validate_ean13($barcode, true)."\n"; }
if($bartype=="ean8"&&strlen($barcode)==7) { echo $barcode.validate_ean8($barcode, true)."\n"; }
if($bartype=="itf14"&&strlen($barcode)==13) { echo $barcode.validate_itf14($barcode, true)."\n"; } }
if($act=="create") {
if(!isset($argv[2])) {
echo "barcode types\nupca: UPC-A\nupce: UPC-E\nean13: EAN-13\nean8: EAN-8\nitf14: ITF-14\nitf: ITF\ncode39: CODE39\ncode93: CODE93\nEnter barcode type to create: ";
$bartype = strtolower(trim(fgets(STDIN), "\x00..\x1F")); }
if(isset($argv[2])) { $bartype = $argv[2]; }
if($bartype!="upca"&&
	$bartype!="upce"&&
	$bartype!="ean13"&&
	$bartype!="ean8"&&
	$bartype!="itf"&&
	$bartype!="itf14"&&
	$bartype!="code39"&&
	$bartype!="code93") {
		$bartype="upcae"; }
if(!isset($argv[3])) {
echo "Enter barcode: ";
$barcode = trim(fgets(STDIN), "\x00..\x1F"); }
if(isset($argv[3])) { $barcode = $argv[3]; }
if(!isset($argv[4])) {
echo "Enter file to save to: "; 
$filename = trim(fgets(STDIN), "\x00..\x1F"); }
if(isset($argv[4])) { $filename = $argv[4]; }
$imagetypeext = strtolower(trim(pathinfo($filename, PATHINFO_EXTENSION)));
if($imagetypeext!="png"&&
	$imagetypeext!="gif"&&
	$imagetypeext!="wbmp"&&
	$imagetypeext!="xbm") {
		$imagetypeext="png"; }
/*echo "image types\npng: PNG\ngif: GIF\nwbmp: WBMP\nxbm: XBM\nEnter image type to use: ";
$imagetypeext = strtolower(trim(fgets(STDIN), "\x00..\x1F"));
if($imagetypeext!="png"&&
	$imagetypeext!="gif"&&
	$imagetypeext!="wbmp"&&
	$imagetypeext!="xbm") {
		$imagetypeext="png"; }*/
if(!isset($argv[5])) {
echo "Enter how much to resize barcode by: ";
$resizenum = trim(fgets(STDIN), "\x00..\x1F"); }
if(isset($argv[5])) { $resizenum = $argv[5]; }
$upc_pieces = null; $supplement = null;
if(preg_match("/([0-9]+) ([0-9]{2})$/", $barcode, $upc_pieces)) {
$barcode = $upc_pieces[1]; $supplement = $upc_pieces[2]; }
if(preg_match("/([0-9]+) ([0-9]{5})$/", $barcode, $upc_pieces)) {
$barcode = $upc_pieces[1]; $supplement = $upc_pieces[2]; }
if($bartype=="upca"&&validate_upca($barcode, false)===true) {
echo "Creating Barcode: ".$barcode." to file ".$filename."\n";
ob_start();
if(strlen($supplement)==0) {
create_upca($barcode,$imagetypeext,true,$resizenum); }
if(strlen($supplement)>0) {
create_upca($barcode." ".$supplement,$imagetypeext,true,$resizenum); }
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); }
if($bartype=="upce"&&validate_upce($barcode, false)===true) {
echo "Creating Barcode: ".$barcode." to file ".$filename."\n";
ob_start();
if(strlen($supplement)==0) {
create_upce($barcode,$imagetypeext,true,$resizenum); }
if(strlen($supplement)>0) { 
create_upce($barcode." ".$supplement,$imagetypeext,true,$resizenum); }
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); }
if(strlen($supplement)!=2&&strlen($supplement)!=5) { $supplement = null; }
if($bartype=="ean13"&&validate_ean13($barcode, false)===true) {
echo "Creating Barcode: ".$barcode." to file ".$filename."\n";
ob_start();
if(strlen($supplement)==0) {
create_ean13($barcode,$imagetypeext,true,$resizenum); }
if(strlen($supplement)>0) {
create_ean13($barcode." ".$supplement,$imagetypeext,true,$resizenum); }
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); }
if($bartype=="ean8"&&validate_ean8($barcode, false)===true) {
echo "Creating Barcode: ".$barcode." to file ".$filename."\n";
ob_start();
if(strlen($supplement)==0) {
create_ean8($barcode,$imagetypeext,true,$resizenum); }
if(strlen($supplement)>0) {
create_ean8($barcode." ".$supplement,$imagetypeext,true,$resizenum); }
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); }
if($bartype=="itf") {
if(strlen($barcode) % 2) { exit(); }
if(strlen($barcode) < 6) { exit(); }
echo "Creating Barcode: ".$barcode." to file ".$filename."\n";
ob_start();
create_itf($barcode,$imagetypeext,true,$resizenum);
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); }
if($bartype=="itf14") {
if(strlen($barcode) % 2) { exit(); }
if(strlen($barcode) < 6) { exit(); }
echo "Creating Barcode: ".$barcode." to file ".$filename."\n";
ob_start();
create_itf14($barcode,$imagetypeext,true,$resizenum);
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); }
if($bartype=="code39") {
if(!preg_match("/([0-9a-zA-Z\-\.\$\/\+% ]+)/", $barcode)) { exit(); }
echo "Creating Barcode: ".$barcode." to file ".$filename."\n";
ob_start();
create_code39($barcode,$imagetypeext,true,$resizenum);
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); }
if($bartype=="code93") {
if(!preg_match("/([0-9a-zA-Z\-\.\$\/\+% ]+)/", $barcode)) { exit(); }
echo "Creating Barcode: ".$barcode." to file ".$filename."\n";
ob_start();
create_code93($barcode,$imagetypeext,true,$resizenum);
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); } }
if($act=="convert") {
if(!isset($argv[2])) {
echo "barcode types\nupca: UPC-A\nupce: UPC-E\nean13: EAN-13\nitf14: ITF-14\nEnter barcode type to convert from: ";
$confrom = strtolower(trim(fgets(STDIN), "\x00..\x1F")); }
if(isset($argv[2])) { $confrom = $argv[2]; }
if($confrom!="upca"&&
	$confrom!="upce"&&
	$confrom!="ean13"&&
	$confrom!="itf14") {
		$confrom="upca"; }
if(!isset($argv[3])) {
echo "barcode types\nupca: UPC-A\nupce: UPC-E\nean13: EAN-13\nitf14: ITF-14\nEnter barcode type to convert to: ";
$conto = strtolower(trim(fgets(STDIN), "\x00..\x1F")); }
if(isset($argv[3])) { $conto = $argv[3]; }
if($conto!="upca"&&
	$conto!="upce"&&
	$conto!="ean13"&&
	$conto!="itf14") {
		$conto="ean13"; }
if($confrom==$conto) { exit(); }
if(!isset($argv[4])) {
echo "Enter barcode: ";
$barcode = trim(fgets(STDIN), "\x00..\x1F"); }
if(isset($argv[4])) { $barcode = $argv[4]; }
if($confrom=="upce"&&$conto=="upca"&&validate_upce($barcode, false)===true) { echo convert_upce_to_upca($barcode)."\n"; }
if($confrom=="upca"&&$conto=="upce"&&validate_upca($barcode, false)===true) { echo convert_upca_to_upce($barcode)."\n"; }
if($confrom=="upce"&&$conto=="itf14"&&validate_upce($barcode, false)===true) { echo convert_upce_to_itf14($barcode)."\n"; }
if($confrom=="itf14"&&$conto=="upce"&&validate_itf14($barcode, false)===true) { echo convert_itf14_to_upce($barcode)."\n"; }
if($confrom=="upca"&&$conto=="ean13"&&validate_upca($barcode, false)===true) { echo convert_upca_to_ean13($barcode)."\n"; }
if($confrom=="ean13"&&$conto=="upca"&&validate_ean13($barcode, false)===true) { echo convert_ean13_to_upca($barcode)."\n"; }
if($confrom=="upce"&&$conto=="ean13"&&validate_upce($barcode, false)===true) { echo convert_upce_to_ean13($barcode)."\n"; }
if($confrom=="ean13"&&$conto=="upce"&&validate_ean13($barcode, false)===true) { echo convert_ean13_to_upce($barcode)."\n"; }
if($confrom=="upca"&&$conto=="itf14"&&validate_upca($barcode, false)===true) { echo convert_upca_to_itf14($barcode)."\n"; }
if($confrom=="itf14"&&$conto=="upca"&&validate_itf14($barcode, false)===true) { echo convert_itf14_to_upca($barcode)."\n"; }
if($confrom=="ean13"&&$conto=="itf14"&&validate_ean13($barcode, false)===true) { echo convert_ean13_to_itf14($barcode)."\n"; }
if($confrom=="itf14"&&$conto=="ean13"&&validate_itf14($barcode, false)===true) { echo convert_itf14_to_ean13($barcode)."\n"; } }
?>
