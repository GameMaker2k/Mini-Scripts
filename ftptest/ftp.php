<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.

    Copyright 2011 iDB Support - http://idb.berlios.de/
    Copyright 2011 Game Maker 2k - http://gamemaker2k.org/

    $FileInfo: ftp.php - Last Update: 08/09/2011 Version: 1 - Author: cooldude2k $
*/

ob_start();
session_start();
if (!isset($_GET['act'])) {
    $_GET['act'] = "login";
}
if (!isset($_GET['dir'])) {
    $_GET['dir'] = "/";
}
if (!isset($_POST['act'])) {
    $_POST['act'] = null;
}
if (!isset($_POST['server']) && !isset($_POST['user']) && !isset($_POST['password'])) {
    if (!isset($_SESSION['server'])) {
        $_GET['act'] = "login";
    }
    if (!isset($_SESSION['user'])) {
        $_GET['act'] = "login";
    }
    if (!isset($_SESSION['password'])) {
        $_GET['act'] = "login";
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <?php if ($_GET['act'] == "login") { ?>
  <title> FTP Test </title>
  <?php } if ($_GET['act'] == "list" && !isset($_GET['dir'])) { ?>
  <title> FTP Test - / </title>
  <?php } if ($_GET['act'] == "list" && isset($_GET['dir'])) { ?>
  <title> FTP Test - <?php echo htmlspecialchars($_GET['dir']); ?> </title>
  <?php } ?>
  <meta name="generator" content="editplus" />
  <meta name="author" content="" />
  <meta name="keywords" content="" />
  <meta name="description" content="" />
 </head>

 <body>
<?php if ($_GET['act'] == "login") { ?>
<form method="post" action="ftp.php?act=list">
<?php
if (!isset($_SESSION['server'])) { ?>
Server: <input type="text" id="server" name="server" /><br />
<?php }
if (!isset($_SESSION['user'])) { ?>
User: <input type="text" id="user" name="user" /><br />
<?php }
if (!isset($_SESSION['password'])) { ?>
Password: <input type="password" id="password" name="password" /><br />
<?php }
if (isset($_SESSION['server'])) { ?>
Server: <input type="text" id="server" name="server" value="<?php echo htmlspecialchars($_SESSION['server']); ?>" /><br />
<?php }
if (isset($_SESSION['user'])) { ?>
User: <input type="text" id="user" name="user" value="<?php echo htmlspecialchars($_SESSION['user']); ?>" /><br />
<?php }
if (isset($_SESSION['password'])) { ?>
Password: <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($_SESSION['password']); ?>" /><br />
<?php } ?>
<input type="hidden" style="display: hidden;" id="act" name="act" value="login" />
<input type="submit" value="Login" />
</form>
<?php }
if ($_POST['act']) {
    if (isset($_POST['server'])) {
        $_SESSION['server'] = $_POST['server'];
    }
    if (isset($_POST['user'])) {
        $_SESSION['user'] = $_POST['user'];
    }
    if (isset($_POST['password'])) {
        $_SESSION['password'] = $_POST['password'];
    }
}
if ($_GET['act'] == "list") {
    $ftp_id = ftp_connect($_SESSION['server']);
    ftp_login($ftp_id, $_SESSION['user'], $_SESSION['password']);
    ftp_pasv($ftp_id, true);
    ftp_chdir($ftp_id, $_GET['dir']);
    $chck_ftp_pwd = ftp_pwd($ftp_id);
    $chck_ftp_pwd = str_replace("\\", "/", $chck_ftp_pwd);
    echo "<a href=\"ftp.php?act=login\">Index Page</a><br />\n";
    echo "<a href=\"ftp://".htmlspecialchars($_SESSION['user'])."@".htmlspecialchars($_SESSION['server']).$chck_ftp_pwd."\">ftp://".htmlspecialchars($_SESSION['user'])."@".htmlspecialchars($_SESSION['server']).$chck_ftp_pwd."</a><br /><br />\n";
    if ($chck_ftp_pwd == "/") {
        $myftpdir = $chck_ftp_pwd;
    }
    if ($chck_ftp_pwd != "/") {
        $myftpdir = $chck_ftp_pwd."/";
    }
    $filelist = ftp_rawlist($ftp_id, ".");
    $i = 0;
    $num = count($filelist);
    $flist = array();
    $fli = 0;
    $dlist = array();
    $dli = 0;
    while ($i < $num) {
        $info = preg_split("/[\s]+/", $filelist[$i], 9);
        if (preg_match("/^d/", $info[0]) && $info[8] != "." && $info[8] != "..") {
            $dlist[$dli]['name'] = $info[8];
            ++$dli;
        }
        if (preg_match("/^\-/", $info[0])) {
            $flist[$fli]['name'] = $info[8];
            ++$fli;
        }
        ++$i;
    }
    if ($chck_ftp_pwd != "/") {
        echo "<a href=\"ftp.php?act=list&amp;dir=".urlencode(str_replace("\\", "/", dirname($chck_ftp_pwd)))."\">..</a><br />\n";
    }
    $dirnum = count($dlist);
    $diri = 0;
    while ($diri < $dirnum) {
        echo "<a href=\"ftp.php?act=list&amp;dir=".urlencode($myftpdir.$dlist[$diri]['name'])."\">".$dlist[$diri]['name']."</a><br />\n";
        ++$diri;
    }
    $filenum = count($flist);
    $filei = 0;
    while ($filei < $filenum) {
        echo "<!--<a href=\"ftp.php?act=view&amp;file=".urlencode($myftpdir.$flist[$filei]['name'])."\">-->".$flist[$filei]['name']."<!--</a>--><br />\n";
        ++$filei;
    }
    ftp_close($ftp_id);
} ?>
 </body>
</html>
<?php echo ob_get_clean(); ?>
