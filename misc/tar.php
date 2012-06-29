<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.
 
    Copyright 2004-2011 iDB Support - http://idb.berlios.de/
    Copyright 2004-2011 Game Maker 2k - http://gamemaker2k.org/
    iTar ver. 0.0.5 by Kazuki Przyborowski
 
    $FileInfo: tar.php - Last Update: 06/29/2012 Ver 0.0.5 - Author: cooldude2k $
*/

function file_list_dir($dirname) {
if (!isset($dirnum)) { $dirnum = null; }
$srcfile = array();
$srcdir = array();
if ($handle = opendir($dirname)) {
while (false !== ($file = readdir($handle))) {
      if ($dirnum==null) { $dirnum = 0; }
	  if ($file != "." && $file != ".." && $file != null) {
      if(filetype($dirname.$file)=="file") {
	  $srcfile[$dirnum] = $file; }
      if(filetype($dirname.$file)=="dir") {
	  $srcdir[$dirnum] = $file; }
	  ++$dirnum;
	  } }
if($srcdir!=null) { asort($srcdir); }
if($srcfile!=null) { asort($srcfile); }
if($srcdir!=null&&$srcfile!=null) {
$fulllist = array_merge($srcdir, $srcfile); }
if($srcdir!=null&&$srcfile==null) { $fulllist = $srcdir; }
if($srcdir==null&&$srcfile!=null) { $fulllist = $srcfile; }
closedir($handle); }
 return $fulllist; }
// PHP iTAR Version 0.0.5
// license: Revised BSD license
// Kazuki Przyborowski (http://ja.gamemaker2k.org/)
function tar($tarfile,$indir1="./",$indir2="") {
	clearstatcache();
	$filelist = file_list_dir($indir1);
	$tarhandle = fopen($tarfile, "wb+");
	$i = 0; $num = count($filelist);
	while ($i < $num) {
	$fstats=stat($indir1.$filelist[$i]);
	$fname=str_pad($indir2.$filelist[$i], 100, "\x00");
	$fmode=str_pad(sprintf('%o', fileperms($indir1.$filelist[$i]))."\x20", 8, "\x00");
	$fowner=str_pad($fstats['uid']."\x20\x00", 8, "\x20", STR_PAD_LEFT);
	$fgroup=str_pad($fstats['gid']."\x20\x00", 8, "\x20", STR_PAD_LEFT);
	$fsize=str_pad(decoct($fstats['size'])."\x20", 12, "\x20", STR_PAD_LEFT);
	$fmtime=str_pad(decoct($fstats['mtime']), 12, "\x20");
	$fcksum=str_pad("", 8, "\x20", STR_PAD_LEFT);
	$ftype="0";
	$flinklist=str_pad("", 100, "\x00");
	$fulltar=$fname.$fmode.$fowner.$fgroup.$fsize.$fmtime.$fcksum.$ftype.$flinklist;
	$fulltar=str_pad($fulltar, 512, "\x00");
//	$ftarbytes = unpack('c*', $fulltar);
	$ftarbytes = unpack('C*', $fulltar);
	$il = 1; $numax = count($ftarbytes);
	$fckstotal = 0;
	while ($il <= $numax) {
	$fckstotal = $fckstotal + $ftarbytes[$il];
	++$il; }
	$fcksum=str_pad(decoct($fckstotal)."\x20\x00", 8, "\x20", STR_PAD_LEFT);
	$fulltar=$fname.$fmode.$fowner.$fgroup.$fsize.$fmtime.$fcksum.$ftype.$flinklist;
	$fulltar=str_pad($fulltar, 512, "\x00");
	$fbytesize = 0;
	while ($fbytesize < filesize($indir1.$filelist[$i])) {
	$fbytesize = $fbytesize + 512; }
	$fulltar=$fulltar.str_pad(file_get_contents($indir1.$filelist[$i]), $fbytesize, "\x00");
	fwrite($tarhandle,$fulltar,strlen($fulltar));
	$fwstats=fstat($tarhandle);
	echo ($i + 1)."/".$num." writing: ".$indir2.$filelist[$i]." (".filesize($indir1.$filelist[$i]).") to tar file: ".$tarfile." (".$fwstats['size'].")\n<hr />";
	++$i; }
	fwrite($tarhandle,str_pad("", 1024, "\x00"), 1024);
	fclose($tarhandle);
	return true; }
function itar($tarfile,$indir1="./",$indir2="") {
	return tar($tarfile,$indir1,$indir2); }
echo "<pre>\n<hr />";
itar("./tfbb.tar", "./tfbb/", "");
echo "</pre>";
?>