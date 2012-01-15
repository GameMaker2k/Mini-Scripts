<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.

    Copyright 2004-2008 Cool Dude 2k - http://idb.berlios.de/
    Copyright 2004-2008 Game Maker 2k - http://intdb.sourceforge.net/

    $FileInfo: belarus.php - Last Update: 8/22/2009 Version 8 - Author: cooldude2k $
*/
@ob_clean(); @ini_set('default_charset', 'iso-8859-1');
@header("Content-Type: text/html; charset=iso-8859-1"); 
if(isset($_GET['act'])&&!isset($_POST['act'])) { $_POST['act'] = $_GET['act']; }
if(!isset($_GET['act'])) { $_GET['act'] = null; }
if(!isset($_POST['act'])) { $_POST['act'] = null; }
if($_POST['act']=="en_be"||$_POST['act']=="be_en"||$_GET['act']=="lang:be") {
	require("belarus.php"); die(); }
if($_POST['act']=="en_bg"||$_POST['act']=="bg_en"||$_GET['act']=="lang:bg") {
	require("bulgaria.php"); die(); }
if($_POST['act']=="en_ky"||$_POST['act']=="ky_en"||$_GET['act']=="lang:ky") {
	require("kyrgyzstan.php"); die(); }
if($_POST['act']=="en_mk"||$_POST['act']=="mk_en"||$_GET['act']=="lang:mk") {
	require("macedonia.php"); die(); }
if($_POST['act']=="en_ru"||$_POST['act']=="ru_en"||$_GET['act']=="lang:ru") {
	require("russia.php"); die(); }
if($_POST['act']=="en_uk"||$_POST['act']=="uk_en"||$_GET['act']=="lang:uk") {
	require("ukraine.php"); die(); }
if($_POST['act']=="en_cy"||$_POST['act']=="cy_en"||$_GET['act']=="lang:cy") {
	require("test.php"); die(); } 
if($_POST['act']=="en_sla"||$_POST['act']=="sla_en"||$_GET['act']=="lang:sla") {
	require("rusyn.php"); die(); }
if($_POST['act']=="en_sr"||$_POST['act']=="sr_en"||$_GET['act']=="lang:sr") {
	require("serbia.php"); die(); }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> Cyrillic Test </title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
  <meta name="generator" content="editplus" />
  <meta name="author" content="Kazuki Przyborowski" />
  <meta name="keywords" content="Cyrillic, Test, Cyrillic Test" />
  <meta name="description" content="Cyrillic Test" />
 </head>

 <body>
<form method="post" action="?">
<label for="text">Insert Text: </label><br /><textarea rows="10" id="text" name="text" cols="40"></textarea><br />
<label for="act">Select Romanization Type: </label><br />
<select id="act" name="act" class="TextBox">
<option value="en_be">English to Belarusian</option>
<option value="be_en">Belarusian to English</option>
<option value="en_bg">English to Bulgarian</option>
<option value="bg_en">Bulgarian to English</option>
<option value="en_ky">English to Kyrgyz</option>
<option value="ky_en">Kyrgyz to English</option>
<option value="en_mk">English to Macedonian</option>
<option value="mk_en">Macedonian to English</option>
<option value="en_ru">English to Russian</option>
<option value="ru_en">Russian to English</option>
<option value="en_sla">English to Rusyn</option>
<option value="sla_en">Rusyn to English</option>
<option value="en_sr">English to Serbian</option>
<option value="sr_en">Serbian to English</option>
<option value="en_uk">English to Ukrainian</option>
<option value="uk_en">Ukrainian to English</option>
<option value="en_cy">English to Cyrillic</option>
<option value="cy_en">Cyrillic to English</option>
</select>
<input type="submit" value="Romanize Text" />
</form>
<div class="copyright"><br /><a href="http://idb.berlios.de/">Game Maker 2k</a> @ 2009</div>
 </body>
</html>
