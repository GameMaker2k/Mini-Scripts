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

    $FileInfo: russia.php - Last Update: 8/22/2009 Version 8 - Author: cooldude2k $
*/
@ob_clean();
@ini_set('default_charset', 'iso-8859-1');
@header("Content-Type: text/html; charset=iso-8859-1");
function stripslashes_if_gpc_magic_quotes($string)
{
    if (get_magic_quotes_gpc()) {
        return stripslashes($string);
    } else {
        return $string;
    }
}
if (!isset($_POST['text']) && isset($_GET['text'])) {
    $_POST['text'] = $_GET['text'];
}
if (!isset($_POST['act']) && isset($_GET['act'])) {
    $_POST['act'] = $_GET['act'];
}
if (!isset($_POST['text'])) {
    $_POST['text'] = null;
}
if (isset($_POST['text'])) {
    $_POST['text'] = stripslashes_if_gpc_magic_quotes($_POST['text']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> Russian Romanization Test </title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <meta name="generator" content="editplus" />
  <meta name="author" content="Kazuki Przyborowski" />
  <meta name="keywords" content="Russian, Romanization, Test, Russian Romanization Test, Russian Romanization" />
  <meta name="description" content="Russian Romanization Test" />
 </head>

 <body>
<form method="post" action="?">
<label for="text">Insert text to romanize: </label><br /><textarea rows="10" id="text" name="text" cols="40"></textarea><br />
<label for="act">Select Romanization Type: </label><br />
<select id="act" name="act" class="TextBox">
<option value="en_ru">English to Russian</option>
<option value="ru_en">Russian to English</option>
</select>
<input type="submit" value="Romanize Text" />
</form>
<?php
$real_en_ru = array("A=&#".hexdec("0410").";","a=&#".hexdec("0430").";",
"B=&#".hexdec("0411").";","b=&#".hexdec("0431").";",
"V=&#".hexdec("0412").";","v=&#".hexdec("0432").";",
"G=&#".hexdec("0413").";","g=&#".hexdec("0433").";",
"D=&#".hexdec("0414").";","d=&#".hexdec("0434").";",
"E=&#".hexdec("0415").";","e=&#".hexdec("0435").";",
"&#".hexdec("00CB").";=&#".hexdec("0401").";","&#".hexdec("00EB").";=&#".hexdec("0451").";",
"Zh=&#".hexdec("0416").";","zh=&#".hexdec("0436").";",
"Z=&#".hexdec("0417").";","z=&#".hexdec("0437").";",
"I=&#".hexdec("0418").";","i=&#".hexdec("0438").";",
"Y=&#".hexdec("0419").";","y=&#".hexdec("0439").";",
"K=&#".hexdec("041A").";","k=&#".hexdec("043A").";",
"L=&#".hexdec("041B").";","l=&#".hexdec("043B").";",
"M=&#".hexdec("041C").";","m=&#".hexdec("043C").";",
"N=&#".hexdec("041D").";","n=&#".hexdec("043D").";",
"O=&#".hexdec("041E").";","o=&#".hexdec("043E").";",
"P=&#".hexdec("041F").";","p=&#".hexdec("043F").";",
"R=&#".hexdec("0420").";","r=&#".hexdec("0440").";",
"S=&#".hexdec("0421").";","s=&#".hexdec("0441").";",
"T=&#".hexdec("0422").";","t=&#".hexdec("0442").";",
"U=&#".hexdec("0423").";","u=&#".hexdec("0443").";",
"F=&#".hexdec("0424").";","f=&#".hexdec("0444").";",
"Kh=&#".hexdec("0425").";","kh=&#".hexdec("0445").";",
"Ts=&#".hexdec("0426").";","ts=&#".hexdec("0446").";",
"Ch=&#".hexdec("0427").";","ch=&#".hexdec("0447").";",
"Sh=&#".hexdec("0428").";","sh=&#".hexdec("0448").";",
"Shch=&#".hexdec("0429").";","shch=&#".hexdec("0449").";",
"&#".hexdec("02BA").";=&#".hexdec("042A").";","&#".hexdec("02BA").";=&#".hexdec("044A").";",
"Y=&#".hexdec("042B").";","y=&#".hexdec("044B").";",
"&#".hexdec("02B9").";=&#".hexdec("042C").";","&#".hexdec("02B9").";=&#".hexdec("044C").";",
"E=&#".hexdec("042D").";","e=&#".hexdec("044D").";",
"Yu=&#".hexdec("042E").";","yu=&#".hexdec("044E").";",
"Ya=&#".hexdec("042F").";","ya=&#".hexdec("044F").";");
$en_ru = array("Shch=&#".hexdec("0429").";","shch=&#".hexdec("0449").";",
"Zh=&#".hexdec("0416").";","zh=&#".hexdec("0436").";",
"Kh=&#".hexdec("0425").";","kh=&#".hexdec("0445").";",
"Ts=&#".hexdec("0426").";","ts=&#".hexdec("0446").";",
"Ch=&#".hexdec("0427").";","ch=&#".hexdec("0447").";",
"Sh=&#".hexdec("0428").";","sh=&#".hexdec("0448").";",
"Yu=&#".hexdec("042E").";","yu=&#".hexdec("044E").";",
"Ya=&#".hexdec("042F").";","ya=&#".hexdec("044F").";",
"A=&#".hexdec("0410").";","a=&#".hexdec("0430").";",
"B=&#".hexdec("0411").";","b=&#".hexdec("0431").";",
"V=&#".hexdec("0412").";","v=&#".hexdec("0432").";",
"G=&#".hexdec("0413").";","g=&#".hexdec("0433").";",
"D=&#".hexdec("0414").";","d=&#".hexdec("0434").";",
"E=&#".hexdec("0415").";","e=&#".hexdec("0435").";",
"&#".hexdec("00CB").";=&#".hexdec("0401").";","&#".hexdec("00EB").";=&#".hexdec("0451").";",
"Z=&#".hexdec("0417").";","z=&#".hexdec("0437").";",
"I=&#".hexdec("0418").";","i=&#".hexdec("0438").";",
"Y=&#".hexdec("0419").";","y=&#".hexdec("0439").";",
"K=&#".hexdec("041A").";","k=&#".hexdec("043A").";",
"L=&#".hexdec("041B").";","l=&#".hexdec("043B").";",
"M=&#".hexdec("041C").";","m=&#".hexdec("043C").";",
"N=&#".hexdec("041D").";","n=&#".hexdec("043D").";",
"O=&#".hexdec("041E").";","o=&#".hexdec("043E").";",
"P=&#".hexdec("041F").";","p=&#".hexdec("043F").";",
"R=&#".hexdec("0420").";","r=&#".hexdec("0440").";",
"S=&#".hexdec("0421").";","s=&#".hexdec("0441").";",
"T=&#".hexdec("0422").";","t=&#".hexdec("0442").";",
"U=&#".hexdec("0423").";","u=&#".hexdec("0443").";",
"F=&#".hexdec("0424").";","f=&#".hexdec("0444").";",
"&#".hexdec("02BA").";=&#".hexdec("042A").";","&#".hexdec("02BA").";=&#".hexdec("044A").";",
"Y=&#".hexdec("042B").";","y=&#".hexdec("044B").";",
"&#".hexdec("02B9").";=&#".hexdec("042C").";","&#".hexdec("02B9").";=&#".hexdec("044C").";",
"E=&#".hexdec("042D").";","e=&#".hexdec("044D").";");
$tmp_lang = $en_ru;
$en_to_new = null;
$lang_to_en = null;
$i = 0;
$num = count($tmp_lang);
$l = 1;
while ($i < $num) {
    $lang_exp = explode("=", $tmp_lang[$i]);
    if ($l < $num) {
        $en_to_new = $en_to_new.$lang_exp[0].",";
        $lang_to_en = $lang_to_en.$lang_exp[1].",";
    }
    if ($l >= $num) {
        $en_to_new = $en_to_new.$lang_exp[0];
        $lang_to_en = $lang_to_en.$lang_exp[1];
    }
    ++$l;
    ++$i;
}
$en_ru = explode(",", $lang_to_en);
$ru_en = explode(",", $en_to_new);
if (!isset($_POST['act'])) {
    $_POST['act'] = "en_ru";
}
if (isset($_POST['act'])) {
    if (isset($_POST['text']) && $_POST['act'] == "en_ru") {
        $_POST['text'] = str_replace($ru_en, $en_ru, $_POST['text']);
        echo str_replace("<br>", "<br />", nl2br($_POST['text']));
    }
    if (isset($_POST['text']) && $_POST['act'] == "ru_en") {
        $_POST['text'] = str_replace($en_ru, $ru_en, $_POST['text']);
        echo str_replace("<br>", "<br />", nl2br($_POST['text']));
    }
}
?>
<div class="copyright"><br /><a href="http://idb.berlios.de/">Game Maker 2k</a> @ 2009</div>
 </body>
</html>
