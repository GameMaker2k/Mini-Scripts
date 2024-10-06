<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.

    Copyright 2009-2011 iDB Support - http://idb.berlios.de/
    Copyright 2009-2011 Game Maker 2k - http://gamemaker2k.org/

    $FileInfo: data.php - Last Update: 10/23/2011 Ver 1.0.0 - Author: cooldude2k $
*/

ob_start();
if (!isset($_SERVER['HTTP_USER_AGENT'])) {
    $_SERVER['HTTP_USER_AGENT'] = "";
}
if (strpos($_SERVER['HTTP_USER_AGENT'], "msie") &&
    !strpos($_SERVER['HTTP_USER_AGENT'], "opera")) {
    header("X-UA-Compatible: IE=Edge");
}
if (strpos($_SERVER['HTTP_USER_AGENT'], "chromeframe")) {
    header("X-UA-Compatible: IE=Edge,chrome=1");
}
header("Pragma: private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0");
header("P3P: CP=\"IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT\"");
header("Date: ".gmdate("D, d M Y H:i:s")." GMT");
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");
if (!isset($_GET['data']) && isset($_POST['data'])) {
    $_GET['data'] = $_POST['data'];
}
if (isset($_GET['data']) && ($_GET['data'] === null || $_GET['data'] == "")) {
    unset($_GET['data']);
}
if (!isset($_GET['data'])) {
    $_GET['data'] = null;
    $RandVar = rand(0, time()); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> Data URI scheme </title>
  <meta name="generator" content="editplus" />
  <meta name="author" content="Kazuki Przyborowski" />
 </head>
 <body>
  <form method="get" action="data.php">
   <div>GET Data</div>
   <textarea id="data_get" name="data" rows="10" cols="100"><?php echo htmlentities($_GET['data'], ENT_QUOTES); ?></textarea>
   <br />
   <input type="submit" />
  </form>
  <form method="post" action="data.php?<?php echo time().".".$RandVar; ?>&amp;<?php echo time().".".$RandVar; ?>&amp;#<?php echo time().".".$RandVar; ?>">
   <div>POST Data</div>
   <textarea id="data_post" name="data" rows="10" cols="100"><?php echo htmlentities($_GET['data'], ENT_QUOTES); ?></textarea>
   <br />
   <input type="submit" />
  </form>
 </body>
</html>
<?php unset($_GET['data']);
} if (isset($_GET['data'])) {
    $_GET['data'] = preg_replace("/([\s|\t|\n|\r|\x0B])/is", "", $_GET['data']);
    $NumMatches = preg_match("/data\:(.*?)\;charset=(.*?);base64\,(.*?)/isU", $_GET['data'], $URLData);
    if ($NumMatches > 0) {
        $URLDataInfo['MimeType'] = $URLData[1];
        $URLDataInfo['Charset'] = $URLData[2];
        $URLDataInfo['Data'] = $URLData[3];
        header("Content-Type: ".$URLDataInfo['MimeType']."; charset=".$URLDataInfo['Charset']);
        $ContentData = base64_decode($URLDataInfo['Data']);
        header("Content-Length: ".strlen($ContentData));
        header("Content-MD5: ".md5($ContentData));
        header("Content-SHA1: ".sha1($ContentData));
        echo $ContentData;
        die();
        exit();
    }
    $NumMatches = preg_match("/data\:(.*?)\;charset=(.*?);uuencode\,(.*?)/isU", $_GET['data'], $URLData);
    if ($NumMatches > 0) {
        $URLDataInfo['MimeType'] = $URLData[1];
        $URLDataInfo['Charset'] = $URLData[2];
        $URLDataInfo['Data'] = $URLData[3];
        header("Content-Type: ".$URLDataInfo['MimeType']."; charset=".$URLDataInfo['Charset']);
        $ContentData = convert_uudecode($URLDataInfo['Data']);
        header("Content-Length: ".strlen($ContentData));
        header("Content-MD5: ".md5($ContentData));
        header("Content-SHA1: ".sha1($ContentData));
        echo $ContentData;
        die();
        exit();
    }
    $NumMatches = preg_match("/data\:(.*?)\;charset=(.*?)\,(.*?)/isU", $_GET['data'], $URLData);
    if ($NumMatches > 0) {
        $URLDataInfo['MimeType'] = $URLData[1];
        $URLDataInfo['Charset'] = $URLData[2];
        $URLDataInfo['Data'] = $URLData[3];
        header("Content-Type: ".$URLDataInfo['MimeType']."; charset=".$URLDataInfo['Charset']);
        $ContentData = urldecode($URLDataInfo['Data']);
        header("Content-Length: ".strlen($ContentData));
        header("Content-MD5: ".md5($ContentData));
        header("Content-SHA1: ".sha1($ContentData));
        echo $ContentData;
        die();
        exit();
    }
    $NumMatches = preg_match("/data\:(.*?)\;base64\,(.*?)/isU", $_GET['data'], $URLData);
    if ($NumMatches > 0) {
        $URLDataInfo['MimeType'] = $URLData[1];
        $URLDataInfo['Data'] = $URLData[2];
        header("Content-Type: ".$URLDataInfo['MimeType']);
        $ContentData = base64_decode($URLDataInfo['Data']);
        header("Content-Length: ".strlen($ContentData));
        header("Content-MD5: ".md5($ContentData));
        header("Content-SHA1: ".sha1($ContentData));
        echo $ContentData;
        die();
        exit();
    }
    $NumMatches = preg_match("/data\:(.*?)\;uuencode\,(.*?)/isU", $_GET['data'], $URLData);
    if ($NumMatches > 0) {
        $URLDataInfo['MimeType'] = $URLData[1];
        $URLDataInfo['Data'] = $URLData[2];
        header("Content-Type: ".$URLDataInfo['MimeType']);
        $ContentData = convert_uudecode($URLDataInfo['Data']);
        header("Content-Length: ".strlen($ContentData));
        header("Content-MD5: ".md5($ContentData));
        header("Content-SHA1: ".sha1($ContentData));
        echo $ContentData;
        die();
        exit();
    }
    $NumMatches = preg_match("/data\:(.*?)\,(.*?)/isU", $_GET['data'], $URLData);
    if ($NumMatches > 0) {
        $URLDataInfo['MimeType'] = $URLData[1];
        $URLDataInfo['Data'] = $URLData[2];
        header("Content-Type: ".$URLDataInfo['MimeType']);
        $ContentData = urldecode($URLDataInfo['Data']);
        header("Content-Length: ".strlen($ContentData));
        header("Content-MD5: ".md5($ContentData));
        header("Content-SHA1: ".sha1($ContentData));
        echo $ContentData;
        die();
        exit();
    }
}
?>