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

    $FileInfo: functions.php - Last Update: 02/05/2012 Ver. 2.1.7 RC 2 - Author: cooldude2k $
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

// Code for decoding CueCat codes by Neil McNab
########################################################################
#
# Project: Grocery List
# URL: http://sourceforge.net/projects/grocery-list/
# E-mail: hide@address.com
#
# Copyright: (C) 2010, Neil McNab
# License: GNU General Public License Version 3
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, version 3 of the License.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
# Filename: $URL: https://grocery-list.svn.sourceforge.net/svnroot/grocery-list/releases/1.0/include/cuecat.php $
# Last Updated: $Date: 2010-03-07 21:53:54 -0800 (Sun, 07 Mar 2010) $
# Author(s): Neil McNab
#
# Description:
#   Cuecat decode to normal UPC functions.
#
########################################################################

/*
http://osiris.978.org/~brianr/cuecat/files/cuecat-0.0.8/SUPPORTED_BARCODES

http://www.accipiter.org/projects/cat.php

http://uscan.sourceforge.net/upc.txt

divide the UPC into 4 groups of three digits
use the scheme below to translate each digit into its output



    1  2  3

0   C3 n  Z  
1   CN j  Y  
2   Cx f  X  
3   Ch b  W  
4   D3 D  3  
5   DN z  2  
6   Dx v  1  
7   Dh r  0  
8   E3 T  7  
9   EN P  6 

*/

$File3Name = basename($_SERVER['SCRIPT_NAME']);
if ($File3Name=="cuecat.php"||$File3Name=="/cuecat.php") {
	chdir("../");
	require("./upc.php");
	exit(); }

function cuecat_decode($ccstr) {
	$ccparts = explode(".", $ccstr);
	$upcstr = "";
	if ('fHmc' == $ccparts[2]) {
		// decode UPC-A
		$upcstr .= cuecat_decode_block(substr($ccparts[3], 0, 4));
		$upcstr .= cuecat_decode_block(substr($ccparts[3], 4, 4));
		$upcstr .= cuecat_decode_block(substr($ccparts[3], 8, 4));
		$upcstr .= cuecat_decode_block(substr($ccparts[3], 12, 4));
	} elseif ('fHmg' == $ccparts[2]) {
		// decode UPC-E
		$upcstr .= cuecat_decode_block(substr($ccparts[3], 0, 4));
		$upcstr .= cuecat_decode_block(substr($ccparts[3], 4, 4));
		$upcstr .= cuecat_decode_block(substr($ccparts[3], 8, 2));
                $upcstr = compute_check_digit($upcstr . 'X');
	}

	return $upcstr;
}

function cuecat_decode_block($ccblock) {
	$lookup1 = array(
		'' => '',
		'C3' => '0', 'CW' => '0',
		'CN' => '1',
		'Cx' => '2',
		'Ch' => '3',
		'D3' => '4',
		'DN' => '5',
		'Dx' => '6',
		'Dh' => '7',
		'E3' => '8',
		'EN' => '9',
	);
	$lookup2 = array(
		'' => '',
		'n' => '0',
		'j' => '1',
		'f' => '2',
		'b' => '3',
		'D' => '4',
		'z' => '5',
		'v' => '6',
		'r' => '7',
		'T' => '8',
		'P' => '9',
	);
	$lookup3 = array(
		'' => '',
		'Z' => '0',
		'Y' => '1',
		'X' => '2',
		'W' => '3',
		'3' => '4',
		'2' => '5',
		'1' => '6',
		'0' => '7',
		'7' => '8',
		'6' => '9',
	);

	$result = "";
	$result .= $lookup1[strval(substr($ccblock, 0, 2))];
	$result .= $lookup2[strval(substr($ccblock, 2, 1))];
	$result .= $lookup3[strval(substr($ccblock, 3, 1))];
	
	return $result;
}

//print cuecat_decode(".C3nZC3nZC3nYD3b6ENnZCNnY.fHmc.C3D1Dxr2C3nZE3n7.");
//print cuecat_decode(".C3nZC3nZC3nXC3v2Dhz6C3nX.fHmg.C3T0CxrYCW.");
//print cuecat_decode(".C3nZC3nZC3nXC3v2Dhz6C3nX.fHmg.C3bZDhr2CW.");

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
function validate_upca($upc,$return_check=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)>12||strlen($upc)<11) { return false; }
	if(strlen($upc)==11) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches); }
	if(strlen($upc)==12) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches); }
	$OddSum = ($upc_matches[1] + $upc_matches[3] + $upc_matches[5] + $upc_matches[7] + $upc_matches[9] + $upc_matches[11]) * 3;
	$EvenSum = $upc_matches[2] + $upc_matches[4] + $upc_matches[6] + $upc_matches[8] + $upc_matches[10];
	$AllSum = $OddSum + $EvenSum;
	$CheckSum = $AllSum % 10;
	if($CheckSum>0) {
	$CheckSum = 10 - $CheckSum; }
	if($return_check==false&&strlen($upc)==12) {
	if($CheckSum!=$upc_matches[12]) { return false; }
	if($CheckSum==$upc_matches[12]) { return true; } }
	if($return_check==true) { return $CheckSum; } 
	if(strlen($upc)==11) { return $CheckSum; } }

function validate_ean13($upc,$return_check=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)>13||strlen($upc)<12) { return false; }
	if(strlen($upc)==12) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches); }
	if(strlen($upc)==13) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches); }
	$EvenSum = ($upc_matches[2] + $upc_matches[4] + $upc_matches[6] + $upc_matches[8] + $upc_matches[10] + $upc_matches[12]) * 3;
	$OddSum = $upc_matches[1] + $upc_matches[3] + $upc_matches[5] + $upc_matches[7] + $upc_matches[9] + $upc_matches[11];
	$AllSum = $OddSum + $EvenSum;
	$CheckSum = $AllSum % 10;
	if($CheckSum>0) {
	$CheckSum = 10 - $CheckSum; }
	if($return_check==false&&strlen($upc)==13) {
	if($CheckSum!=$upc_matches[13]) { return false; }
	if($CheckSum==$upc_matches[13]) { return true; } }
	if($return_check==true) { return $CheckSum; }
	if(strlen($upc)==12) { return $CheckSum; } }

function validate_itf14($upc,$return_check=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)>14||strlen($upc)<13) { return false; }
	if(strlen($upc)==13) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches); }
	if(strlen($upc)==14) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches); }
	$EvenSum = $upc_matches[2] + $upc_matches[4] + $upc_matches[6] + $upc_matches[8] + $upc_matches[10] + $upc_matches[12];
	$OddSum = ($upc_matches[1] + $upc_matches[3] + $upc_matches[5] + $upc_matches[7] + $upc_matches[9] + $upc_matches[11] + $upc_matches[13]) * 3;
	$AllSum = $OddSum + $EvenSum;
	$CheckSum = $AllSum % 10;
	if($CheckSum>0) {
	$CheckSum = 10 - $CheckSum; }
	if($return_check==false&&strlen($upc)==14) {
	if($CheckSum!=$upc_matches[14]) { return false; }
	if($CheckSum==$upc_matches[14]) { return true; } }
	if($return_check==true) { return $CheckSum; }
	if(strlen($upc)==13) { return $CheckSum; } }

function validate_ean8($upc,$return_check=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)>8||strlen($upc)<7) { return false; }
	if(strlen($upc)==7) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches); }
	if(strlen($upc)==8) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches); }
	$EvenSum = ($upc_matches[1] + $upc_matches[3] + $upc_matches[5] + $upc_matches[7]) * 3;
	$OddSum = $upc_matches[2] + $upc_matches[4] + $upc_matches[6];
	$AllSum = $OddSum + $EvenSum;
	$CheckSum = $AllSum % 10;
	if($CheckSum>0) {
	$CheckSum = 10 - $CheckSum; }
	if($return_check==false&&strlen($upc)==8) {
	if($CheckSum!=$upc_matches[8]) { return false; }
	if($CheckSum==$upc_matches[8]) { return true; } }
	if($return_check==true) { return $CheckSum; }
	if(strlen($upc)==7) { return $CheckSum; } }

function validate_upce($upc,$return_check=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)>8||strlen($upc)<7) { return false; }
	if(!preg_match("/^0/", $upc)) { return false; }
	$CheckDigit = null;
	if(strlen($upc)==8&&preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches)) {
	preg_match("/^(\d{7})(\d{1})/", $upc, $upc_matches);
	$CheckDigit = $upc_matches[2]; }
	if(preg_match("/^(\d{1})(\d{5})([0-3])/", $upc, $upc_matches)) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches);
	if($upc_matches[7]==0) {
	$OddSum = (0 + $upc_matches[3] + 0 + 0 + $upc_matches[4] + $upc_matches[6]) * 3;
	$EvenSum = $upc_matches[2] + 0 + 0 + 0 + $upc_matches[5]; }
	if($upc_matches[7]==1) {
	$OddSum = (0 + $upc_matches[3] + 0 + 0 + $upc_matches[4] + $upc_matches[6]) * 3;
	$EvenSum = $upc_matches[2] + 1 + 0 + 0 + $upc_matches[5]; }
	if($upc_matches[7]==2) {
	$OddSum = (0 + $upc_matches[3] + 0 + 0 + $upc_matches[4] + $upc_matches[6]) * 3;
	$EvenSum = $upc_matches[2] + 2 + 0 + 0 + $upc_matches[5]; }
	if($upc_matches[7]==3) {
	$OddSum = (0 + $upc_matches[3] + 0 + 0 + 0 + $upc_matches[6]) * 3;
	$EvenSum = $upc_matches[2] + $upc_matches[4] + 0 + 0 + $upc_matches[5]; } }
	if(preg_match("/^(\d{1})(\d{5})([4-9])/", $upc, $upc_matches)) {
	preg_match("/^(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches);
	if($upc_matches[7]==4) {
	$OddSum = (0 + $upc_matches[3] + $upc_matches[5] + 0 + 0 + $upc_matches[6]) * 3;
	$EvenSum = $upc_matches[2] + $upc_matches[4] + 0 + 0 + 0; }
	if($upc_matches[7]==5) {
	$OddSum = (0 + $upc_matches[3] + $upc_matches[5] + 0 + 0 + $upc_matches[7]) * 3;
	$EvenSum = $upc_matches[2] + $upc_matches[4] + $upc_matches[6] + 0 + 0; }
	if($upc_matches[7]==6) {
	$OddSum = (0 + $upc_matches[3] + $upc_matches[5] + 0 + 0 + $upc_matches[7]) * 3;
	$EvenSum = $upc_matches[2] + $upc_matches[4] + $upc_matches[6] + 0 + 0; }
	if($upc_matches[7]==7) {
	$OddSum = (0 + $upc_matches[3] + $upc_matches[5] + 0 + 0 + $upc_matches[7]) * 3;
	$EvenSum = $upc_matches[2] + $upc_matches[4] + $upc_matches[6] + 0 + 0; }
	if($upc_matches[7]==8) {
	$OddSum = (0 + $upc_matches[3] + $upc_matches[5] + 0 + 0 + $upc_matches[7]) * 3;
	$EvenSum = $upc_matches[2] + $upc_matches[4] + $upc_matches[6] + 0 + 0; }
	if($upc_matches[7]==9) {
	$OddSum = (0 + $upc_matches[3] + $upc_matches[5] + 0 + 0 + $upc_matches[7]) * 3;
	$EvenSum = $upc_matches[2] + $upc_matches[4] + $upc_matches[6] + 0 + 0; } }
	$AllSum = $OddSum + $EvenSum;
	$CheckSum = $AllSum % 10;
	if($CheckSum>0) {
	$CheckSum = 10 - $CheckSum; }
	if($return_check==false&&strlen($upc)==8) {
	if($CheckSum!=$CheckDigit) { return false; }
	if($CheckSum==$CheckDigit) { return true; } }
	if($return_check==true) { return $CheckSum; } 
	if(strlen($upc)==7) { return $CheckSum; } }

// Code for converting UPC/EAN by Kazuki Przyborowski
function convert_upce_to_upca($upc) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==7) { $upc = $upc.validate_upce($upc,true); }
	if(strlen($upc)>8||strlen($upc)<8) { return false; }
	if(!preg_match("/^0/", $upc)) { return false; }
	if(preg_match("/0(\d{5})([0-3])(\d{1})/", $upc, $upc_matches)) {
	$upce_test = preg_match("/0(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches);
	if($upce_test==false) { return false; }
	if($upc_matches[6]==0) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[6]."0000".$upc_matches[3].$upc_matches[4].$upc_matches[5].$upc_matches[7]; }
	if($upc_matches[6]==1) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[6]."0000".$upc_matches[3].$upc_matches[4].$upc_matches[5].$upc_matches[7]; }
	if($upc_matches[6]==2) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[6]."0000".$upc_matches[3].$upc_matches[4].$upc_matches[5].$upc_matches[7]; }
	if($upc_matches[6]==3) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3]."00000".$upc_matches[4].$upc_matches[5].$upc_matches[7]; } }
	if(preg_match("/0(\d{5})([4-9])(\d{1})/", $upc, $upc_matches)) {
	preg_match("/0(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})(\d{1})/", $upc, $upc_matches);
	if($upc_matches[6]==4) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4]."00000".$upc_matches[5].$upc_matches[7]; }
	if($upc_matches[6]==5) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; }
	if($upc_matches[6]==6) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; }
	if($upc_matches[6]==7) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; }
	if($upc_matches[6]==8) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; }
	if($upc_matches[6]==9) {
	$upce = "0".$upc_matches[1].$upc_matches[2].$upc_matches[3].$upc_matches[4].$upc_matches[5]."0000".$upc_matches[6].$upc_matches[7]; } }
	return $upce; }

function convert_upca_to_ean13($upc) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==11) { $upc = $upc.validate_upca($upc,true); }
	if(strlen($upc)>13||strlen($upc)<12) { return false; }
	if(strlen($upc)==12) { $ean13 = "0".$upc; }
	if(strlen($upc)==13) { $ean13 = $upc; }
	return $ean13; }

function convert_ean13_to_itf14($upc) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==11) { $upc = $upc.validate_upca($upc,true); }
	if(strlen($upc)==12) { $upc = "0".$upc; }
	if(strlen($upc)>14||strlen($upc)<13) { return false; }
	if(strlen($upc)==13) { $itf14 = "0".$upc; }
	if(strlen($upc)==14) { $itf14 = $upc; }
	return $itf14; }

function convert_upce_to_ean13($upc) {
	return convert_upca_to_ean13(convert_upce_to_upca($upc)); }

function convert_ean13_to_upca($upc) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==12) { $upc = "0".$upc; }
	if(strlen($upc)>13||strlen($upc)<13) { return false; }
	if(preg_match("/^0(\d{12})/", $upc, $upc_matches)) {
	$upca = $upc_matches[1]; }
	if(!preg_match("/^0(\d{12})/", $upc, $upc_matches)) {
	return false; }
	return $upca; }

function convert_itf14_to_ean13($upc) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==13) { $upc = "0".$upc; }
	if(strlen($upc)>14||strlen($upc)<14) { return false; }
	if(preg_match("/^0(\d{13})/", $upc, $upc_matches)) {
	$ean13 = $upc_matches[1]; }
	if(!preg_match("/^0(\d{13})/", $upc, $upc_matches)) {
	return false; }
	return $ean13; }

function convert_upca_to_upce($upc) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==11) { $upc = $upc.validate_upca($upc,true); }
	if(strlen($upc)>12||strlen($upc)<12) { return false; }
	if(!preg_match("/0(\d{11})/", $upc)) { return false; }
	$upce = null;
	if(preg_match("/0(\d{2})00000(\d{3})(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1].$upc_matches[2]."0";
	$upce = $upce.$upc_matches[3]; return $upce; }
	if(preg_match("/0(\d{2})10000(\d{3})(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1].$upc_matches[2]."1";
	$upce = $upce.$upc_matches[3]; return $upce; }
	if(preg_match("/0(\d{2})20000(\d{3})(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1].$upc_matches[2]."2";
	$upce = $upce.$upc_matches[3]; return $upce; }
	if(preg_match("/0(\d{3})00000(\d{2})(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1].$upc_matches[2]."3";
	$upce = $upce.$upc_matches[3]; return $upce; }
	if(preg_match("/0(\d{4})00000(\d{1})(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1].$upc_matches[2]."4";
	$upce = $upce.$upc_matches[3]; return $upce; }
	if(preg_match("/0(\d{5})00005(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1]."5";
	$upce = $upce.$upc_matches[2]; return $upce; }
	if(preg_match("/0(\d{5})00006(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1]."6";
	$upce = $upce.$upc_matches[2]; return $upce; }
	if(preg_match("/0(\d{5})00007(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1]."7";
	$upce = $upce.$upc_matches[2]; return $upce; }
	if(preg_match("/0(\d{5})00008(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1]."8";
	$upce = $upce.$upc_matches[2]; return $upce; }
	if(preg_match("/0(\d{5})00009(\d{1})/", $upc, $upc_matches)) {
	$upce = "0".$upc_matches[1]."9";
	$upce = $upce.$upc_matches[2]; return $upce; }
	if($upce==null) { return false; }
	return $upce; }

function convert_ean13_to_upce($upc) {
	return convert_upca_to_upce(convert_ean13_to_upca($upc)); }

function convert_itf14_to_upca($upc) {
	return convert_ean13_to_upca(convert_itf14_to_ean13($upc)); }

function convert_itf14_to_upce($upc) {
	return convert_upca_to_upce(convert_itf14_to_upca($upc)); }

// Code for making EAN-2 supplement by Kazuki Przyborowski
function create_ean2($upc,$offsetadd,$imgres) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)>2||strlen($upc)<2) { return false; }
	preg_match("/(\d{2})/", $upc, $upc_matches);
	if(count($upc_matches)<=0) { return false; }
	$CheckSum = $upc_matches[1] % 4;
	$LeftDigit = str_split($upc_matches[1]);
	$text_color = imagecolorallocate($imgres, 0, 0, 0);
	$alt_text_color = imagecolorallocate($imgres, 255, 255, 255);
	imagestring($imgres, 2, 5 + $offsetadd, 47, $LeftDigit[0], $text_color);
	imagestring($imgres, 2, 13 + $offsetadd, 47, $LeftDigit[1], $text_color);
	imageline($imgres, 0 + $offsetadd, 10, 0 + $offsetadd, 47, $alt_text_color);
	imageline($imgres, 1 + $offsetadd, 10, 1 + $offsetadd, 47, $text_color);
	imageline($imgres, 2 + $offsetadd, 10, 2 + $offsetadd, 47, $alt_text_color);
	imageline($imgres, 3 + $offsetadd, 10, 3 + $offsetadd, 47, $text_color);
	imageline($imgres, 4 + $offsetadd, 10, 4 + $offsetadd, 47, $text_color);
	$NumZero = 0; $LineStart = 5 + $offsetadd;
	while ($NumZero < count($LeftDigit)) {
		$LineSize = 47;
		$left_text_color_l = array(0, 0, 0, 0, 0, 0, 0); 
		$left_text_color_g = array(1, 1, 1, 1, 1, 1, 1);
		if($LeftDigit[$NumZero]==0) { 
		$left_text_color_l = array(0, 0, 0, 1, 1, 0, 1); 
		$left_text_color_g = array(0, 1, 0, 0, 1, 1, 1); }
		if($LeftDigit[$NumZero]==1) { 
		$left_text_color_l = array(0, 0, 1, 1, 0, 0, 1); 
		$left_text_color_g = array(0, 1, 1, 0, 0, 1, 1); }
		if($LeftDigit[$NumZero]==2) { 
		$left_text_color_l = array(0, 0, 1, 0, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 1, 0, 1, 1); }
		if($LeftDigit[$NumZero]==3) { 
		$left_text_color_l = array(0, 1, 1, 1, 1, 0, 1); 
		$left_text_color_g = array(0, 1, 0, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==4) { 
		$left_text_color_l = array(0, 1, 0, 0, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 1, 1, 0, 1); }
		if($LeftDigit[$NumZero]==5) { 
		$left_text_color_l = array(0, 1, 1, 0, 0, 0, 1); 
		$left_text_color_g = array(0, 1, 1, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==6) { 
		$left_text_color_l = array(0, 1, 0, 1, 1, 1, 1); 
		$left_text_color_g = array(0, 0, 0, 0, 1, 0, 1); }
		if($LeftDigit[$NumZero]==7) { 
		$left_text_color_l = array(0, 1, 1, 1, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==8) { 
		$left_text_color_l = array(0, 1, 1, 0, 1, 1, 1); 
		$left_text_color_g = array(0, 0, 0, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==9) { 
		$left_text_color_l = array(0, 0, 0, 1, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 0, 1, 1, 1); }
		$left_text_color = $left_text_color_l;
		if($CheckSum==0&&$NumZero==0) { $left_text_color = $left_text_color_l; }
		if($CheckSum==0&&$NumZero==1) { $left_text_color = $left_text_color_l; }
		if($CheckSum==1&&$NumZero==0) { $left_text_color = $left_text_color_l; }
		if($CheckSum==1&&$NumZero==1) { $left_text_color = $left_text_color_g; }
		if($CheckSum==2&&$NumZero==0) { $left_text_color = $left_text_color_g; }
		if($CheckSum==2&&$NumZero==1) { $left_text_color = $left_text_color_l; }
		if($CheckSum==3&&$NumZero==0) { $left_text_color = $left_text_color_g; }
		if($CheckSum==3&&$NumZero==1) { $left_text_color = $left_text_color_g; }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($imgres, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($imgres, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		if($NumZero == 0) {
		imageline($imgres, $LineStart, 10, $LineStart, $LineSize, $alt_text_color);
		$LineStart += 1;
		imageline($imgres, $LineStart, 10, $LineStart, $LineSize, $text_color);
		$LineStart += 1; }
		++$NumZero; }
	return true; }

// Code for making EAN-5 supplement by Kazuki Przyborowski
function create_ean5($upc,$offsetadd,$imgres) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)>5||strlen($upc)<5) { return false; }
	preg_match("/(\d{5})/", $upc, $upc_matches);
	if(count($upc_matches)<=0) { return false; }
	$LeftDigit = str_split($upc_matches[1]);
	$CheckSum = ($LeftDigit[0] * 3) + ($LeftDigit[1] * 9) + ($LeftDigit[2] * 3) + ($LeftDigit[3] * 9) + ($LeftDigit[4] * 3);
	$CheckSum = $CheckSum % 10;
	$imgres = imagecreatetruecolor(48, 62);
	imagefilledrectangle($imgres, 0, 0, 48, 62, 0xFFFFFF);
	imageinterlace($imgres, true);
	$background_color = imagecolorallocate($imgres, 255, 255, 255);
	$text_color = imagecolorallocate($imgres, 0, 0, 0);
	$alt_text_color = imagecolorallocate($imgres, 255, 255, 255);
	imagestring($imgres, 2, 7 + $offsetadd, 47, $LeftDigit[0], $text_color);
	imagestring($imgres, 2, 16 + $offsetadd, 47, $LeftDigit[1], $text_color);
	imagestring($imgres, 2, 24 + $offsetadd, 47, $LeftDigit[2], $text_color);
	imagestring($imgres, 2, 32 + $offsetadd, 47, $LeftDigit[3], $text_color);
	imagestring($imgres, 2, 40 + $offsetadd, 47, $LeftDigit[3], $text_color);
	imageline($imgres, 0 + $offsetadd, 10, 0 + $offsetadd, 47, $alt_text_color);
	imageline($imgres, 1 + $offsetadd, 10, 1 + $offsetadd, 47, $text_color);
	imageline($imgres, 2 + $offsetadd, 10, 2 + $offsetadd, 47, $alt_text_color);
	imageline($imgres, 3 + $offsetadd, 10, 3 + $offsetadd, 47, $text_color);
	imageline($imgres, 4 + $offsetadd, 10, 4 + $offsetadd, 47, $text_color);
	$NumZero = 0; $LineStart = 5 + $offsetadd;
	while ($NumZero < count($LeftDigit)) {
		$LineSize = 47;
		$left_text_color_l = array(0, 0, 0, 0, 0, 0, 0); 
		$left_text_color_g = array(1, 1, 1, 1, 1, 1, 1);
		if($LeftDigit[$NumZero]==0) { 
		$left_text_color_l = array(0, 0, 0, 1, 1, 0, 1); 
		$left_text_color_g = array(0, 1, 0, 0, 1, 1, 1); }
		if($LeftDigit[$NumZero]==1) { 
		$left_text_color_l = array(0, 0, 1, 1, 0, 0, 1); 
		$left_text_color_g = array(0, 1, 1, 0, 0, 1, 1); }
		if($LeftDigit[$NumZero]==2) { 
		$left_text_color_l = array(0, 0, 1, 0, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 1, 0, 1, 1); }
		if($LeftDigit[$NumZero]==3) { 
		$left_text_color_l = array(0, 1, 1, 1, 1, 0, 1); 
		$left_text_color_g = array(0, 1, 0, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==4) { 
		$left_text_color_l = array(0, 1, 0, 0, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 1, 1, 0, 1); }
		if($LeftDigit[$NumZero]==5) { 
		$left_text_color_l = array(0, 1, 1, 0, 0, 0, 1); 
		$left_text_color_g = array(0, 1, 1, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==6) { 
		$left_text_color_l = array(0, 1, 0, 1, 1, 1, 1); 
		$left_text_color_g = array(0, 0, 0, 0, 1, 0, 1); }
		if($LeftDigit[$NumZero]==7) { 
		$left_text_color_l = array(0, 1, 1, 1, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==8) { 
		$left_text_color_l = array(0, 1, 1, 0, 1, 1, 1); 
		$left_text_color_g = array(0, 0, 0, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==9) { 
		$left_text_color_l = array(0, 0, 0, 1, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 0, 1, 1, 1); }
		$left_text_color = $left_text_color_l;
		if($CheckSum==0&&$NumZero==0) { $left_text_color = $left_text_color_g; }
		if($CheckSum==0&&$NumZero==1) { $left_text_color = $left_text_color_g; }
		if($CheckSum==0&&$NumZero==2) { $left_text_color = $left_text_color_l; }
		if($CheckSum==0&&$NumZero==3) { $left_text_color = $left_text_color_l; }
		if($CheckSum==0&&$NumZero==4) { $left_text_color = $left_text_color_l; }
		if($CheckSum==1&&$NumZero==0) { $left_text_color = $left_text_color_g; }
		if($CheckSum==1&&$NumZero==1) { $left_text_color = $left_text_color_l; }
		if($CheckSum==1&&$NumZero==2) { $left_text_color = $left_text_color_g; }
		if($CheckSum==1&&$NumZero==3) { $left_text_color = $left_text_color_l; }
		if($CheckSum==1&&$NumZero==4) { $left_text_color = $left_text_color_l; }
		if($CheckSum==2&&$NumZero==0) { $left_text_color = $left_text_color_g; }
		if($CheckSum==2&&$NumZero==1) { $left_text_color = $left_text_color_l; }
		if($CheckSum==2&&$NumZero==2) { $left_text_color = $left_text_color_l; }
		if($CheckSum==2&&$NumZero==3) { $left_text_color = $left_text_color_g; }
		if($CheckSum==2&&$NumZero==4) { $left_text_color = $left_text_color_l; }
		if($CheckSum==3&&$NumZero==0) { $left_text_color = $left_text_color_g; }
		if($CheckSum==3&&$NumZero==1) { $left_text_color = $left_text_color_l; }
		if($CheckSum==3&&$NumZero==2) { $left_text_color = $left_text_color_l; }
		if($CheckSum==3&&$NumZero==3) { $left_text_color = $left_text_color_l; }
		if($CheckSum==3&&$NumZero==4) { $left_text_color = $left_text_color_g; }
		if($CheckSum==4&&$NumZero==0) { $left_text_color = $left_text_color_l; }
		if($CheckSum==4&&$NumZero==1) { $left_text_color = $left_text_color_g; }
		if($CheckSum==4&&$NumZero==2) { $left_text_color = $left_text_color_g; }
		if($CheckSum==4&&$NumZero==3) { $left_text_color = $left_text_color_l; }
		if($CheckSum==4&&$NumZero==4) { $left_text_color = $left_text_color_l; }
		if($CheckSum==5&&$NumZero==0) { $left_text_color = $left_text_color_l; }
		if($CheckSum==5&&$NumZero==1) { $left_text_color = $left_text_color_l; }
		if($CheckSum==5&&$NumZero==2) { $left_text_color = $left_text_color_g; }
		if($CheckSum==5&&$NumZero==3) { $left_text_color = $left_text_color_g; }
		if($CheckSum==5&&$NumZero==4) { $left_text_color = $left_text_color_l; }
		if($CheckSum==6&&$NumZero==0) { $left_text_color = $left_text_color_l; }
		if($CheckSum==6&&$NumZero==1) { $left_text_color = $left_text_color_l; }
		if($CheckSum==6&&$NumZero==2) { $left_text_color = $left_text_color_l; }
		if($CheckSum==6&&$NumZero==3) { $left_text_color = $left_text_color_g; }
		if($CheckSum==6&&$NumZero==4) { $left_text_color = $left_text_color_g; }
		if($CheckSum==7&&$NumZero==0) { $left_text_color = $left_text_color_l; }
		if($CheckSum==7&&$NumZero==1) { $left_text_color = $left_text_color_g; }
		if($CheckSum==7&&$NumZero==2) { $left_text_color = $left_text_color_l; }
		if($CheckSum==7&&$NumZero==3) { $left_text_color = $left_text_color_g; }
		if($CheckSum==7&&$NumZero==4) { $left_text_color = $left_text_color_l; }
		if($CheckSum==8&&$NumZero==0) { $left_text_color = $left_text_color_l; }
		if($CheckSum==8&&$NumZero==1) { $left_text_color = $left_text_color_g; }
		if($CheckSum==8&&$NumZero==2) { $left_text_color = $left_text_color_l; }
		if($CheckSum==8&&$NumZero==3) { $left_text_color = $left_text_color_l; }
		if($CheckSum==8&&$NumZero==4) { $left_text_color = $left_text_color_g; }
		if($CheckSum==9&&$NumZero==0) { $left_text_color = $left_text_color_l; }
		if($CheckSum==9&&$NumZero==1) { $left_text_color = $left_text_color_l; }
		if($CheckSum==9&&$NumZero==2) { $left_text_color = $left_text_color_g; }
		if($CheckSum==9&&$NumZero==3) { $left_text_color = $left_text_color_l; }
		if($CheckSum==9&&$NumZero==4) { $left_text_color = $left_text_color_g; }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($imgres, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($imgres, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		if($NumZero < 5) {
		imageline($imgres, $LineStart, 10, $LineStart, $LineSize, $alt_text_color);
		$LineStart += 1;
		imageline($imgres, $LineStart, 10, $LineStart, $LineSize, $text_color);
		$LineStart += 1; }
		++$NumZero; }
	return true; }

// Code for making UPC-A by Kazuki Przyborowski
function create_upca($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)) { return false; }
	$upc_pieces = null; $supplement = null;
	if(strlen($upc)==15) { $upc_pieces = explode(" ", $upc); }
	if(strlen($upc)==16) { $upc_pieces = explode(" ", $upc); }
	if(strlen($upc)==18) { $upc_pieces = explode(" ", $upc); }
	if(strlen($upc)==19) { $upc_pieces = explode(" ", $upc); }
	if(count($upc_pieces)>1) { $upc = $upc_pieces[0]; $supplement = $upc_pieces[1]; }
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(isset($supplement)&&!is_numeric($supplement)) { return false; }
	if(strlen($upc)==8) { $upc = convert_upce_to_upca($upc); }
	if(strlen($upc)==13) { $upc = convert_ean13_to_upca($upc); }
	if(strlen($upc)==11) { $upc = $upc.validate_upca($upc,true); }
	if(strlen($upc)>12||strlen($upc)<12) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if(validate_upca($upc)===false) { return false; }
	if($imgtype!="png"&&$imgtype!="gif"&&$imgtype!="xbm"&&$imgtype!="wbmp") { $imgtype = "png"; }
	preg_match("/(\d{1})(\d{5})(\d{5})(\d{1})/", $upc, $upc_matches);
	if(count($upc_matches)<=0) { return false; }
	$PrefixDigit = $upc_matches[1];
	$LeftDigit = str_split($upc_matches[2]);
	array_unshift($LeftDigit, $upc_matches[1]);
	$RightDigit = str_split($upc_matches[3]);
	array_push($RightDigit, $upc_matches[4]);
	$CheckDigit = $upc_matches[4];
	if($imgtype=="png") {
	if($outputimage==true) {
	/*header("Content-Type: image/png");*/ } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	/*header("Content-Type: image/gif");*/ } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	/*header("Content-Type: image/x-xbitmap");*/ } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	/*header("Content-Type: image/vnd.wap.wbmp");*/ } }
	$addonsize = 0;
	if(strlen($supplement)==2) { $addonsize = 29; }
	if(strlen($supplement)==5) { $addonsize = 56; }
	$upc_img = imagecreatetruecolor(113 + $addonsize, 62);
	imagefilledrectangle($upc_img, 0, 0, 113 + $addonsize, 62, 0xFFFFFF);
	imageinterlace($upc_img, true);
	$background_color = imagecolorallocate($upc_img, 255, 255, 255);
	$text_color = imagecolorallocate($upc_img, 0, 0, 0);
	$alt_text_color = imagecolorallocate($upc_img, 255, 255, 255);
	imagestring($upc_img, 2, 2, 47, $upc_matches[1], $text_color);
	imagestring($upc_img, 2, 22, 47, $upc_matches[2], $text_color);
	imagestring($upc_img, 2, 61, 47, $upc_matches[3], $text_color);
	if($hidecd!==true) {
	imagestring($upc_img, 2, 106, 47, $upc_matches[4], $text_color); }
	imageline($upc_img, 0, 10, 0, 47, $alt_text_color);
	imageline($upc_img, 1, 10, 1, 47, $alt_text_color);
	imageline($upc_img, 2, 10, 2, 47, $alt_text_color);
	imageline($upc_img, 3, 10, 3, 47, $alt_text_color);
	imageline($upc_img, 4, 10, 4, 47, $alt_text_color);
	imageline($upc_img, 5, 10, 5, 47, $alt_text_color);
	imageline($upc_img, 6, 10, 6, 47, $alt_text_color);
	imageline($upc_img, 7, 10, 7, 47, $alt_text_color);
	imageline($upc_img, 8, 10, 8, 47, $alt_text_color);
	imageline($upc_img, 9, 10, 9, 51, $text_color);
	imageline($upc_img, 10, 10, 10, 51, $alt_text_color);
	imageline($upc_img, 11, 10, 11, 51, $text_color);
	$NumZero = 0; $LineStart = 12;
	while ($NumZero < count($LeftDigit)) {
		if($NumZero!=0) { $LineSize = 47; }
		if($NumZero==0) { $LineSize = 51; }
		$left_text_color = array(0, 0, 0, 0, 0, 0, 0);
		if($LeftDigit[$NumZero]==0) { 
		$left_text_color = array(0, 0, 0, 1, 1, 0, 1); }
		if($LeftDigit[$NumZero]==1) { 
		$left_text_color = array(0, 0, 1, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==2) { 
		$left_text_color = array(0, 0, 1, 0, 0, 1, 1); }
		if($LeftDigit[$NumZero]==3) { 
		$left_text_color = array(0, 1, 1, 1, 1, 0, 1); }
		if($LeftDigit[$NumZero]==4) { 
		$left_text_color = array(0, 1, 0, 0, 0, 1, 1); }
		if($LeftDigit[$NumZero]==5) { 
		$left_text_color = array(0, 1, 1, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==6) { 
		$left_text_color = array(0, 1, 0, 1, 1, 1, 1); }
		if($LeftDigit[$NumZero]==7) { 
		$left_text_color = array(0, 1, 1, 1, 0, 1, 1); }
		if($LeftDigit[$NumZero]==8) { 
		$left_text_color = array(0, 1, 1, 0, 1, 1, 1); }
		if($LeftDigit[$NumZero]==9) {
		$left_text_color = array(0, 0, 0, 1, 0, 1, 1); }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 54, 10, 54, 51, $alt_text_color);
	imageline($upc_img, 55, 10, 55, 51, $text_color);
	imageline($upc_img, 56, 10, 56, 51, $alt_text_color);
	imageline($upc_img, 57, 10, 57, 51, $text_color);
	imageline($upc_img, 58, 10, 58, 51, $alt_text_color);
	$NumZero = 0; $LineStart = 59;
	while ($NumZero < count($RightDigit)) {
		if($NumZero!=5) { $LineSize = 47; }
		if($NumZero==5) { $LineSize = 51; }
		$right_text_color = array(0, 0, 0, 0, 0, 0, 0);
		if($RightDigit[$NumZero]==0) { 
		$right_text_color = array(1, 1, 1, 0, 0, 1, 0); }
		if($RightDigit[$NumZero]==1) { 
		$right_text_color = array(1, 1, 0, 0, 1, 1, 0); }
		if($RightDigit[$NumZero]==2) { 
		$right_text_color = array(1, 1, 0, 1, 1, 0, 0); }
		if($RightDigit[$NumZero]==3) { 
		$right_text_color = array(1, 0, 0, 0, 0, 1, 0); }
		if($RightDigit[$NumZero]==4) { 
		$right_text_color = array(1, 0, 1, 1, 1, 0, 0); }
		if($RightDigit[$NumZero]==5) { 
		$right_text_color = array(1, 0, 0, 1, 1, 1, 0); }
		if($RightDigit[$NumZero]==6) { 
		$right_text_color = array(1, 0, 1, 0, 0, 0, 0); }
		if($RightDigit[$NumZero]==7) { 
		$right_text_color = array(1, 0, 0, 0, 1, 0, 0); }
		if($RightDigit[$NumZero]==8) { 
		$right_text_color = array(1, 0, 0, 1, 0, 0, 0); }
		if($RightDigit[$NumZero]==9) { 
		$right_text_color = array(1, 1, 1, 0, 1, 0, 0); }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($right_text_color)) {
		if($right_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($right_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 101, 10, 101, 51, $text_color);
	imageline($upc_img, 102, 10, 102, 51, $alt_text_color);
	imageline($upc_img, 103, 10, 103, 51, $text_color);
	imageline($upc_img, 104, 10, 104, 47, $alt_text_color);
	imageline($upc_img, 105, 10, 105, 47, $alt_text_color);
	imageline($upc_img, 106, 10, 106, 47, $alt_text_color);
	imageline($upc_img, 107, 10, 107, 47, $alt_text_color);
	imageline($upc_img, 108, 10, 108, 47, $alt_text_color);
	imageline($upc_img, 109, 10, 109, 47, $alt_text_color);
	imageline($upc_img, 110, 10, 110, 47, $alt_text_color);
	imageline($upc_img, 111, 10, 111, 47, $alt_text_color);
	imageline($upc_img, 112, 10, 112, 47, $alt_text_color);
	if(strlen($supplement)==2) { create_ean2($supplement,113,$upc_img); }
	if(strlen($supplement)==5) { create_ean2($supplement,113,$upc_img); }
	if($resize>1) {
	$new_upc_img = imagecreatetruecolor((113 + $addonsize) * $resize, 62 * $resize);
	imagefilledrectangle($new_upc_img, 0, 0, (113 + $addonsize) * $resize, 62 * $resize, 0xFFFFFF);
	imageinterlace($new_upc_img, true);
	if($resizetype=="resize") {
	imagecopyresized($new_upc_img, $upc_img, 0, 0, 0, 0, (113 + $addonsize) * $resize, 62 * $resize, 113 + $addonsize, 62); }
	if($resizetype=="resample") {
	imagecopyresampled($new_upc_img, $upc_img, 0, 0, 0, 0, (113 + $addonsize) * $resize, 62 * $resize, 113 + $addonsize, 62); }
	imagedestroy($upc_img); 
	$upc_img = $new_upc_img; }
	if($imgtype=="png") {
	if($outputimage==true) {
	imagepng($upc_img); }
	if($outfile!=null) {
	imagepng($upc_img,$outfile); } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	imagegif($upc_img); }
	if($outfile!=null) {
	imagegif($upc_img,$outfile); } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	imagexbm($upc_img,NULL); }
	if($outfile!=null) {
	imagexbm($upc_img,$outfile); } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	imagewbmp($upc_img); }
	if($outfile!=null) {
	imagewbmp($upc_img,$outfile); } }
	imagedestroy($upc_img); 
	return true; }

// Code for making UPC-E by Kazuki Przyborowski
function create_upce($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==12) { $upc = convert_upca_to_upce($upc); }
	if(strlen($upc)==13) { $upc = convert_ean13_to_upce($upc); }
	if(strlen($upc)==7) { $upc = $upc.validate_upce($upc,true); }
	if(strlen($upc)>8||strlen($upc)<8) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if(!preg_match("/^0/", $upc)) { return false; }
	if(validate_upce($upc)===false) { return false; }
	if($imgtype!="png"&&$imgtype!="gif"&&$imgtype!="xbm"&&$imgtype!="wbmp") { $imgtype = "png"; }
	preg_match("/(\d{1})(\d{6})(\d{1})/", $upc, $upc_matches);
	if(count($upc_matches)<=0) { return false; }
	if($upc_matches[1]>1) { return false; }
	$PrefixDigit = $upc_matches[1];
	$LeftDigit = str_split($upc_matches[2]);
	$CheckDigit = $upc_matches[3];
	if($imgtype=="png") {
	if($outputimage==true) {
	/*header("Content-Type: image/png");*/ } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	/*header("Content-Type: image/gif");*/ } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	/*header("Content-Type: image/x-xbitmap");*/ } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	/*header("Content-Type: image/vnd.wap.wbmp");*/ } }
	$upc_img = imagecreatetruecolor(69, 62);
	imagefilledrectangle($upc_img, 0, 0, 69, 62, 0xFFFFFF);
	imageinterlace($upc_img, true);
	$background_color = imagecolorallocate($upc_img, 255, 255, 255);
	$text_color = imagecolorallocate($upc_img, 0, 0, 0);
	$alt_text_color = imagecolorallocate($upc_img, 255, 255, 255);
	imagestring($upc_img, 2, 2, 47, $upc_matches[1], $text_color);
	imagestring($upc_img, 2, 16, 47, $upc_matches[2], $text_color);
	if($hidecd!==true) {
	imagestring($upc_img, 2, 62, 47, $upc_matches[3], $text_color); }
	imageline($upc_img, 0, 10, 0, 47, $alt_text_color);
	imageline($upc_img, 1, 10, 1, 47, $alt_text_color);
	imageline($upc_img, 2, 10, 2, 47, $alt_text_color);
	imageline($upc_img, 3, 10, 3, 47, $alt_text_color);
	imageline($upc_img, 4, 10, 4, 47, $alt_text_color);
	imageline($upc_img, 5, 10, 5, 47, $alt_text_color);
	imageline($upc_img, 6, 10, 6, 47, $alt_text_color);
	imageline($upc_img, 7, 10, 7, 47, $alt_text_color);
	imageline($upc_img, 8, 10, 8, 47, $alt_text_color);
	imageline($upc_img, 9, 10, 9, 51, $text_color);
	imageline($upc_img, 10, 10, 10, 51, $alt_text_color);
	imageline($upc_img, 11, 10, 11, 51, $text_color);
	$NumZero = 0; $LineStart = 12;
	while ($NumZero < count($LeftDigit)) {
		$LineSize = 47;
		$left_text_color = array(0, 0, 0, 0, 0, 0, 0);
		$left_text_color_odd = array(0, 0, 0, 0, 0, 0, 0);
		$left_text_color_even = array(0, 0, 0, 0, 0, 0, 0);
		if($LeftDigit[$NumZero]==0) { 
		$left_text_color_odd = array(0, 0, 0, 1, 1, 0, 1); 
		$left_text_color_even = array(0, 1, 0, 0, 1, 1, 1); }
		if($LeftDigit[$NumZero]==1) { 
		$left_text_color_odd = array(0, 0, 1, 1, 0, 0, 1); 
		$left_text_color_even = array(0, 1, 1, 0, 0, 1, 1); }
		if($LeftDigit[$NumZero]==2) { 
		$left_text_color_odd = array(0, 0, 1, 0, 0, 1, 1); 
		$left_text_color_even = array(0, 0, 1, 1, 0, 1, 1); }
		if($LeftDigit[$NumZero]==3) { 
		$left_text_color_odd = array(0, 1, 1, 1, 1, 0, 1); 
		$left_text_color_even = array(0, 1, 0, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==4) { 
		$left_text_color_odd = array(0, 1, 0, 0, 0, 1, 1); 
		$left_text_color_even = array(0, 0, 1, 1, 1, 0, 1); }
		if($LeftDigit[$NumZero]==5) { 
		$left_text_color_odd = array(0, 1, 1, 0, 0, 0, 1); 
		$left_text_color_even = array(0, 1, 1, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==6) { 
		$left_text_color_odd = array(0, 1, 0, 1, 1, 1, 1); 
		$left_text_color_even = array(0, 0, 0, 0, 1, 0, 1); }
		if($LeftDigit[$NumZero]==7) { 
		$left_text_color_odd = array(0, 1, 1, 1, 0, 1, 1); 
		$left_text_color_even = array(0, 0, 1, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==8) { 
		$left_text_color_odd = array(0, 1, 1, 0, 1, 1, 1); 
		$left_text_color_even = array(0, 0, 0, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==9) {
		$left_text_color_odd = array(0, 0, 0, 1, 0, 1, 1);
		$left_text_color_even = array(0, 0, 1, 0, 1, 1, 1); }
		$left_text_color = $left_text_color_odd;
		if($upc_matches[3]==0&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==2) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==1&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==2&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==3&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==4&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==5&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==6&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==7&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==8&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==9&&$upc_matches[1]==0) {
		if($NumZero==0) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==0&&$upc_matches[1]==1) {
		if($NumZero==3) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==1&&$upc_matches[1]==1) {
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==2&&$upc_matches[1]==1) {
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==3&&$upc_matches[1]==1) {
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==4&&$upc_matches[1]==1) {
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==5&&$upc_matches[1]==1) {
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==6&&$upc_matches[1]==1) {
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==7&&$upc_matches[1]==1) {
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; }
		if($NumZero==5) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==8&&$upc_matches[1]==1) {
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==3) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; } }
		if($upc_matches[3]==9&&$upc_matches[1]==1) {
		if($NumZero==1) { $left_text_color = $left_text_color_even; }
		if($NumZero==2) { $left_text_color = $left_text_color_even; }
		if($NumZero==4) { $left_text_color = $left_text_color_even; } }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 54, 10, 54, 51, $alt_text_color);
	imageline($upc_img, 55, 10, 55, 51, $text_color);
	imageline($upc_img, 56, 10, 56, 51, $alt_text_color);
	imageline($upc_img, 57, 10, 57, 51, $text_color);
	imageline($upc_img, 58, 10, 58, 51, $alt_text_color);
	imageline($upc_img, 59, 10, 59, 51, $text_color);
	imageline($upc_img, 60, 10, 60, 47, $alt_text_color);
	imageline($upc_img, 61, 10, 61, 47, $alt_text_color);
	imageline($upc_img, 62, 10, 62, 47, $alt_text_color);
	imageline($upc_img, 63, 10, 63, 47, $alt_text_color);
	imageline($upc_img, 64, 10, 64, 47, $alt_text_color);
	imageline($upc_img, 65, 10, 65, 47, $alt_text_color);
	imageline($upc_img, 66, 10, 66, 47, $alt_text_color);
	if($resize>1) {
	$new_upc_img = imagecreatetruecolor(69 * $resize, 62 * $resize);
	imagefilledrectangle($new_upc_img, 0, 0, 69 * $resize, 62 * $resize, 0xFFFFFF);
	imageinterlace($new_upc_img, true);
	if($resizetype=="resize") {
	imagecopyresized($new_upc_img, $upc_img, 0, 0, 0, 0, 69 * $resize, 62 * $resize, 69, 62); }
	if($resizetype=="resample") {
	imagecopyresampled($new_upc_img, $upc_img, 0, 0, 0, 0, 69 * $resize, 62 * $resize, 69, 62); }
	imagedestroy($upc_img); 
	$upc_img = $new_upc_img; }
	if($imgtype=="png") {
	if($outputimage==true) {
	imagepng($upc_img); }
	if($outfile!=null) {
	imagepng($upc_img,$outfile); } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	imagegif($upc_img); }
	if($outfile!=null) {
	imagegif($upc_img,$outfile); } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	imagexbm($upc_img,NULL); }
	if($outfile!=null) {
	imagexbm($upc_img,$outfile); } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	imagewbmp($upc_img); }
	if($outfile!=null) {
	imagewbmp($upc_img,$outfile); } }
	imagedestroy($upc_img); 
	return true; }

// Code for making EAN-13 by Kazuki Przyborowski
function create_ean13($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)) { return false; }
	$upc_pieces = null; $supplement = null;
	if(strlen($upc)==15) { $upc_pieces = explode(" ", $upc); }
	if(strlen($upc)==16) { $upc_pieces = explode(" ", $upc); }
	if(strlen($upc)==18) { $upc_pieces = explode(" ", $upc); }
	if(strlen($upc)==19) { $upc_pieces = explode(" ", $upc); }
	if(count($upc_pieces)>1) { $upc = $upc_pieces[0]; $supplement = $upc_pieces[1]; }
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(isset($supplement)&&!is_numeric($supplement)) { return false; }
	if(strlen($upc)==8) { $upc = convert_upce_to_ean13($upc); }
	if(strlen($upc)==12) { $upc = convert_upca_to_ean13($upc); }
	if(strlen($upc)==12) { $upc = "0".$upc; }
	if(strlen($upc)>13||strlen($upc)<13) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if(validate_ean13($upc)===false) { return false; }
	if($imgtype!="png"&&$imgtype!="gif"&&$imgtype!="xbm"&&$imgtype!="wbmp") { $imgtype = "png"; }
	preg_match("/(\d{1})(\d{6})(\d{6})/", $upc, $upc_matches);
	if(count($upc_matches)<=0) { return false; }
	$PrefixDigit = $upc_matches[1];
	$LeftDigit = str_split($upc_matches[2]);
	$RightDigit = str_split($upc_matches[3]);
	if($imgtype=="png") {
	if($outputimage==true) {
	/*header("Content-Type: image/png");*/ } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	/*header("Content-Type: image/gif");*/ } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	/*header("Content-Type: image/x-xbitmap");*/ } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	/*header("Content-Type: image/vnd.wap.wbmp");*/ } }
	$addonsize = 0;
	if(strlen($supplement)==2) { $addonsize = 29; }
	if(strlen($supplement)==5) { $addonsize = 56; }
	$upc_img = imagecreatetruecolor(115 + $addonsize, 62);
	imagefilledrectangle($upc_img, 0, 0, 115 + $addonsize, 62, 0xFFFFFF);
	imageinterlace($upc_img, true);
	$background_color = imagecolorallocate($upc_img, 255, 255, 255);
	$text_color = imagecolorallocate($upc_img, 0, 0, 0);
	$alt_text_color = imagecolorallocate($upc_img, 255, 255, 255);
	imagestring($upc_img, 2, 4, 47, $upc_matches[1], $text_color);
	imagestring($upc_img, 2, 18, 47, $upc_matches[2], $text_color);
	imagestring($upc_img, 2, 65, 47, $upc_matches[3], $text_color);
	imageline($upc_img, 0, 10, 0, 47, $alt_text_color);
	imageline($upc_img, 1, 10, 1, 47, $alt_text_color);
	imageline($upc_img, 2, 10, 2, 47, $alt_text_color);
	imageline($upc_img, 3, 10, 3, 47, $alt_text_color);
	imageline($upc_img, 4, 10, 4, 47, $alt_text_color);
	imageline($upc_img, 5, 10, 5, 47, $alt_text_color);
	imageline($upc_img, 6, 10, 6, 47, $alt_text_color);
	imageline($upc_img, 7, 10, 7, 47, $alt_text_color);
	imageline($upc_img, 8, 10, 8, 47, $alt_text_color);
	imageline($upc_img, 9, 10, 9, 47, $alt_text_color);
	imageline($upc_img, 10, 10, 10, 51, $alt_text_color);
	imageline($upc_img, 11, 10, 11, 51, $text_color);
	imageline($upc_img, 12, 10, 12, 51, $alt_text_color);
	imageline($upc_img, 13, 10, 13, 51, $text_color);
	$NumZero = 0; $LineStart = 14;
	while ($NumZero < count($LeftDigit)) {
		$LineSize = 47;
		$left_text_color_l = array(0, 0, 0, 0, 0, 0, 0); 
		$left_text_color_g = array(1, 1, 1, 1, 1, 1, 1);
		if($LeftDigit[$NumZero]==0) { 
		$left_text_color_l = array(0, 0, 0, 1, 1, 0, 1); 
		$left_text_color_g = array(0, 1, 0, 0, 1, 1, 1); }
		if($LeftDigit[$NumZero]==1) { 
		$left_text_color_l = array(0, 0, 1, 1, 0, 0, 1); 
		$left_text_color_g = array(0, 1, 1, 0, 0, 1, 1); }
		if($LeftDigit[$NumZero]==2) { 
		$left_text_color_l = array(0, 0, 1, 0, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 1, 0, 1, 1); }
		if($LeftDigit[$NumZero]==3) { 
		$left_text_color_l = array(0, 1, 1, 1, 1, 0, 1); 
		$left_text_color_g = array(0, 1, 0, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==4) { 
		$left_text_color_l = array(0, 1, 0, 0, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 1, 1, 0, 1); }
		if($LeftDigit[$NumZero]==5) { 
		$left_text_color_l = array(0, 1, 1, 0, 0, 0, 1); 
		$left_text_color_g = array(0, 1, 1, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==6) { 
		$left_text_color_l = array(0, 1, 0, 1, 1, 1, 1); 
		$left_text_color_g = array(0, 0, 0, 0, 1, 0, 1); }
		if($LeftDigit[$NumZero]==7) { 
		$left_text_color_l = array(0, 1, 1, 1, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==8) { 
		$left_text_color_l = array(0, 1, 1, 0, 1, 1, 1); 
		$left_text_color_g = array(0, 0, 0, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==9) {
		$left_text_color_l = array(0, 0, 0, 1, 0, 1, 1);
		$left_text_color_g = array(0, 0, 1, 0, 1, 1, 1); }
		$left_text_color = $left_text_color_l;
		if($upc_matches[1]==1) {
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==2) {
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==3) {
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==4) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==5) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==6) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==7) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==8) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==9) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; } }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 56, 10, 56, 51, $alt_text_color);
	imageline($upc_img, 57, 10, 57, 51, $text_color);
	imageline($upc_img, 58, 10, 58, 51, $alt_text_color);
	imageline($upc_img, 59, 10, 59, 51, $text_color);
	imageline($upc_img, 60, 10, 60, 51, $alt_text_color);
	$NumZero = 0; $LineStart = 61;
	while ($NumZero < count($RightDigit)) {
		$LineSize = 47;
		$right_text_color = array(0, 0, 0, 0, 0, 0, 0);
		if($RightDigit[$NumZero]==0) { 
		$right_text_color = array(1, 1, 1, 0, 0, 1, 0); }
		if($RightDigit[$NumZero]==1) { 
		$right_text_color = array(1, 1, 0, 0, 1, 1, 0); }
		if($RightDigit[$NumZero]==2) { 
		$right_text_color = array(1, 1, 0, 1, 1, 0, 0); }
		if($RightDigit[$NumZero]==3) { 
		$right_text_color = array(1, 0, 0, 0, 0, 1, 0); }
		if($RightDigit[$NumZero]==4) { 
		$right_text_color = array(1, 0, 1, 1, 1, 0, 0); }
		if($RightDigit[$NumZero]==5) { 
		$right_text_color = array(1, 0, 0, 1, 1, 1, 0); }
		if($RightDigit[$NumZero]==6) { 
		$right_text_color = array(1, 0, 1, 0, 0, 0, 0); }
		if($RightDigit[$NumZero]==7) { 
		$right_text_color = array(1, 0, 0, 0, 1, 0, 0); }
		if($RightDigit[$NumZero]==8) { 
		$right_text_color = array(1, 0, 0, 1, 0, 0, 0); }
		if($RightDigit[$NumZero]==9) { 
		$right_text_color = array(1, 1, 1, 0, 1, 0, 0); }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($right_text_color)) {
		if($right_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($right_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 103, 10, 103, 51, $text_color);
	imageline($upc_img, 104, 10, 104, 51, $alt_text_color);
	imageline($upc_img, 105, 10, 105, 51, $text_color);
	imageline($upc_img, 106, 10, 106, 47, $alt_text_color);
	imageline($upc_img, 107, 10, 107, 47, $alt_text_color);
	imageline($upc_img, 108, 10, 108, 47, $alt_text_color);
	imageline($upc_img, 109, 10, 109, 47, $alt_text_color);
	imageline($upc_img, 110, 10, 110, 47, $alt_text_color);
	imageline($upc_img, 111, 10, 111, 47, $alt_text_color);
	imageline($upc_img, 112, 10, 112, 47, $alt_text_color);
	if(strlen($supplement)==2) { create_ean2($supplement,113,$upc_img); }
	if(strlen($supplement)==5) { create_ean2($supplement,113,$upc_img); }
	if($resize>1) {
	$new_upc_img = imagecreatetruecolor((115 + $addonsize) * $resize, 62 * $resize);
	imagefilledrectangle($new_upc_img, 0, 0, (115 + $addonsize), 62, 0xFFFFFF);
	imageinterlace($new_upc_img, true);
	if($resizetype=="resize") {
	imagecopyresized($new_upc_img, $upc_img, 0, 0, 0, 0, (115 + $addonsize) * $resize, 62 * $resize, 115 + $addonsize, 62); }
	if($resizetype=="resample") {
	imagecopyresampled($new_upc_img, $upc_img, 0, 0, 0, 0, (115 + $addonsize) * $resize, 62 * $resize, 115 + $addonsize, 62); }
	imagedestroy($upc_img); 
	$upc_img = $new_upc_img; }
	if($imgtype=="png") {
	if($outputimage==true) {
	imagepng($upc_img); }
	if($outfile!=null) {
	imagepng($upc_img,$outfile); } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	imagegif($upc_img); }
	if($outfile!=null) {
	imagegif($upc_img,$outfile); } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	imagexbm($upc_img,NULL); }
	if($outfile!=null) {
	imagexbm($upc_img,$outfile); } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	imagewbmp($upc_img); }
	if($outfile!=null) {
	imagewbmp($upc_img,$outfile); } }
	imagedestroy($upc_img); 
	return true; }

// Code for making EAN-8 by Kazuki Przyborowski
function create_ean8($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc)==7) { $upc = $upc.validate_ean8($upc,true); }
	if(strlen($upc)>8||strlen($upc)<8) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if(validate_ean8($upc)===false) { return false; }
	if($imgtype!="png"&&$imgtype!="gif"&&$imgtype!="xbm"&&$imgtype!="wbmp") { $imgtype = "png"; }
	preg_match("/(\d{4})(\d{4})/", $upc, $upc_matches);
	if(count($upc_matches)<=0) { return false; }
	$LeftDigit = str_split($upc_matches[1]);
	preg_match("/(\d{2})(\d{2})/", $upc_matches[1], $upc_matches_new);
	$LeftLeftDigit = $upc_matches_new[1];
	$LeftRightDigit = $upc_matches_new[2];
	$RightDigit = str_split($upc_matches[2]);
	preg_match("/(\d{2})(\d{2})/", $upc_matches[2], $upc_matches_new);
	$RightLeftDigit = $upc_matches_new[1];
	$RightRightDigit = $upc_matches_new[2];
	if($imgtype=="png") {
	if($outputimage==true) {
	/*header("Content-Type: image/png");*/ } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	/*header("Content-Type: image/gif");*/ } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	/*header("Content-Type: image/x-xbitmap");*/ } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	/*header("Content-Type: image/vnd.wap.wbmp");*/ } }
	$upc_img = imagecreatetruecolor(83, 62);
	imagefilledrectangle($upc_img, 0, 0, 83, 62, 0xFFFFFF);
	imageinterlace($upc_img, true);
	$background_color = imagecolorallocate($upc_img, 255, 255, 255);
	$text_color = imagecolorallocate($upc_img, 0, 0, 0);
	$alt_text_color = imagecolorallocate($upc_img, 255, 255, 255);
	imagestring($upc_img, 2, 12, 47, $LeftLeftDigit, $text_color);
	imagestring($upc_img, 2, 25, 47, $LeftRightDigit, $text_color);
	imagestring($upc_img, 2, 45, 47, $RightLeftDigit, $text_color);
	imagestring($upc_img, 2, 58, 47, $RightRightDigit, $text_color);
	imageline($upc_img, 0, 10, 0, 47, $alt_text_color);
	imageline($upc_img, 1, 10, 1, 47, $alt_text_color);
	imageline($upc_img, 2, 10, 2, 47, $alt_text_color);
	imageline($upc_img, 3, 10, 3, 47, $alt_text_color);
	imageline($upc_img, 4, 10, 4, 47, $alt_text_color);
	imageline($upc_img, 5, 10, 5, 47, $alt_text_color);
	imageline($upc_img, 6, 10, 6, 47, $alt_text_color);
	imageline($upc_img, 7, 10, 7, 51, $text_color);
	imageline($upc_img, 8, 10, 8, 51, $alt_text_color);
	imageline($upc_img, 9, 10, 9, 51, $text_color);
	$NumZero = 0; $LineStart = 10;
	while ($NumZero < count($LeftDigit)) {
		$LineSize = 47;
		$left_text_color_l = array(0, 0, 0, 0, 0, 0, 0); 
		$left_text_color_g = array(1, 1, 1, 1, 1, 1, 1);
		if($LeftDigit[$NumZero]==0) { 
		$left_text_color_l = array(0, 0, 0, 1, 1, 0, 1); 
		$left_text_color_g = array(0, 1, 0, 0, 1, 1, 1); }
		if($LeftDigit[$NumZero]==1) { 
		$left_text_color_l = array(0, 0, 1, 1, 0, 0, 1); 
		$left_text_color_g = array(0, 1, 1, 0, 0, 1, 1); }
		if($LeftDigit[$NumZero]==2) { 
		$left_text_color_l = array(0, 0, 1, 0, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 1, 0, 1, 1); }
		if($LeftDigit[$NumZero]==3) { 
		$left_text_color_l = array(0, 1, 1, 1, 1, 0, 1); 
		$left_text_color_g = array(0, 1, 0, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==4) { 
		$left_text_color_l = array(0, 1, 0, 0, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 1, 1, 0, 1); }
		if($LeftDigit[$NumZero]==5) { 
		$left_text_color_l = array(0, 1, 1, 0, 0, 0, 1); 
		$left_text_color_g = array(0, 1, 1, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==6) { 
		$left_text_color_l = array(0, 1, 0, 1, 1, 1, 1); 
		$left_text_color_g = array(0, 0, 0, 0, 1, 0, 1); }
		if($LeftDigit[$NumZero]==7) { 
		$left_text_color_l = array(0, 1, 1, 1, 0, 1, 1); 
		$left_text_color_g = array(0, 0, 1, 0, 0, 0, 1); }
		if($LeftDigit[$NumZero]==8) { 
		$left_text_color_l = array(0, 1, 1, 0, 1, 1, 1); 
		$left_text_color_g = array(0, 0, 0, 1, 0, 0, 1); }
		if($LeftDigit[$NumZero]==9) {
		$left_text_color_l = array(0, 0, 0, 1, 0, 1, 1);
		$left_text_color_g = array(0, 0, 1, 0, 1, 1, 1); }
		$left_text_color = $left_text_color_l;
		if($upc_matches[1]==1) {
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==2) {
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==3) {
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==4) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==5) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==6) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==7) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; }
		if($NumZero==5) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==8) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==3) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; } }
		if($upc_matches[1]==9) {
		if($NumZero==1) { $left_text_color = $left_text_color_g; }
		if($NumZero==2) { $left_text_color = $left_text_color_g; }
		if($NumZero==4) { $left_text_color = $left_text_color_g; } }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 38, 10, 38, 51, $alt_text_color);
	imageline($upc_img, 39, 10, 39, 51, $text_color);
	imageline($upc_img, 40, 10, 40, 51, $alt_text_color);
	imageline($upc_img, 41, 10, 41, 51, $text_color);
	imageline($upc_img, 42, 10, 42, 51, $alt_text_color);
	$NumZero = 0; $LineStart = 43;
	while ($NumZero < count($RightDigit)) {
		$LineSize = 47;
		$right_text_color = array(0, 0, 0, 0, 0, 0, 0);
		if($RightDigit[$NumZero]==0) { 
		$right_text_color = array(1, 1, 1, 0, 0, 1, 0); }
		if($RightDigit[$NumZero]==1) { 
		$right_text_color = array(1, 1, 0, 0, 1, 1, 0); }
		if($RightDigit[$NumZero]==2) { 
		$right_text_color = array(1, 1, 0, 1, 1, 0, 0); }
		if($RightDigit[$NumZero]==3) { 
		$right_text_color = array(1, 0, 0, 0, 0, 1, 0); }
		if($RightDigit[$NumZero]==4) { 
		$right_text_color = array(1, 0, 1, 1, 1, 0, 0); }
		if($RightDigit[$NumZero]==5) { 
		$right_text_color = array(1, 0, 0, 1, 1, 1, 0); }
		if($RightDigit[$NumZero]==6) { 
		$right_text_color = array(1, 0, 1, 0, 0, 0, 0); }
		if($RightDigit[$NumZero]==7) { 
		$right_text_color = array(1, 0, 0, 0, 1, 0, 0); }
		if($RightDigit[$NumZero]==8) { 
		$right_text_color = array(1, 0, 0, 1, 0, 0, 0); }
		if($RightDigit[$NumZero]==9) { 
		$right_text_color = array(1, 1, 1, 0, 1, 0, 0); }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($right_text_color)) {
		if($right_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $text_color); }
		if($right_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 10, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 71, 10, 71, 51, $text_color);
	imageline($upc_img, 72, 10, 72, 51, $alt_text_color);
	imageline($upc_img, 73, 10, 73, 51, $text_color);
	imageline($upc_img, 74, 10, 74, 47, $alt_text_color);
	imageline($upc_img, 75, 10, 75, 47, $alt_text_color);
	imageline($upc_img, 76, 10, 76, 47, $alt_text_color);
	imageline($upc_img, 77, 10, 77, 47, $alt_text_color);
	imageline($upc_img, 78, 10, 78, 47, $alt_text_color);
	imageline($upc_img, 79, 10, 79, 47, $alt_text_color);
	imageline($upc_img, 80, 10, 80, 47, $alt_text_color);
	if($resize>1) {
	$new_upc_img = imagecreatetruecolor(83 * $resize, 62 * $resize);
	imagefilledrectangle($new_upc_img, 0, 0, 83, 62, 0xFFFFFF);
	imageinterlace($new_upc_img, true);
	if($resizetype=="resize") {
	imagecopyresized($new_upc_img, $upc_img, 0, 0, 0, 0, 83 * $resize, 62 * $resize, 83, 62); }
	if($resizetype=="resample") {
	imagecopyresampled($new_upc_img, $upc_img, 0, 0, 0, 0, 83 * $resize, 62 * $resize, 83, 62); }
	imagedestroy($upc_img); 
	$upc_img = $new_upc_img; }
	if($imgtype=="png") {
	if($outputimage==true) {
	imagepng($upc_img); }
	if($outfile!=null) {
	imagepng($upc_img,$outfile); } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	imagegif($upc_img); }
	if($outfile!=null) {
	imagegif($upc_img,$outfile); } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	imagexbm($upc_img,NULL); }
	if($outfile!=null) {
	imagexbm($upc_img,$outfile); } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	imagewbmp($upc_img); }
	if($outfile!=null) {
	imagewbmp($upc_img,$outfile); } }
	imagedestroy($upc_img); 
	return true; }

// Code for making Interleaved 2 of 5 by Kazuki Przyborowski
function create_itf($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc) % 2) { return false; }
	if(strlen($upc) < 6) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if($imgtype!="png"&&$imgtype!="gif"&&$imgtype!="xbm"&&$imgtype!="wbmp") { $imgtype = "png"; }
	$upc_matches = str_split($upc, 2);
	$upc_size_add = count($upc_matches) * 18;
	if(count($upc_matches)<=0) { return false; }
	//$ITF14Digits = str_split($upc_matches[1]);
	if($imgtype=="png") {
	if($outputimage==true) {
	/*header("Content-Type: image/png");*/ } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	/*header("Content-Type: image/gif");*/ } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	/*header("Content-Type: image/x-xbitmap");*/ } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	/*header("Content-Type: image/vnd.wap.wbmp");*/ } }
	$upc_img = imagecreatetruecolor(39 + $upc_size_add, 62);
	imagefilledrectangle($upc_img, 0, 0, 39 + $upc_size_add, 62, 0xFFFFFF);
	imageinterlace($upc_img, true);
	$background_color = imagecolorallocate($upc_img, 255, 255, 255);
	$text_color = imagecolorallocate($upc_img, 0, 0, 0);
	$alt_text_color = imagecolorallocate($upc_img, 255, 255, 255);
	$NumTxtZero = 0; $LineTxtStart = 20;
	while ($NumTxtZero < count($upc_matches)) {
	$ArrayDigit = str_split($upc_matches[$NumTxtZero]);
	imagestring($upc_img, 2, $LineTxtStart, 48, $ArrayDigit[0], $text_color);
	$LineTxtStart += 9;
	imagestring($upc_img, 2, $LineTxtStart, 48, $ArrayDigit[1], $text_color);
	$LineTxtStart += 9;
	++$NumTxtZero; }
	imageline($upc_img, 0, 4, 0, 47, $alt_text_color);
	imageline($upc_img, 1, 4, 1, 47, $alt_text_color);
	imageline($upc_img, 2, 4, 2, 47, $alt_text_color);
	imageline($upc_img, 3, 4, 3, 47, $alt_text_color);
	imageline($upc_img, 4, 4, 4, 47, $alt_text_color);
	imageline($upc_img, 5, 4, 5, 47, $alt_text_color);
	imageline($upc_img, 6, 4, 6, 47, $alt_text_color);
	imageline($upc_img, 7, 4, 7, 47, $alt_text_color);
	imageline($upc_img, 8, 4, 8, 47, $alt_text_color);
	imageline($upc_img, 9, 4, 9, 47, $alt_text_color);
	imageline($upc_img, 10, 4, 10, 47, $alt_text_color);
	imageline($upc_img, 11, 4, 11, 47, $alt_text_color);
	imageline($upc_img, 12, 4, 12, 47, $alt_text_color);
	imageline($upc_img, 13, 4, 13, 47, $text_color);
	imageline($upc_img, 14, 4, 14, 47, $alt_text_color);
	imageline($upc_img, 15, 4, 15, 47, $text_color);
	imageline($upc_img, 16, 4, 16, 47, $alt_text_color);
	$NumZero = 0; $LineStart = 17; $LineSize = 47;
	while ($NumZero < count($upc_matches)) {
		$ArrayDigit = str_split($upc_matches[$NumZero]);
		$left_text_color = array(0, 0, 1, 1, 0);
		if($ArrayDigit[0]==0) {
		$left_text_color = array(0, 0, 1, 1, 0); }
		if($ArrayDigit[0]==1) {
		$left_text_color = array(1, 0, 0, 0, 1); }
		if($ArrayDigit[0]==2) {
		$left_text_color = array(0, 1, 0, 0, 1); }
		if($ArrayDigit[0]==3) {
		$left_text_color = array(1, 1, 0, 0, 0); }
		if($ArrayDigit[0]==4) {
		$left_text_color = array(0, 0, 1, 0, 1); }
		if($ArrayDigit[0]==5) {
		$left_text_color = array(1, 0, 1, 0, 0); }
		if($ArrayDigit[0]==6) {
		$left_text_color = array(0, 1, 1, 0, 0); }
		if($ArrayDigit[0]==7) {
		$left_text_color = array(0, 0, 0, 1, 1); }
		if($ArrayDigit[0]==8) {
		$left_text_color = array(1, 0, 0, 1, 0); }
		if($ArrayDigit[0]==9) {
		$left_text_color = array(0, 1, 0, 1, 0); }
		$right_text_color = array(0, 0, 1, 1, 0);
		if($ArrayDigit[1]==0) {
		$right_text_color = array(0, 0, 1, 1, 0); }
		if($ArrayDigit[1]==1) {
		$right_text_color = array(1, 0, 0, 0, 1); }
		if($ArrayDigit[1]==2) {
		$right_text_color = array(0, 1, 0, 0, 1); }
		if($ArrayDigit[1]==3) {
		$right_text_color = array(1, 1, 0, 0, 0); }
		if($ArrayDigit[1]==4) {
		$right_text_color = array(0, 0, 1, 0, 1); }
		if($ArrayDigit[1]==5) {
		$right_text_color = array(1, 0, 1, 0, 0); }
		if($ArrayDigit[1]==6) {
		$right_text_color = array(0, 1, 1, 0, 0); }
		if($ArrayDigit[1]==7) {
		$right_text_color = array(0, 0, 0, 1, 1); }
		if($ArrayDigit[1]==8) {
		$right_text_color = array(1, 0, 0, 1, 0); }
		if($ArrayDigit[1]==9) {
		$right_text_color = array(0, 1, 0, 1, 0); }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; }
		if($right_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; }
		if($right_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color);
		$LineStart += 1; }
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 17 + $upc_size_add, 4, 17 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 18 + $upc_size_add, 4, 18 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 19 + $upc_size_add, 4, 19 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 20 + $upc_size_add, 4, 20 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 21 + $upc_size_add, 4, 21 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 22 + $upc_size_add, 4, 22 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 23 + $upc_size_add, 4, 23 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 24 + $upc_size_add, 4, 24 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 25 + $upc_size_add, 4, 25 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 26 + $upc_size_add, 4, 26 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 27 + $upc_size_add, 4, 27 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 28 + $upc_size_add, 4, 28 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 29 + $upc_size_add, 4, 29 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 30 + $upc_size_add, 4, 30 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 31 + $upc_size_add, 4, 31 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 32 + $upc_size_add, 4, 32 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 33 + $upc_size_add, 4, 33 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 34 + $upc_size_add, 4, 34 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 35 + $upc_size_add, 4, 35 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 36 + $upc_size_add, 4, 36 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 37 + $upc_size_add, 4, 37 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 38 + $upc_size_add, 4, 38 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 39 + $upc_size_add, 4, 39 + $upc_size_add, 47, $alt_text_color);
	if($resize>1) {
	$new_upc_img = imagecreatetruecolor((39 + $upc_size_add) * $resize, 62 * $resize);
	imagefilledrectangle($new_upc_img, 0, 0, (39 + $upc_size_add) * $resize, 62 * $resize, 0xFFFFFF);
	imageinterlace($new_upc_img, true);
	if($resizetype=="resize") {
	imagecopyresized($new_upc_img, $upc_img, 0, 0, 0, 0, (39 + $upc_size_add) * $resize, 62 * $resize, (39 + $upc_size_add), 62); }
	if($resizetype=="resample") {
	imagecopyresampled($new_upc_img, $upc_img, 0, 0, 0, 0, (39 + $upc_size_add) * $resize, 62 * $resize, (39 + $upc_size_add), 62); }
	imagedestroy($upc_img); 
	$upc_img = $new_upc_img; }
	if($imgtype=="png") {
	if($outputimage==true) {
	imagepng($upc_img); }
	if($outfile!=null) {
	imagepng($upc_img,$outfile); } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	imagegif($upc_img); }
	if($outfile!=null) {
	imagegif($upc_img,$outfile); } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	imagexbm($upc_img,NULL); }
	if($outfile!=null) {
	imagexbm($upc_img,$outfile); } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	imagewbmp($upc_img); }
	if($outfile!=null) {
	imagewbmp($upc_img,$outfile); } }
	imagedestroy($upc_img); 
	return true; }

// Code for making ITF-14 by Kazuki Przyborowski
function create_itf14($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)||!is_numeric($upc)) { return false; }
	if(strlen($upc) % 2) { return false; }
	if(strlen($upc) < 6) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if($imgtype!="png"&&$imgtype!="gif"&&$imgtype!="xbm"&&$imgtype!="wbmp") { $imgtype = "png"; }
	$upc_matches = str_split($upc, 2);
	$upc_size_add = count($upc_matches) * 18;
	if(count($upc_matches)<=0) { return false; }
	if($imgtype=="png") {
	if($outputimage==true) {
	/*header("Content-Type: image/png");*/ } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	/*header("Content-Type: image/gif");*/ } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	/*header("Content-Type: image/x-xbitmap");*/ } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	/*header("Content-Type: image/vnd.wap.wbmp");*/ } }
	$upc_img = imagecreatetruecolor(44 + $upc_size_add, 62);
	imagefilledrectangle($upc_img, 0, 0, 44 + $upc_size_add, 62, 0xFFFFFF);
	imageinterlace($upc_img, true);
	$background_color = imagecolorallocate($upc_img, 255, 255, 255);
	$text_color = imagecolorallocate($upc_img, 0, 0, 0);
	$alt_text_color = imagecolorallocate($upc_img, 255, 255, 255);
	$NumTxtZero = 0; $LineTxtStart = 23;
	while ($NumTxtZero < count($upc_matches)) {
	$ArrayDigit = str_split($upc_matches[$NumTxtZero]);
	imagestring($upc_img, 2, $LineTxtStart, 50, $ArrayDigit[0], $text_color);
	$LineTxtStart += 9;
	imagestring($upc_img, 2, $LineTxtStart, 50, $ArrayDigit[1], $text_color);
	$LineTxtStart += 9;
	++$NumTxtZero; }
	imagerectangle($upc_img, 0, 0, 43 + $upc_size_add, 51, $text_color);
	imagerectangle($upc_img, 1, 1, 42 + $upc_size_add, 50, $text_color);
	imagerectangle($upc_img, 2, 2, 41 + $upc_size_add, 49, $text_color);
	imagerectangle($upc_img, 3, 3, 40 + $upc_size_add, 48, $text_color);
	imageline($upc_img, 4, 4, 4, 47, $alt_text_color);
	imageline($upc_img, 5, 4, 5, 47, $alt_text_color);
	imageline($upc_img, 6, 4, 6, 47, $alt_text_color);
	imageline($upc_img, 7, 4, 7, 47, $alt_text_color);
	imageline($upc_img, 8, 4, 8, 47, $alt_text_color);
	imageline($upc_img, 9, 4, 9, 47, $alt_text_color);
	imageline($upc_img, 10, 4, 10, 47, $alt_text_color);
	imageline($upc_img, 11, 4, 11, 47, $alt_text_color);
	imageline($upc_img, 12, 4, 12, 47, $alt_text_color);
	imageline($upc_img, 13, 4, 13, 47, $alt_text_color);
	imageline($upc_img, 14, 4, 14, 47, $alt_text_color);
	imageline($upc_img, 15, 4, 15, 47, $alt_text_color);
	imageline($upc_img, 16, 4, 16, 47, $alt_text_color);
	imageline($upc_img, 17, 4, 17, 47, $text_color);
	imageline($upc_img, 18, 4, 18, 47, $alt_text_color);
	imageline($upc_img, 19, 4, 19, 47, $text_color);
	imageline($upc_img, 20, 4, 20, 47, $alt_text_color);
	$NumZero = 0; $LineStart = 21; $LineSize = 47;
	while ($NumZero < count($upc_matches)) {
		$ArrayDigit = str_split($upc_matches[$NumZero]);
		$left_text_color = array(0, 0, 1, 1, 0);
		if($ArrayDigit[0]==0) {
		$left_text_color = array(0, 0, 1, 1, 0); }
		if($ArrayDigit[0]==1) {
		$left_text_color = array(1, 0, 0, 0, 1); }
		if($ArrayDigit[0]==2) {
		$left_text_color = array(0, 1, 0, 0, 1); }
		if($ArrayDigit[0]==3) {
		$left_text_color = array(1, 1, 0, 0, 0); }
		if($ArrayDigit[0]==4) {
		$left_text_color = array(0, 0, 1, 0, 1); }
		if($ArrayDigit[0]==5) {
		$left_text_color = array(1, 0, 1, 0, 0); }
		if($ArrayDigit[0]==6) {
		$left_text_color = array(0, 1, 1, 0, 0); }
		if($ArrayDigit[0]==7) {
		$left_text_color = array(0, 0, 0, 1, 1); }
		if($ArrayDigit[0]==8) {
		$left_text_color = array(1, 0, 0, 1, 0); }
		if($ArrayDigit[0]==9) {
		$left_text_color = array(0, 1, 0, 1, 0); }
		$right_text_color = array(0, 0, 1, 1, 0);
		if($ArrayDigit[1]==0) {
		$right_text_color = array(0, 0, 1, 1, 0); }
		if($ArrayDigit[1]==1) {
		$right_text_color = array(1, 0, 0, 0, 1); }
		if($ArrayDigit[1]==2) {
		$right_text_color = array(0, 1, 0, 0, 1); }
		if($ArrayDigit[1]==3) {
		$right_text_color = array(1, 1, 0, 0, 0); }
		if($ArrayDigit[1]==4) {
		$right_text_color = array(0, 0, 1, 0, 1); }
		if($ArrayDigit[1]==5) {
		$right_text_color = array(1, 0, 1, 0, 0); }
		if($ArrayDigit[1]==6) {
		$right_text_color = array(0, 1, 1, 0, 0); }
		if($ArrayDigit[1]==7) {
		$right_text_color = array(0, 0, 0, 1, 1); }
		if($ArrayDigit[1]==8) {
		$right_text_color = array(1, 0, 0, 1, 0); }
		if($ArrayDigit[1]==9) {
		$right_text_color = array(0, 1, 0, 1, 0); }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; }
		if($right_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; }
		if($right_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color);
		$LineStart += 1; }
		++$InnerUPCNum; }
		++$NumZero; }
	imageline($upc_img, 21 + $upc_size_add, 4, 21 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 22 + $upc_size_add, 4, 22 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 23 + $upc_size_add, 4, 23 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 24 + $upc_size_add, 4, 24 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 25 + $upc_size_add, 4, 25 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 26 + $upc_size_add, 4, 26 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 27 + $upc_size_add, 4, 27 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 28 + $upc_size_add, 4, 28 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 29 + $upc_size_add, 4, 29 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 30 + $upc_size_add, 4, 30 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 31 + $upc_size_add, 4, 31 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 32 + $upc_size_add, 4, 32 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 33 + $upc_size_add, 4, 33 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 34 + $upc_size_add, 4, 34 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 35 + $upc_size_add, 4, 35 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 36 + $upc_size_add, 4, 36 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 37 + $upc_size_add, 4, 37 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 38 + $upc_size_add, 4, 38 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 39 + $upc_size_add, 4, 39 + $upc_size_add, 47, $alt_text_color);
	if($resize>1) {
	$new_upc_img = imagecreatetruecolor((44 + $upc_size_add) * $resize, 62 * $resize);
	imagefilledrectangle($new_upc_img, 0, 0, (44 + $upc_size_add) * $resize, 62 * $resize, 0xFFFFFF);
	imageinterlace($new_upc_img, true);
	if($resizetype=="resize") {
	imagecopyresized($new_upc_img, $upc_img, 0, 0, 0, 0, (44 + $upc_size_add) * $resize, 62 * $resize, (44 + $upc_size_add), 62); }
	if($resizetype=="resample") {
	imagecopyresampled($new_upc_img, $upc_img, 0, 0, 0, 0, (44 + $upc_size_add) * $resize, 62 * $resize, (44 + $upc_size_add), 62); }
	imagedestroy($upc_img); 
	$upc_img = $new_upc_img; }
	if($imgtype=="png") {
	if($outputimage==true) {
	imagepng($upc_img); }
	if($outfile!=null) {
	imagepng($upc_img,$outfile); } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	imagegif($upc_img); }
	if($outfile!=null) {
	imagegif($upc_img,$outfile); } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	imagexbm($upc_img,NULL); }
	if($outfile!=null) {
	imagexbm($upc_img,$outfile); } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	imagewbmp($upc_img); }
	if($outfile!=null) {
	imagewbmp($upc_img,$outfile); } }
	imagedestroy($upc_img); 
	return true; }

// Code for making Code 39 by Kazuki Przyborowski
function create_code39($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)) { return false; }
	if(strlen($upc) < 1) { return false; }
	if(!preg_match("/([0-9a-zA-Z\-\.\$\/\+% ]+)/", $upc)) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if($imgtype!="png"&&$imgtype!="gif"&&$imgtype!="xbm"&&$imgtype!="wbmp") { $imgtype = "png"; }
	$upc = strtoupper($upc);
	$upc_matches = str_split($upc);
	$upc_size_add = (count($upc_matches) * 15) + (count($upc_matches) + 1);
	if(count($upc_matches)<=0) { return false; }
	if($imgtype=="png") {
	if($outputimage==true) {
	/*header("Content-Type: image/png");*/ } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	/*header("Content-Type: image/gif");*/ } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	/*header("Content-Type: image/x-xbitmap");*/ } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	/*header("Content-Type: image/vnd.wap.wbmp");*/ } }
	$upc_img = imagecreatetruecolor(48 + $upc_size_add, 62);
	imagefilledrectangle($upc_img, 0, 0, 48 + $upc_size_add, 62, 0xFFFFFF);
	imageinterlace($upc_img, true);
	$background_color = imagecolorallocate($upc_img, 255, 255, 255);
	$text_color = imagecolorallocate($upc_img, 0, 0, 0);
	$alt_text_color = imagecolorallocate($upc_img, 255, 255, 255);
	$NumTxtZero = 0; $LineTxtStart = 30;
	imagestring($upc_img, 2, 14, 48, "*", $text_color);
	while ($NumTxtZero < count($upc_matches)) {
	imagestring($upc_img, 2, $LineTxtStart, 48, $upc_matches[$NumTxtZero], $text_color);
	$LineTxtStart += 16;
	++$NumTxtZero; }
	imagestring($upc_img, 2, $LineTxtStart, 48, "*", $text_color);
	imageline($upc_img, 0, 4, 0, 47, $alt_text_color);
	imageline($upc_img, 1, 4, 1, 47, $alt_text_color);
	imageline($upc_img, 2, 4, 2, 47, $alt_text_color);
	imageline($upc_img, 3, 4, 3, 47, $alt_text_color);
	imageline($upc_img, 4, 4, 4, 47, $alt_text_color);
	imageline($upc_img, 5, 4, 5, 47, $alt_text_color);
	imageline($upc_img, 6, 4, 6, 47, $alt_text_color);
	imageline($upc_img, 7, 4, 7, 47, $alt_text_color);
	imageline($upc_img, 8, 4, 8, 47, $alt_text_color);
	imageline($upc_img, 9, 4, 9, 47, $text_color);
	imageline($upc_img, 10, 4, 10, 47, $alt_text_color);
	imageline($upc_img, 11, 4, 11, 47, $alt_text_color);
	imageline($upc_img, 12, 4, 12, 47, $alt_text_color);
	imageline($upc_img, 13, 4, 13, 47, $text_color);
	imageline($upc_img, 14, 4, 14, 47, $alt_text_color);
	imageline($upc_img, 15, 4, 15, 47, $text_color);
	imageline($upc_img, 16, 4, 16, 47, $text_color);
	imageline($upc_img, 17, 4, 17, 47, $text_color);
	imageline($upc_img, 18, 4, 18, 47, $alt_text_color);
	imageline($upc_img, 19, 4, 19, 47, $text_color);
	imageline($upc_img, 20, 4, 20, 47, $text_color);
	imageline($upc_img, 21, 4, 21, 47, $text_color);
	imageline($upc_img, 22, 4, 22, 47, $alt_text_color);
	imageline($upc_img, 23, 4, 23, 47, $text_color);
	imageline($upc_img, 24, 4, 24, 47, $alt_text_color); 
	$NumZero = 0; $LineStart = 25; $LineSize = 47;
	while ($NumZero < count($upc_matches)) {
		$left_text_color = array(0, 2, 0, 3, 1, 2, 1, 2, 0);
		if($upc_matches[$NumZero]==0) {
		$left_text_color = array(0, 2, 0, 3, 1, 2, 1, 2, 0); }
		if($upc_matches[$NumZero]==1) {
		$left_text_color = array(1, 2, 0, 3, 0, 2, 0, 2, 1); }
		if($upc_matches[$NumZero]==2) {
		$left_text_color = array(0, 2, 1, 3, 0, 2, 0, 2, 1); }
		if($upc_matches[$NumZero]==3) {
		$left_text_color = array(1, 2, 1, 3, 0, 2, 0, 2, 0); }
		if($upc_matches[$NumZero]==4) {
		$left_text_color = array(0, 2, 0, 3, 1, 2, 0, 2, 1); }
		if($upc_matches[$NumZero]==5) {
		$left_text_color = array(1, 2, 0, 3, 1, 2, 0, 2, 0); }
		if($upc_matches[$NumZero]==6) {
		$left_text_color = array(0, 2, 1, 3, 1, 2, 0, 2, 0); }
		if($upc_matches[$NumZero]==7) {
		$left_text_color = array(0, 2, 0, 3, 0, 2, 1, 2, 1); }
		if($upc_matches[$NumZero]==8) {
		$left_text_color = array(1, 2, 0, 3, 0, 2, 1, 2, 0); }
		if($upc_matches[$NumZero]==9) {
		$left_text_color = array(0, 2, 1, 3, 0, 2, 1, 2, 0); }
		if($upc_matches[$NumZero]=="A") {
		$left_text_color = array(1, 2, 0, 2, 0, 3, 0, 2, 1); }
		if($upc_matches[$NumZero]=="B") {
		$left_text_color = array(0, 2, 1, 2, 0, 3, 0, 2, 1); }
		if($upc_matches[$NumZero]=="C") {
		$left_text_color = array(1, 2, 1, 2, 0, 3, 0, 2, 0); }
		if($upc_matches[$NumZero]=="D") {
		$left_text_color = array(0, 2, 0, 2, 1, 3, 0, 2, 1); }
		if($upc_matches[$NumZero]=="E") {
		$left_text_color = array(1, 2, 0, 2, 1, 3, 0, 2, 0); }
		if($upc_matches[$NumZero]=="F") {
		$left_text_color = array(0, 2, 1, 2, 1, 3, 0, 2, 0); }
		if($upc_matches[$NumZero]=="G") {
		$left_text_color = array(0, 2, 0, 2, 0, 3, 1, 2, 1); }
		if($upc_matches[$NumZero]=="H") {
		$left_text_color = array(1, 2, 0, 2, 0, 3, 1, 2, 0); }
		if($upc_matches[$NumZero]=="I") {
		$left_text_color = array(0, 2, 1, 2, 0, 3, 1, 2, 0); }
		if($upc_matches[$NumZero]=="J") {
		$left_text_color = array(0, 2, 0, 2, 1, 3, 1, 2, 0); }
		if($upc_matches[$NumZero]=="K") {
		$left_text_color = array(1, 2, 0, 2, 0, 2, 0, 3, 1); }
		if($upc_matches[$NumZero]=="L") {
		$left_text_color = array(0, 2, 1, 2, 0, 2, 0, 3, 1); }
		if($upc_matches[$NumZero]=="M") {
		$left_text_color = array(1, 2, 1, 2, 0, 2, 0, 3, 0); }
		if($upc_matches[$NumZero]=="N") {
		$left_text_color = array(0, 2, 0, 2, 1, 2, 0, 3, 1); }
		if($upc_matches[$NumZero]=="O") {
		$left_text_color = array(1, 2, 0, 2, 1, 2, 0, 3, 0); }
		if($upc_matches[$NumZero]=="P") {
		$left_text_color = array(0, 2, 1, 2, 1, 2, 0, 3, 0); }
		if($upc_matches[$NumZero]=="Q") {
		$left_text_color = array(0, 2, 0, 2, 0, 2, 1, 3, 1); }
		if($upc_matches[$NumZero]=="R") {
		$left_text_color = array(1, 2, 0, 2, 0, 2, 1, 3, 0); }
		if($upc_matches[$NumZero]=="S") {
		$left_text_color = array(0, 2, 1, 2, 0, 2, 1, 3, 0); }
		if($upc_matches[$NumZero]=="T") {
		$left_text_color = array(0, 2, 0, 2, 1, 2, 1, 3, 0); }
		if($upc_matches[$NumZero]=="U") {
		$left_text_color = array(1, 3, 0, 2, 0, 2, 0, 2, 1); }
		if($upc_matches[$NumZero]=="V") {
		$left_text_color = array(0, 3, 1, 2, 0, 2, 0, 2, 1); }
		if($upc_matches[$NumZero]=="W") {
		$left_text_color = array(1, 3, 1, 2, 0, 2, 0, 2, 0); }
		if($upc_matches[$NumZero]=="X") {
		$left_text_color = array(0, 3, 0, 2, 1, 2, 0, 2, 1); }
		if($upc_matches[$NumZero]=="Y") {
		$left_text_color = array(1, 3, 0, 2, 1, 2, 0, 2, 0); }
		if($upc_matches[$NumZero]=="Z") {
		$left_text_color = array(0, 3, 1, 2, 1, 2, 0, 2, 0); }
		if($upc_matches[$NumZero]=="-") {
		$left_text_color = array(0, 3, 0, 2, 0, 2, 1, 2, 1); }
		if($upc_matches[$NumZero]==".") {
		$left_text_color = array(1, 3, 0, 2, 0, 2, 1, 2, 0); }
		if($upc_matches[$NumZero]==" ") {
		$left_text_color = array(0, 3, 1, 2, 0, 2, 1, 2, 0); }
		if($upc_matches[$NumZero]=="$") {
		$left_text_color = array(0, 3, 0, 3, 0, 3, 0, 2, 0); }
		if($upc_matches[$NumZero]=="/") {
		$left_text_color = array(0, 3, 0, 3, 0, 2, 0, 3, 0); }
		if($upc_matches[$NumZero]=="+") {
		$left_text_color = array(0, 3, 0, 2, 0, 3, 0, 3, 0); }
		if($upc_matches[$NumZero]=="%") {
		$left_text_color = array(0, 2, 0, 3, 0, 3, 0, 3, 0); }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); 
		$LineStart += 1; }
		if($left_text_color[$InnerUPCNum]==3) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; 
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; }
		if($left_text_color[$InnerUPCNum]==2) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; }
		++$InnerUPCNum; }
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); 
		$LineStart += 1; 
		++$NumZero; }
	imageline($upc_img, 23 + $upc_size_add, 4, 23 + $upc_size_add, 47, $alt_text_color); 
	imageline($upc_img, 24 + $upc_size_add, 4, 24 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 25 + $upc_size_add, 4, 25 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 26 + $upc_size_add, 4, 26 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 27 + $upc_size_add, 4, 27 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 28 + $upc_size_add, 4, 28 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 29 + $upc_size_add, 4, 29 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 30 + $upc_size_add, 4, 30 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 31 + $upc_size_add, 4, 31 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 32 + $upc_size_add, 4, 32 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 33 + $upc_size_add, 4, 33 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 34 + $upc_size_add, 4, 34 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 35 + $upc_size_add, 4, 35 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 36 + $upc_size_add, 4, 36 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 37 + $upc_size_add, 4, 37 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 38 + $upc_size_add, 4, 38 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 39 + $upc_size_add, 4, 39 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 40 + $upc_size_add, 4, 40 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 41 + $upc_size_add, 4, 41 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 42 + $upc_size_add, 4, 42 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 43 + $upc_size_add, 4, 43 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 44 + $upc_size_add, 4, 44 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 45 + $upc_size_add, 4, 45 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 46 + $upc_size_add, 4, 46 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 47 + $upc_size_add, 4, 47 + $upc_size_add, 47, $alt_text_color);
	if($resize>1) {
	$new_upc_img = imagecreatetruecolor((48 + $upc_size_add) * $resize, 62 * $resize);
	imagefilledrectangle($new_upc_img, 0, 0, (48 + $upc_size_add) * $resize, 62 * $resize, 0xFFFFFF);
	imageinterlace($new_upc_img, true);
	if($resizetype=="resize") {
	imagecopyresized($new_upc_img, $upc_img, 0, 0, 0, 0, (48 + $upc_size_add) * $resize, 62 * $resize, (48 + $upc_size_add), 62); }
	if($resizetype=="resample") {
	imagecopyresampled($new_upc_img, $upc_img, 0, 0, 0, 0, (48 + $upc_size_add) * $resize, 62 * $resize, (48 + $upc_size_add), 62); }
	imagedestroy($upc_img); 
	$upc_img = $new_upc_img; }
	if($imgtype=="png") {
	if($outputimage==true) {
	imagepng($upc_img); }
	if($outfile!=null) {
	imagepng($upc_img,$outfile); } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	imagegif($upc_img); }
	if($outfile!=null) {
	imagegif($upc_img,$outfile); } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	imagexbm($upc_img,NULL); }
	if($outfile!=null) {
	imagexbm($upc_img,$outfile); } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	imagewbmp($upc_img); }
	if($outfile!=null) {
	imagewbmp($upc_img,$outfile); } }
	imagedestroy($upc_img); 
	return true; }

// Code for making Code 93 by Kazuki Przyborowski
function create_code93($upc,$imgtype="png",$outputimage=true,$resize=1,$resizetype="resize",$outfile=NULL,$hidecd=false) {
	if(!isset($upc)) { return false; }
	if(strlen($upc) < 1) { return false; }
	if(!preg_match("/([0-9a-zA-Z\-\.\$\/\+% ]+)/", $upc)) { return false; }
	if(!isset($resize)||!preg_match("/^([0-9]*[\.]?[0-9])/", $resize)||$resize<1) { $resize = 1; }
	if($resizetype!="resample"&&$resizetype!="resize") { $resizetype = "resize"; }
	if($imgtype!="png"&&$imgtype!="gif"&&$imgtype!="xbm"&&$imgtype!="wbmp") { $imgtype = "png"; }
	$upc = strtoupper($upc);
	$upc_matches = str_split($upc);
	if(count($upc_matches)<=0) { return false; }
	$Code93Array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9", "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "-", ".", " ", "$", "/", "+", "%", "($)", "(%)", "(/)", "(+)");
	$Code93Values = array_flip($Code93Array);
	$upc_reverse = array_reverse($upc_matches);
	$upc_print = $upc_matches;
	$UPC_Count = 0; $UPC_Weight = 1; $UPC_Sum = 0;
	while ($UPC_Count < count($upc_reverse)) {
	if($UPC_Weight>20) { $UPC_Weight = 1; }
	$UPC_Sum = $UPC_Sum + ($UPC_Weight * $Code93Values[$upc_reverse[$UPC_Count]]);
	++$UPC_Count; ++$UPC_Weight; } 
	array_push($upc_matches, $Code93Array[$UPC_Sum % 47]);
	$upc_reverse = array_reverse($upc_matches);
	$UPC_Count = 0; $UPC_Weight = 1; $UPC_Sum = 0;
	while ($UPC_Count < count($upc_reverse)) {
	if($UPC_Weight>20) { $UPC_Weight = 1; }
	$UPC_Sum = $UPC_Sum + ($UPC_Weight * $Code93Values[$upc_reverse[$UPC_Count]]);
	++$UPC_Count; ++$UPC_Weight; } 
	array_push($upc_matches, $Code93Array[$UPC_Sum % 47]);
	$upc_size_add = (count($upc_matches) * 9);
	if($imgtype=="png") {
	if($outputimage==true) {
	/*header("Content-Type: image/png");*/ } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	/*header("Content-Type: image/gif");*/ } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	/*header("Content-Type: image/x-xbitmap");*/ } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	/*header("Content-Type: image/vnd.wap.wbmp");*/ } }
	$upc_img = imagecreatetruecolor(37 + $upc_size_add, 62);
	imagefilledrectangle($upc_img, 0, 0, 37 + $upc_size_add, 62, 0xFFFFFF);
	imageinterlace($upc_img, true);
	$background_color = imagecolorallocate($upc_img, 255, 255, 255);
	$text_color = imagecolorallocate($upc_img, 0, 0, 0);
	$alt_text_color = imagecolorallocate($upc_img, 255, 255, 255);
	$NumTxtZero = 0; $LineTxtStart = 18;
	while ($NumTxtZero < count($upc_print)) {
	imagestring($upc_img, 2, $LineTxtStart, 48, $upc_print[$NumTxtZero], $text_color);
	$LineTxtStart += 9;
	++$NumTxtZero; }
	imageline($upc_img, 0, 4, 0, 47, $alt_text_color);
	imageline($upc_img, 1, 4, 1, 47, $alt_text_color);
	imageline($upc_img, 2, 4, 2, 47, $alt_text_color);
	imageline($upc_img, 3, 4, 3, 47, $alt_text_color);
	imageline($upc_img, 4, 4, 4, 47, $alt_text_color);
	imageline($upc_img, 5, 4, 5, 47, $alt_text_color);
	imageline($upc_img, 6, 4, 6, 47, $alt_text_color);
	imageline($upc_img, 7, 4, 7, 47, $alt_text_color);
	imageline($upc_img, 8, 4, 8, 47, $alt_text_color);
	imageline($upc_img, 9, 4, 9, 47, $text_color);
	imageline($upc_img, 10, 4, 10, 47, $alt_text_color);
	imageline($upc_img, 11, 4, 11, 47, $text_color);
	imageline($upc_img, 12, 4, 12, 47, $alt_text_color);
	imageline($upc_img, 13, 4, 13, 47, $text_color);
	imageline($upc_img, 14, 4, 14, 47, $text_color);
	imageline($upc_img, 15, 4, 15, 47, $text_color);
	imageline($upc_img, 16, 4, 16, 47, $text_color);
	imageline($upc_img, 17, 4, 17, 47, $alt_text_color);
	$NumZero = 0; $LineStart = 18; $LineSize = 47;
	while ($NumZero < count($upc_matches)) {
		$left_text_color = array(1, 0, 0, 0, 1, 0, 1, 0, 0);
		if($upc_matches[$NumZero]==0) {
		$left_text_color = array(1, 0, 0, 0, 1, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]==1) {
		$left_text_color = array(1, 0, 1, 0, 0, 1, 0, 0, 0); }
		if($upc_matches[$NumZero]==2) {
		$left_text_color = array(1, 0, 1, 0, 0, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]==3) {
		$left_text_color = array(1, 0, 1, 0, 0, 0, 0, 1, 0); }
		if($upc_matches[$NumZero]==4) {
		$left_text_color = array(1, 0, 0, 1, 0, 1, 0, 0, 0); }
		if($upc_matches[$NumZero]==5) {
		$left_text_color = array(1, 0, 0, 1, 0, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]==6) {
		$left_text_color = array(1, 0, 0, 1, 0, 0, 0, 1, 0); }
		if($upc_matches[$NumZero]==7) {
		$left_text_color = array(1, 0, 1, 0, 1, 0, 0, 0, 0); }
		if($upc_matches[$NumZero]==8) {
		$left_text_color = array(1, 0, 0, 0, 1, 0, 0, 1, 0); }
		if($upc_matches[$NumZero]==9) {
		$left_text_color = array(1, 0, 0, 0, 0, 1, 0, 1, 0); }
		if($upc_matches[$NumZero]=="A") {
		$left_text_color = array(1, 1, 0, 1, 0, 1, 0, 0, 0); }
		if($upc_matches[$NumZero]=="B") {
		$left_text_color = array(1, 1, 0, 1, 0, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]=="C") {
		$left_text_color = array(1, 1, 0, 1, 0, 0, 0, 1, 0); }
		if($upc_matches[$NumZero]=="D") {
		$left_text_color = array(1, 1, 0, 0, 1, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]=="E") {
		$left_text_color = array(1, 1, 0, 0, 1, 0, 0, 1, 0); }
		if($upc_matches[$NumZero]=="F") {
		$left_text_color = array(1, 1, 0, 0, 0, 1, 0, 1, 0); }
		if($upc_matches[$NumZero]=="G") {
		$left_text_color = array(1, 0, 1, 1, 0, 1, 0, 0, 0); }
		if($upc_matches[$NumZero]=="H") {
		$left_text_color = array(1, 0, 1, 1, 0, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]=="I") {
		$left_text_color = array(1, 0, 1, 1, 0, 0, 0, 1, 0); }
		if($upc_matches[$NumZero]=="J") {
		$left_text_color = array(1, 0, 0, 1, 1, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]=="K") {
		$left_text_color = array(1, 0, 0, 0, 1, 1, 0, 1, 0); }
		if($upc_matches[$NumZero]=="L") {
		$left_text_color = array(1, 0, 1, 0, 1, 1, 0, 0, 0); }
		if($upc_matches[$NumZero]=="M") {
		$left_text_color = array(1, 0, 1, 0, 0, 1, 1, 0, 0); }
		if($upc_matches[$NumZero]=="N") {
		$left_text_color = array(1, 0, 1, 0, 0, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="O") {
		$left_text_color = array(1, 0, 0, 1, 0, 1, 1, 0, 0); }
		if($upc_matches[$NumZero]=="P") {
		$left_text_color = array(1, 0, 0, 0, 1, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="Q") {
		$left_text_color = array(1, 1, 0, 1, 1, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]=="R") {
		$left_text_color = array(1, 1, 0, 1, 1, 0, 0, 1, 0); }
		if($upc_matches[$NumZero]=="S") {
		$left_text_color = array(1, 1, 0, 1, 0, 1, 1, 0, 0); }
		if($upc_matches[$NumZero]=="T") {
		$left_text_color = array(1, 1, 0, 1, 0, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="U") {
		$left_text_color = array(1, 1, 0, 0, 1, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="V") {
		$left_text_color = array(1, 1, 0, 0, 1, 1, 0, 1, 0); }
		if($upc_matches[$NumZero]=="W") {
		$left_text_color = array(1, 0, 1, 1, 0, 1, 1, 0, 0); }
		if($upc_matches[$NumZero]=="X") {
		$left_text_color = array(1, 0, 1, 1, 0, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="Y") {
		$left_text_color = array(1, 0, 0, 1, 1, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="Z") {
		$left_text_color = array(1, 0, 0, 1, 1, 1, 0, 1, 0); }
		if($upc_matches[$NumZero]=="-") {
		$left_text_color = array(1, 0, 0, 1, 0, 1, 1, 1, 0); }
		if($upc_matches[$NumZero]==".") {
		$left_text_color = array(1, 1, 1, 0, 1, 0, 1, 0, 0); }
		if($upc_matches[$NumZero]==" ") {
		$left_text_color = array(1, 1, 1, 0, 1, 0, 0, 1, 0); }
		if($upc_matches[$NumZero]=="$") {
		$left_text_color = array(1, 1, 1, 0, 0, 1, 0, 1, 0); }
		if($upc_matches[$NumZero]=="/") {
		$left_text_color = array(1, 0, 1, 1, 0, 1, 1, 1, 0); }
		if($upc_matches[$NumZero]=="+") {
		$left_text_color = array(1, 0, 1, 1, 1, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="%") {
		$left_text_color = array(1, 1, 0, 1, 0, 1, 1, 1, 0); }
		if($upc_matches[$NumZero]=="($)") {
		$left_text_color = array(1, 0, 0, 1, 0, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="(%)") {
		$left_text_color = array(1, 1, 1, 0, 1, 1, 0, 1, 0); }
		if($upc_matches[$NumZero]=="(/)") {
		$left_text_color = array(1, 1, 1, 0, 1, 0, 1, 1, 0); }
		if($upc_matches[$NumZero]=="(+)") {
		$left_text_color = array(1, 0, 0, 1, 1, 0, 0, 1, 0); }
		$InnerUPCNum = 0;
		while ($InnerUPCNum < count($left_text_color)) {
		if($left_text_color[$InnerUPCNum]==1) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $text_color); }
		if($left_text_color[$InnerUPCNum]==0) {
		imageline($upc_img, $LineStart, 4, $LineStart, $LineSize, $alt_text_color); }
		$LineStart += 1;
		++$InnerUPCNum; }
		++$NumZero; }	
	imageline($upc_img, 18 + $upc_size_add, 4, 18 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 19 + $upc_size_add, 4, 19 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 20 + $upc_size_add, 4, 20 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 21 + $upc_size_add, 4, 21 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 22 + $upc_size_add, 4, 22 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 23 + $upc_size_add, 4, 23 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 24 + $upc_size_add, 4, 24 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 25 + $upc_size_add, 4, 25 + $upc_size_add, 47, $text_color);
	imageline($upc_img, 26 + $upc_size_add, 4, 26 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 27 + $upc_size_add, 4, 27+ $upc_size_add, 47, $text_color);
	imageline($upc_img, 28 + $upc_size_add, 4, 28 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 29 + $upc_size_add, 4, 29 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 30 + $upc_size_add, 4, 30 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 31 + $upc_size_add, 4, 31 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 32 + $upc_size_add, 4, 32 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 33 + $upc_size_add, 4, 33 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 34 + $upc_size_add, 4, 34 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 35 + $upc_size_add, 4, 35 + $upc_size_add, 47, $alt_text_color);
	imageline($upc_img, 36 + $upc_size_add, 4, 36 + $upc_size_add, 47, $alt_text_color);
	if($resize>1) {
	$new_upc_img = imagecreatetruecolor((37 + $upc_size_add) * $resize, 62 * $resize);
	imagefilledrectangle($new_upc_img, 0, 0, (37 + $upc_size_add) * $resize, 62 * $resize, 0xFFFFFF);
	imageinterlace($new_upc_img, true);
	if($resizetype=="resize") {
	imagecopyresized($new_upc_img, $upc_img, 0, 0, 0, 0, (37 + $upc_size_add) * $resize, 62 * $resize, (37 + $upc_size_add), 62); }
	if($resizetype=="resample") {
	imagecopyresampled($new_upc_img, $upc_img, 0, 0, 0, 0, (37 + $upc_size_add) * $resize, 62 * $resize, (37 + $upc_size_add), 62); }
	imagedestroy($upc_img); 
	$upc_img = $new_upc_img; }
	if($imgtype=="png") {
	if($outputimage==true) {
	imagepng($upc_img); }
	if($outfile!=null) {
	imagepng($upc_img,$outfile); } }
	if($imgtype=="gif") {
	if($outputimage==true) {
	imagegif($upc_img); }
	if($outfile!=null) {
	imagegif($upc_img,$outfile); } }
	if($imgtype=="xbm") {
	if($outputimage==true) {
	imagexbm($upc_img,NULL); }
	if($outfile!=null) {
	imagexbm($upc_img,$outfile); } }
	if($imgtype=="wbmp") {
	if($outputimage==true) {
	imagewbmp($upc_img); }
	if($outfile!=null) {
	imagewbmp($upc_img,$outfile); } }
	imagedestroy($upc_img); 
	return true; }

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
if($bartype=="upca"&&strlen($barcode)==15) { $upc_pieces = explode(" ", $barcode); }
if($bartype=="upca"&&strlen($barcode)==18) { $upc_pieces = explode(" ", $barcode); }
if($bartype=="upca"&&count($upc_pieces)>1) { $barcode = $upc_pieces[0]; $supplement = $upc_pieces[1]; }
if(strlen($supplement)!=2&&strlen($supplement)!=5) { $supplement = null; }
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
create_upce($barcode,$imagetypeext,true,$resizenum);
$bufsize = ob_get_length();
$buffer = ob_get_clean();
$handle = fopen($filename,"w+b");
fwrite($handle,$buffer,$bufsize);
fclose($handle); }
$upc_pieces = null; $supplement = null;
if($bartype=="ean13"&&strlen($barcode)==16) { $upc_pieces = explode(" ", $barcode); }
if($bartype=="ean13"&&strlen($barcode)==19) { $upc_pieces = explode(" ", $barcode); }
if($bartype=="ean13"&&count($upc_pieces)>1) { $barcode = $upc_pieces[0]; $supplement = $upc_pieces[1]; }
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
create_ean8($barcode,$imagetypeext,true,$resizenum);
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
if($confrom=="upce"&&$conto=="itf14"&&validate_upce($barcode, false)===true) { echo convert_ean13_to_itf14(convert_upce_to_ean13($barcode))."\n"; }
if($confrom=="itf14"&&$conto=="upce"&&validate_itf14($barcode, false)===true) { echo convert_itf14_to_upce($barcode)."\n"; }
if($confrom=="upca"&&$conto=="ean13"&&validate_upca($barcode, false)===true) { echo convert_upca_to_ean13($barcode)."\n"; }
if($confrom=="ean13"&&$conto=="upca"&&validate_ean13($barcode, false)===true) { echo convert_ean13_to_upca($barcode)."\n"; }
if($confrom=="upce"&&$conto=="ean13"&&validate_upce($barcode, false)===true) { echo convert_upce_to_ean13($barcode)."\n"; }
if($confrom=="ean13"&&$conto=="upce"&&validate_ean13($barcode, false)===true) { echo convert_ean13_to_upce($barcode)."\n"; }
if($confrom=="upca"&&$conto=="itf14"&&validate_upca($barcode, false)===true) { echo convert_ean13_to_itf14(convert_upca_to_ean13($barcode))."\n"; }
if($confrom=="itf14"&&$conto=="upca"&&validate_itf14($barcode, false)===true) { echo convert_itf14_to_upca($barcode)."\n"; }
if($confrom=="ean13"&&$conto=="itf14"&&validate_ean13($barcode, false)===true) { echo convert_ean13_to_itf14($barcode)."\n"; }
if($confrom=="itf14"&&$conto=="ean13"&&validate_itf14($barcode, false)===true) { echo convert_itf14_to_ean13($barcode)."\n"; } }
?>
