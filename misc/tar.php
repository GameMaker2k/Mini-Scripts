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
 
    $FileInfo: tar.php - Last Update: 08/21/2012 Ver 0.0.5 - Author: cooldude2k $
*/

// By: Mike @ http://us3.php.net/manual/en/function.glob.php#106595
if (!function_exists('glob_recursive'))
{
    // Does not support flag GLOB_BRACE
    function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, glob_recursive($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }
}

// PHP iTAR Version 0.0.5
// license: Revised BSD license
// Kazuki Przyborowski (http://ja.gamemaker2k.org/)
function tar($tarfile,$indir1="./",$indir2="") {
	clearstatcache();
	$filelist = glob_recursive($indir1);
	$tarhandle = fopen($tarfile, "wb+");
	$i = 0; $num = count($filelist);
	while ($i < $num) {
	$rftype=filetype($filelist[$i]);
	$fstats=stat($filelist[$i]);
	$fname=str_pad($indir2.$filelist[$i], 100, "\x00");
	$fmode=str_pad(sprintf('%o', fileperms($filelist[$i]))."\x20", 8, "\x00");
	$fowner=str_pad($fstats['uid']."\x20\x00", 8, "\x20", STR_PAD_LEFT);
	$fgroup=str_pad($fstats['gid']."\x20\x00", 8, "\x20", STR_PAD_LEFT);
	$fsize=str_pad(decoct($fstats['size'])."\x20", 12, "\x20", STR_PAD_LEFT);
	$fmtime=str_pad(decoct($fstats['mtime']), 12, "\x20");
	$fcksum=str_pad("", 8, "\x20", STR_PAD_LEFT);
	if($rftype=="dir") { $ftype="5"; }
	if($rftype=="file") { $ftype="0"; }
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
	if($rftype=="file") {
	$fbytesize = 0;
	while ($fbytesize <= filesize($filelist[$i])) {
	$fbytesize = $fbytesize + 512; }
	$fulltar=$fulltar.str_pad(file_get_contents($filelist[$i]), $fbytesize, "\x00");
	fwrite($tarhandle,$fulltar,strlen($fulltar));
	$fwstats=fstat($tarhandle);
	echo ($i + 1)."/".$num." writing: ".$indir2.$filelist[$i]." (".filesize($filelist[$i]).") to tar file: ".$tarfile." (".$fwstats['size'].")\n<hr />"; }
	++$i; }
	fwrite($tarhandle,str_pad("", 1024, "\x00"), 1024);
	fclose($tarhandle);
	return true; }
function itar($tarfile,$indir1="./",$indir2="") {
	return tar($tarfile,$indir1,$indir2); }
echo "<pre>\n<hr />";
itar("./iDB.tar", "iDB/*", "");
echo "</pre>";
?>