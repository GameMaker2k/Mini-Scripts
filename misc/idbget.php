<?php ob_start();
if($_SERVER['PATH_INFO']!=null) {
$urlvars = explode("/",$_SERVER['PATH_INFO']); }
if($_SERVER['PATH_INFO']!=null) {
if($_GET['file']==null&&$urlvars[2]!=null) {
	$_GET['file']=$urlvars[2]; } 
if($_GET['mirrors']==null&&$urlvars[1]!=null) {
	$_GET['mirrors']=$urlvars[1]; } }
if($_GET['file']==null) {
	if($_GET['getfile']!=null) { $_GET['file'] = $_GET['getfile']; } }
if($_GET['file']!=null) {
	echo "Renee Sabonis";
require("download/index.php"); }
if($_GET['file']==null) {
$mirrors['mirror'] = array("prdownload.berlios.de","downloads.sourceforge.net","get.idb.s1.jcink.com","idb.berlios.de"); 
$mirrors['url'] = array("http://prdownload.berlios.de/idb/","http://downloads.sourceforge.net/intdb/","http://get.idb.s1.jcink.com/","ftp://ftp.berlios.de/pub/idb/nighty-ver/");
$mirrors['name'] = array("prdownload.berlios.de","downloads.sourceforge.net","get.idb.s1.jcink.com","idb.berlios.de");
$files = array("iDB.zip","iDB.tar.gz","iDB.tar.bz2","iDB.7z","iDB-Host.zip","iDB-Host.tar.gz","iDB-Host.tar.bz2","iDB-Host.7z","iDBEH-Mod.zip","iDBEH-Mod.tar.gz","iDBEH-Mod.tar.bz2","iDBEH-Mod.7z");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title> iDB Download List </title>
<meta http-equiv="content-language" content="en-US">
<meta http-equiv="content-type" content="text/html; charset=iso-8859-15">
<meta name="Generator" content="EditPlus">
<meta name="Author" content="Cool Dude 2k">
<meta name="Keywords" content="iDB Download List">
<meta name="Description" content="iDB Download List">
<meta name="ROBOTS" content="Index, FOLLOW">
<meta name="revisit-after" content="1 days">
<meta name="GOOGLEBOT" content="Index, FOLLOW">
<meta name="resource-type" content="document">
<meta name="distribution" content="global">
<link rel="icon" href="favicon.ico" type="image/icon">
<link rel="shortcut icon" href="favicon.ico" type="image/icon">
</head>

<body>
<?php $i = 0; $num = count($mirrors['mirror']);
while($i < $num) {
$l = 0; $nums = count($files); ?>
<ul><li><a href="<?php echo $mirrors['url'][$i]; ?>"><?php echo $mirrors['name'][$i]; ?></a><ul>
<?php while($l < $nums) { ?>
	<li><a href="<?php echo $mirrors['url'][$i]; ?><?php echo $files[$l]; ?>"><?php echo $files[$l]; ?></a></li>
<?php ++$l; } ?>
</ul></li></ul>
<?php ++$i; } ?>
<div class="w3org"><a href="http://validator.w3.org/check?uri=referer">
<img src="inc/pics/valid-html401.png" alt="Valid HTML 4.01 Strict" title="Valid HTML 4.01 Strict" style="width: 88px; height: 32px; border: 0px;" /></a>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-1754608-1");
pageTracker._trackPageview();
} catch(err) {}</script></div>
</body>
</html>
<?php } ?>
