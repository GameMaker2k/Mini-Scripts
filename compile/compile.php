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

    $FileInfo: compile.php - Last Update: 03/22/2012 Ver. 1.0.0 RC 10 - Author: cooldude2k $
*/

@ini_set("memory_limit", "-1");
@ignore_user_abort(true);
@set_time_limit(0);
@ini_set("zlib.output_compression", true);
@ini_set("zlib.output_compression_level", -1);
@ob_start("ob_gzhandler");
@header("Content-Type: text/html; charset=UTF-8");
$clang = "en_US";
@setlocale(LC_CTYPE, $clang.".UTF-8");
@ini_set("date.timezone","UTC"); 
if(!isset($_GET['act'])) { $_GET['act'] = null; }
if(!isset($_POST['act'])) { $_POST['act'] = null; }
if(!isset($_POST['code'])) { $_POST['code'] = null; }
if(PHP_OS=="WINNT"||PHP_OS=="WIN32") {
$file_ext = ".exe";
$gcc_cmd = "\"C:\MinGW\bin\gcc.exe\" -pass-exit-codes -v -x c \"%s\" -o \"%s\"";
$gpp_cmd = "\"C:\MinGW\bin\g++.exe\" -pass-exit-codes -v -x c++ \"%s\" -o \"%s\"";
$fortran_cmd = "\"C:\MinGW\bin\gfortran.exe\" -pass-exit-codes -v -x none \"%s\" -o \"%s\"";
$cmd_path = "C:\MinGW\bin;C:\Windows\;C:\Windows\system32;"; 
$cmd_env_vars = array(null); }
if(PHP_OS!="WINNT"&&PHP_OS!="WIN32") {
$file_ext = "";
$gcc_cmd = "\"/usr/bin/gcc\" -pass-exit-codes -v -x c \"%s\" -o \"%s\"";
$gpp_cmd = "\"/usr/bin/g++\" -pass-exit-codes -v -x c++ \"%s\" -o \"%s\"";
$fortran_cmd = "\"/usr/bin/gfortran\" -pass-exit-codes -v -x none \"%s\" -o \"%s\"";
$cmd_path = getenv("PATH");
$cmd_env_vars = array("TERM" => "xterm", "LANG" => $clang.".UTF-8", "LC_ALL" => $clang.".UTF-8", "TZ" => "UTC"); }
$c_ext = ".c";
$cpp_ext = ".cxx";
$fortran_ext = ".f";
$log_ext = ".log";
$file_stdout = ".txt";
$out_dir = "./tmp/";
$other_cmd = $gcc_cmd;
$other_ext = $c_ext;
$use_pty = false;
$other_options = array('suppress_errors' => TRUE, 'bypass_shell' => TRUE);
$sysname = @php_uname("s")." ".@php_uname("r")." ".@php_uname("m");
if($sysname=="") { $sysname = PHP_OS; }

@ini_set("html_errors", false);
@ini_set("track_errors", false);
@ini_set("display_errors", false);
@ini_set("report_memleaks", false);
@ini_set("display_startup_errors", false);
@ini_set("docref_ext", "");
@ini_set("docref_root", "http://php.net/");
if(!defined("E_DEPRECATED")) { define("E_DEPRECATED", 0); }
@error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
@ini_set("default_mimetype","text/html"); 
@ini_set("session.use_trans_sid", false);
@ini_set("session.use_cookies", true);
@ini_set("session.use_only_cookies", true);
@ini_set("url_rewriter.tags",""); 
@ini_set('zend.ze1_compatibility_mode', 0);
@ini_set("ignore_user_abort", 1);
if(function_exists("date_default_timezone_set")) { 
	@date_default_timezone_set("UTC"); }

if(!isset($_SERVER['HTTP_USER_AGENT'])) {
	$_SERVER['HTTP_USER_AGENT'] = ""; }
if(strpos($_SERVER['HTTP_USER_AGENT'], "msie") && 
	!strpos($_SERVER['HTTP_USER_AGENT'], "opera")){
	@header("X-UA-Compatible: IE=Edge"); }
if(strpos($_SERVER['HTTP_USER_AGENT'], "chromeframe")) {
	@header("X-UA-Compatible: IE=Edge,chrome=1"); }
@header("Cache-Control: private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0");
@header("Pragma: private, no-cache, no-store, must-revalidate, pre-check=0, post-check=0, max-age=0");
@header("P3P: CP=\"IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT\"");
@header("Date: ".gmdate("D, d M Y H:i:s")." GMT");
@header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
@header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");


// _format_bytes by yatsynych at gmail dot com
// URL: http://php.net/manual/en/function.filesize.php#106935
function _format_bytes($a_bytes)
{
    if ($a_bytes < 1024) {
        return $a_bytes .' B';
    } elseif ($a_bytes < 1048576) {
        return round($a_bytes / 1024, 2) .' KiB';
    } elseif ($a_bytes < 1073741824) {
        return round($a_bytes / 1048576, 2) . ' MiB';
    } elseif ($a_bytes < 1099511627776) {
        return round($a_bytes / 1073741824, 2) . ' GiB';
    } elseif ($a_bytes < 1125899906842624) {
        return round($a_bytes / 1099511627776, 2) .' TiB';
    } elseif ($a_bytes < 1152921504606846976) {
        return round($a_bytes / 1125899906842624, 2) .' PiB';
    } elseif ($a_bytes < 1180591620717411303424) {
        return round($a_bytes / 1152921504606846976, 2) .' EiB';
    } elseif ($a_bytes < 1208925819614629174706176) {
        return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
    } else {
        return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
    }
}
function getTime() {
$a = explode (' ',microtime());
return(double) $a[0] + $a[1]; }
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> GNU GCC Compiler Test </title>
  <meta name="generator" content="NULL" />
  <meta name="author" content="NULL" />
  <meta name="keywords" content="GNU,GNU GCC,GCC,G++,GPP,GNU GCC Compiler Test" />
  <meta name="description" content="GNU GCC Compiler Test" />
  <meta http-equiv="Content-Language" content="en" />
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta http-equiv="Content-Style-Type" content="text/css" />
  <meta http-equiv="Content-Script-Type" content="text/javascript" />
  <style type="text/css">
  /* Coded by T. Longren from: http://www.longren.org/wrapping-text-inside-pre-tags/ */

  pre {
   white-space: pre-wrap;       /* css-3 */
   white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
   white-space: -pre-wrap;      /* Opera 4-6 */
   white-space: -o-pre-wrap;    /* Opera 7 */
   word-wrap: break-word;       /* Internet Explorer 5.5+ */
  }

  hr {
   border-top: 1px dashed #3CB371;
   border-bottom: 1px solid #2E8B57;
   border-left: 1px solid #87CEFA;
   border-right: 1px solid #87CEFA;
   color: #87CEEB;
   background-color: #87CEEB;
   height: 6px;
  }

  </style>
  <script type="text/javascript">
  <!--
  function getid(id) {
  var itm;
  itm = document.getElementById(id);
  return itm; }

  function change_display(id, display) {
  var itm;
  itm = document.getElementById(id);
  itm.style.display = display; }	

  function toggletag(id) {
  var itm;
  itm = document.getElementById(id);
  if (itm.style.display == "none") {
  itm.style.display = ""; }
  else {
  itm.style.display = "none"; } }	
  //-->
  </script>
 </head>

 <body style="background: #87CEEB;">

<?php
$filechck = str_replace("/", DIRECTORY_SEPARATOR, preg_replace("/(\/$|\\$)/is", "", $out_dir));
$windirfix = str_replace("/", DIRECTORY_SEPARATOR, $out_dir);
if(!file_exists($out_dir)&&!file_exists($filechck)) { 
	mkdir(str_replace("/", DIRECTORY_SEPARATOR, $out_dir), 0777); }
if(file_exists($filechck)&&!file_exists($out_dir)&&!is_dir($filechck)) { 
	unlink($filechck); 
	mkdir($windirfix, 0777); }
if(!isset($_GET['act'])) { $_GET['act'] = null; }
if(!isset($_POST['act'])) { $_POST['act'] = null; }
if(!isset($_POST['code'])) { $_POST['code'] = null; }
if(!isset($_POST['lang'])) { $_POST['lang'] = null; }
if(isset($_GET['act'])&&isset($_POST['act'])&&isset($_POST['code'])&&isset($_POST['lang'])) {
function make_seed()
{
  list($usec, $sec) = explode(' ', microtime());
  return (float) $sec + ((float) $usec * 100000);
}
srand(make_seed());
$filechck = str_replace("/", DIRECTORY_SEPARATOR, preg_replace("/(\/$|\\$)/is", "", $out_dir));
$windirfix = str_replace("/", DIRECTORY_SEPARATOR, $out_dir);
if(!file_exists($out_dir)&&!file_exists($filechck)) { 
	mkdir(str_replace("/", DIRECTORY_SEPARATOR, $out_dir), 0777); }
if(file_exists($filechck)&&!file_exists($out_dir)&&!is_dir($filechck)) { 
	unlink($filechck); 
	mkdir($windirfix, 0777); }
$cfname = tempnam($out_dir, uniqid(dechex(rand()), true));
if(DIRECTORY_SEPARATOR!="/") {
$cfname = str_replace(DIRECTORY_SEPARATOR, "/", $cfname); }
$oldcfile = $cfname;
$cfname_parts = pathinfo($cfname);
if($_POST['lang']=="c") {
$cfname = $cfname_parts['dirname']."/".$cfname_parts['filename'].$c_ext; }
if($_POST['lang']=="cpp") {
$cfname = $cfname_parts['dirname']."/".$cfname_parts['filename'].$cpp_ext; }
if($_POST['lang']=="fortran") {
$cfname = $cfname_parts['dirname']."/".$cfname_parts['filename'].$fortran_ext; }
if($_POST['lang']=="other") {
$cfname = $cfname_parts['dirname']."/".$cfname_parts['filename'].$_POST['extother']; }
if(file_exists($cfname)) { unlink($cfname); }
$cfout = $cfname_parts['dirname']."/".$cfname_parts['filename'].$file_ext;
if(file_exists($cfout)) { unlink($cfout); }
$clogout = $cfname_parts['dirname']."/".$cfname_parts['filename'].$log_ext;
if(file_exists($clogout)) { unlink($clogout); } 
$cstdout = $cfname_parts['dirname']."/".$cfname_parts['filename'].$file_stdout;
if(file_exists($cstdout)) { unlink($cstdout); } 
$fsize = strlen($_POST['code']);
chmod($oldcfile, 0666);
rename($oldcfile, $cfname); 
chmod($cfname, 0666);
if(file_exists($oldcfile)) { unlink($oldcfile); }
if(is_writable($cfname)) {
if(!$chandle = fopen($cfname, 'a')) {
echo "Cannot open file ".$cfname; }
if(fwrite($chandle, $_POST['code'], $fsize) === FALSE) {
echo "Cannot write to file ".$cfname; }
fclose($chandle); } 
else { echo "The file ".$cfname." is not writable"; }
if(file_exists($cfname)) {
$pre_cenv = array("PATH" => $cmd_path);
$cenv = array_merge($cmd_env_vars, $pre_cenv);
if($use_pty===false||$use_pty==null) {
$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
   2 => array("file", $clogout, "w+") // stderr is a file to write to
); }
if($use_pty===true) {
$descriptorspec = array(
   0 => array('pty'),
   1 => array('pty'),
   2 => array('pty')
); }
if(!isset($_POST['comparg'])) { $_POST['comparg'] = null; }
$CStart = getTime();
$cprocess = null;
if($_POST['lang']=="c") {
$cprocess = proc_open(sprintf($gcc_cmd, $cfname, $cfout)." ".$_POST['comparg'], $descriptorspec, $pipes, $cfname_parts['dirname'], $cenv, $other_options); }
if($_POST['lang']=="cpp") {
$cprocess = proc_open(sprintf($gpp_cmd, $cfname, $cfout)." ".$_POST['comparg'], $descriptorspec, $pipes, $cfname_parts['dirname'], $cenv, $other_options); }
if($_POST['lang']=="fortran") {
$cprocess = proc_open(sprintf($fortran_cmd, $cfname, $cfout)." ".$_POST['comparg'], $descriptorspec, $pipes, $cfname_parts['dirname'], $cenv, $other_options); }
if($_POST['lang']=="other") {
$cprocess = proc_open(sprintf($_POST['comother'], $cfname, $cfout)." ".$_POST['comparg'], $descriptorspec, $pipes, $cfname_parts['dirname'], $cenv, $other_options); }
$cprocinfo = proc_get_status($cprocess);
if(is_resource($cprocess)) {
echo "<a href=\"compile.php?act=compile&amp;#\" onclick=\"toggletag('hidecrunpro'); return false;\">Show Command Running</a><br />\n<pre style=\"border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff; display: none;\" id=\"hidecrunpro\">".htmlspecialchars($cprocinfo['command'], ENT_COMPAT | ENT_HTML401, "UTF-8")."</pre>\n";
echo "Source Code File Size "._format_bytes(filesize($cfname))."<br />\n"; }

/*
$ci = 0;
if(!isset($_POST['comstdin'])) {
$comstdinexp = null;
$cprocount = 0; }
if(isset($_POST['comstdin'])) {
$comstdinexp = explode("\n", $_POST['comstdin']); 
$cprocount = count($comstdinexp); }
*/
$ci = 0;
if(isset($_POST['comstdin'])) {
$comstdinexp = explode("\n", $_POST['comstdin']); 
$cprocount = count($comstdinexp);
while ($ci < $cprocount) {
$comstdinexp[$ci] = trim($comstdinexp[$ci]);
	++$ci; } 
$_POST['comstdin'] = implode("\n", $comstdinexp); }
if(is_resource($cprocess)) {
    // $pipes now looks like this:
    // 0 => writeable handle connected to child stdin
    // 1 => readable handle connected to child stdout
    // Any error output will be appended to /tmp/error-output.txt


/*
	while ($ci < $cprocount) {
    fwrite($pipes[0], trim($comstdinexp[$ci]));
	++$ci; }

    fwrite($pipes[0], null);
    fclose($pipes[0]);
*/

	if(isset($_POST['comstdin'])) {
    fwrite($pipes[0], $_POST['comstdin']);
    fclose($pipes[0]); }

	if(!isset($_POST['comstdin'])) {
    fwrite($pipes[0], null);
    fclose($pipes[0]); }

    $crunout = htmlspecialchars(stream_get_contents($pipes[1]), ENT_COMPAT | ENT_HTML401, "UTF-8");
    fclose($pipes[1]);

	$clogoutread = file_get_contents($clogout);
	//if($clogoutread==""||$clogoutread==null) {
	//echo "<br />\n"; }
	if($clogoutread!=""&&$clogoutread!=null) {
	echo "<a href=\"compile.php?act=compile&amp;#\" onclick=\"toggletag('hideclog'); return false;\">Show Compile Log</a><br />\n<pre style=\"border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff; display: none;\" id=\"hideclog\">".htmlspecialchars(file_get_contents($clogout), ENT_COMPAT | ENT_HTML401, "UTF-8")."</pre>\n"; }

    // It is important that you close any pipes before calling
    // proc_close in order to avoid a deadlock
    $return_value = proc_close($cprocess);

	echo "System Info ".$sysname."<br />\n";
	echo "Current Process ID ".$cprocinfo['pid']."<br />\n";
    echo "Command Returned ".$return_value."<br />\n";
    $CEnd = getTime();
    echo "Execution Time ".number_format(($CEnd - $CStart),2)." secs<br />\n";

	if($crunout==""||$crunout==null) {
	echo "<br />\n"; }
	if($crunout!=""&&$crunout!=null) {
    echo "<a href=\"compile.php?act=compile&amp;#\" onclick=\"toggletag('hidecout'); return false;\">Show Compile Output</a><pre style=\"border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff; display: none;\" id=\"hidecout\">".$crunout."</pre><br />\n<br />\n"; }

echo "<hr /><br />\n";

} }

if(file_exists($cfout)) {
chmod($cfname, 0777);
$pre_cenv = array("PATH" => $cfname_parts['dirname'].PATH_SEPARATOR.$cmd_path);
$cenv = array_merge($cmd_env_vars, $pre_cenv);
if($use_pty===false||$use_pty==null) {
$descriptorspec = array(
   0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
   1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
   2 => array("file", $clogout, "w+") // stderr is a file to write to
); }
if($use_pty===true) {
$descriptorspec = array(
   0 => array('pty'),
   1 => array('pty'),
   2 => array('pty')
); }
$ProStart = getTime();
$cprocess = null;
if(isset($_POST['arg'])) {
$cprocess = proc_open("\"".$cfout."\" ".$_POST['arg'], $descriptorspec, $pipes, $cfname_parts['dirname'], $cenv, $other_options); }
if(!isset($_POST['arg'])) {
$cprocess = proc_open("\"".$cfout."\"", $descriptorspec, $pipes, $cfname_parts['dirname'], $cenv, $other_options); }
if(is_resource($cprocess)) {
$cprocinfo = proc_get_status($cprocess);
echo "<a href=\"compile.php?act=compile&amp;#\" onclick=\"toggletag('hiderunpro'); return false;\">Show Command Running</a><br />\n<pre style=\"border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff; display: none;\" id=\"hiderunpro\">".htmlspecialchars($cprocinfo['command'], ENT_COMPAT | ENT_HTML401, "UTF-8")."</pre>\n";
echo "Binary File Size "._format_bytes(filesize($cfout))."<br />\n"; }

/*
$ci = 0;
if(!isset($_POST['stdin'])) {
$stdinexp = null;
$cprocount = 0; }
if(isset($_POST['stdin'])) {
$stdinexp = explode("\n", $_POST['stdin']); 
$cprocount = count($stdinexp); }
*/
$ci = 0;
if(isset($_POST['stdin'])) {
$stdinexp = explode("\n", $_POST['stdin']); 
$cprocount = count($stdinexp);
while ($ci < $cprocount) {
$stdinexp[$ci] = trim($stdinexp[$ci]);
	++$ci; } 
$_POST['stdin'] = implode("\n", $stdinexp); }
if(is_resource($cprocess)) {
    // $pipes now looks like this:
    // 0 => writeable handle connected to child stdin
    // 1 => readable handle connected to child stdout
    // Any error output will be appended to /tmp/error-output.txt


/*
	while ($ci < $cprocount) {
    fwrite($pipes[0], trim($stdinexp[$ci]));
	++$ci; }

    fwrite($pipes[0], null);
    fclose($pipes[0]);
*/

	if(isset($_POST['stdin'])) {
    fwrite($pipes[0], $_POST['stdin']);
    fclose($pipes[0]); }

	if(!isset($_POST['stdin'])) {
    fwrite($pipes[0], null);
    fclose($pipes[0]); }

    $crunout = htmlspecialchars(stream_get_contents($pipes[1]), ENT_COMPAT | ENT_HTML401, "UTF-8");
    fclose($pipes[1]);

	$cstdoutread = file_get_contents($cstdout);
	//if($cstdoutread==""||$cstdoutread==null) {
	//echo "<br />\n"; }
	if($cstdoutread!=""&&$cstdoutread!=null) {
	echo "<a href=\"compile.php?act=compile&amp;#\" onclick=\"toggletag('hidestdout'); return false;\">Show Program Log</a><br />\n<pre style=\"border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff; display: none;\" id=\"hidestdout\">".htmlspecialchars(file_get_contents($cstdout), ENT_COMPAT | ENT_HTML401, "UTF-8")."</pre>\n"; }

    // It is important that you close any pipes before calling
    // proc_close in order to avoid a deadlock
    $return_value = proc_close($cprocess);

	echo "System Info ".$sysname."<br />\n";
	echo "Current Process Id ".$cprocinfo['pid']."<br />\n";
    echo "Command Returned ".$return_value."<br />\n";
    $ProEnd = getTime();
    echo "Execution Time ".number_format(($ProEnd - $ProStart),2)." secs<br />\n";

	if($crunout==""||$crunout==null) {
	echo "<br />\n"; }
	if($crunout!=""&&$crunout!=null) {
    echo "<a href=\"compile.php?act=compile&amp;#\" onclick=\"toggletag('hideprout'); return false;\">Show Program Output</a><br />\n<pre style=\"border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff; display: none;\" id=\"hideprout\">".$crunout."</pre><br />\n"; }

echo "<hr /><br />\n";

} }

if(file_exists($cfname)) { unlink($cfname); }
if(file_exists($cfout)) { unlink($cfout); }
if(file_exists($clogout)) { unlink($clogout); } 
if(file_exists($cstdout)) { unlink($cstdout); } }
$showother = " style=\"display: none;\"";
if(!isset($_POST['lang'])) { $showother = " style=\"display: none;\""; }
if(isset($_POST['lang'])&&$_POST['lang']!="other") { $showother = " style=\"display: none;\""; }
if(isset($_POST['lang'])&&$_POST['lang']=="other") { $showother = ""; }
?>

<form id="compilecmd" name="compilecmd" method="post" action="compile.php?act=compile">
<?php if(!isset($_POST['code'])) { 
$c_code = <<<EOD
#include <stdio.h>

int main(int argc, char *argv[])
{
	printf("Hello, world\\n");
	return 0;
}

EOD;
?>
<textarea style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" name="code" rows="33" cols="138"><?php echo htmlspecialchars($c_code, ENT_COMPAT | ENT_HTML401, "UTF-8"); ?></textarea>
<?php } if(isset($_POST['code'])) { ?>
<textarea style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" id="code" name="code" rows="33" cols="138"><?php echo htmlspecialchars($_POST['code'], ENT_COMPAT | ENT_HTML401, "UTF-8"); ?></textarea>
<?php } ?><br /><label for="lang">Language:</label>&nbsp;
<?php if(!isset($_POST['lang'])) { ?>
<select style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" onchange="if(document.compilecmd.lang.value=='other') { change_display('comothers', ''); change_display('extothers', ''); change_display('comstdins', ''); } if(document.compilecmd.lang.value!='other') { change_display('comothers', 'none'); change_display('extothers', 'none'); change_display('comstdins', 'none'); }" id="lang" name="lang"><option value="c">C</option><option value="cpp">C++</option><option value="fortran">Fortran</option><option value="other">Other</option></select>
<?php } if(isset($_POST['lang'])) { ?>
<select style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" onchange="if(document.compilecmd.lang.value=='other') { change_display('comothers', ''); change_display('extothers', ''); change_display('comstdins', ''); } if(document.compilecmd.lang.value!='other') { change_display('comothers', 'none'); change_display('extothers', 'none'); change_display('comstdins', 'none'); }" id="lang" name="lang"><option value="c"<?php if($_POST['lang']=="c") { ?> selected="selected"<?php } ?>>C</option><option value="cpp"<?php if($_POST['lang']=="cpp") { ?> selected="selected"<?php } ?>>C++</option><option value="fortran"<?php if($_POST['lang']=="fortran") { ?> selected="selected"<?php } ?>>Fortran</option><option value="other"<?php if($_POST['lang']=="other") { ?> selected="selected"<?php } ?>>Other</option></select>
<?php } ?><br /><span id="comothers"<?php echo $showother; ?>><label for="comother">Other Compiler Command:</label>&nbsp;
<?php if(!isset($_POST['comother'])) { ?>
<input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="text" id="comother" name="comother" size="115" value="<?php echo htmlspecialchars($other_cmd, ENT_COMPAT | ENT_HTML401, "UTF-8"); ?>" />
<?php } if(isset($_POST['comother'])) { ?>
<input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="text" id="comother" name="comother" size="115" value="<?php echo htmlspecialchars($_POST['comother'], ENT_COMPAT | ENT_HTML401, "UTF-8"); ?>" />
<?php } ?><br /></span><span id="extothers"<?php echo $showother; ?>><label for="extother">Other File Extension:</label>&nbsp;
<?php if(!isset($_POST['extother'])) { ?>
<input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="text" id="extother" name="extother" size="120" value="<?php echo htmlspecialchars($other_ext, ENT_COMPAT | ENT_HTML401, "UTF-8"); ?>" />
<?php } if(isset($_POST['extother'])) { ?>
<input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="text" id="extother" name="extother" size="120" value="<?php echo htmlspecialchars($_POST['extother'], ENT_COMPAT | ENT_HTML401, "UTF-8"); ?>" />
<?php } ?><br /></span><label for="comparg">Compiler Arguments:</label>&nbsp;
<?php if(!isset($_POST['comparg'])) { ?>
<input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="text" id="comparg" name="comparg" size="120" />
<?php } if(isset($_POST['comparg'])) { ?>
<input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="text" id="comparg" size="120" name="comparg" value="<?php echo htmlspecialchars($_POST['comparg'], ENT_COMPAT | ENT_HTML401, "UTF-8"); ?>" />
<?php } ?><br /><label for="arg">Command Arguments:</label>&nbsp;
<?php if(!isset($_POST['arg'])) { ?>
<input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="text" id="arg" name="arg" size="119" />
<?php } if(isset($_POST['arg'])) { ?>
<input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="text" id="arg" name="arg" size="119" value="<?php echo htmlspecialchars($_POST['arg'], ENT_COMPAT | ENT_HTML401, "UTF-8"); ?>" />
<?php } ?><br /><span id="comstdins"<?php echo $showother; ?>><label for="comstdin">Compiler Standard-In:</label><br />
<?php if(!isset($_POST['comstdin'])) { ?>
<textarea style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" id="comstdin" name="comstdin" rows="5" cols="138"></textarea>
<?php } if(isset($_POST['comstdin'])) { ?>
<textarea style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" id="comstdin" name="comstdin" rows="5" cols="138"><?php echo htmlspecialchars($_POST['comstdin'], ENT_COMPAT | ENT_HTML401, "UTF-8"); ?></textarea>
<?php } ?></span><label for="stdin">Command Standard-In:</label><br />
<?php if(!isset($_POST['stdin'])) { ?>
<textarea style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" id="stdin" name="stdin" rows="5" cols="138"></textarea>
<?php } if(isset($_POST['stdin'])) { ?>
<textarea style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" id="stdin" name="stdin" rows="5" cols="138"><?php echo htmlspecialchars($_POST['stdin'], ENT_COMPAT | ENT_HTML401, "UTF-8"); ?></textarea>
<?php } ?>
<input style="display: none;" type="hidden" id="act" name="act" value="compile" />
<br /><input style="border: 1px solid #2E8B57; font-family: System, sans-serif, Terminal, monospace; background: #000000; color: #ffffff;" type="submit" />
</form>

 </body>
</html>
