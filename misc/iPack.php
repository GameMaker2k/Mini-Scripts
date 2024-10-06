<?php

@error_reporting(E_ALL ^ E_NOTICE);
function file_list_dir($dirname)
{
    if (!isset($dirnum)) {
        $dirnum = null;
    }
    $srcfile = array();
    $srcdir = array();
    if ($handle = opendir($dirname)) {
        while (false !== ($file = readdir($handle))) {
            if ($dirnum == null) {
                $dirnum = 0;
            }
            if ($file != "." && $file != ".." && $file != ".htaccess" && $file != null) {
                if (filetype($dirname.$file) == "file") {
                    $srcfile[$dirnum] = $file;
                }
                if (filetype($dirname.$file) == "dir") {
                    $srcdir[$dirnum] = $file;
                }
                ++$dirnum;
            }
        }
        if ($srcdir != null) {
            asort($srcdir);
        }
        if ($srcfile != null) {
            asort($srcfile);
        }
        if ($srcdir != null && $srcfile != null) {
            $fulllist = array_merge($srcdir, $srcfile);
        }
        if ($srcdir != null && $srcfile == null) {
            $fulllist = $srcdir;
        }
        if ($srcdir == null && $srcfile != null) {
            $fulllist = $srcfile;
        }
        closedir($handle);
    }
    return $fulllist;
}
function gzip($infile, $outfile, $param = 5)
{
    $fp = fopen($infile, "r");
    $data = fread($fp, filesize($infile));
    fclose($fp);
    $zp = gzopen($outfile, "w".$param);
    gzwrite($zp, $data);
    gzclose($zp);
}
function bzip($infile, $outfile)
{
    $fp = fopen($infile, "r");
    $data = fread($fp, filesize($infile));
    fclose($fp);
    $zp = bzopen($outfile, "w");
    bzwrite($zp, $data);
    bzclose($zp);
}
//chdir("C:\iDB\themes");
function make_ipack_1($packname, $ziptype, $delunzip, $encodetype, $decodetype)
{
    if ($packname == null || $encodetype == null ||
        $decodetype == null) {
        return false;
    }
    if (!function_exists($encodetype) ||
        !function_exists($decodetype)) {
        return false;
    }
    $fal = file_list_dir($packname."/");
    $num = count($fal) - 1;
    $i = 0;
    $fp = fopen($packname.".php", "w+");
    $filecont = '<?php'."\n";
    $filecont = $filecont.'if (!file_exists("'.$packname.'")&&filetype("'.$packname.'")!="dir") {'."\n".'mkdir("'.$packname.'"); }'."\n".'chdir("'.$packname.'");'."\n";
    while ($i <= $num) {
        $falc[$i] = file_get_contents($packname."/".$fal[$i]);
        $falc[$i] = $encodetype($falc[$i]);
        $filecont = $filecont.'$filename['."'$i'".'] = "'.$fal[$i].'";'."\n";
        $filecont = $filecont.'$filecont['."'$i'".'] = "'.$falc[$i].'";'."\n";
        $filecont = $filecont.'$filemd5['."'$i'".'] = "'.md5_file($packname."/".$fal[$i]).'";'."\n";
        $filecont = $filecont.'$filesha1['."'$i'".'] = "'.sha1_file($packname."/".$fal[$i]).'";'."\n";
        $filecont = $filecont.'$filecont['."'$i'".'] = '.$decodetype.'($filecont['."'$i'".']);'."\n";
        $filecont = $filecont.'unset($fp);'."\n".'$fp = fopen($filename['."'$i'".'],"w+");'."\n";
        $filecont = $filecont.'fwrite($fp, $filecont['."'$i'".']);'."\n".'fclose($fp);'."\n";
        ++$i;
    }
    $filecont = $filecont.'?>';
    fwrite($fp, $filecont);
    fclose($fp);
    if ($ziptype == "gzip") {
        gzip($packname.".php", $packname.".php.gz");
    }
    if ($ziptype == "bzip2") {
        bzip($packname.".php", $packname.".php.bz2");
    }
    if ($ziptype == "gzip&bzip2" || $ziptype == "bzip2&gzip" ||
        $ziptype == "gzip+bzip2" || $ziptype == "bzip2+gzip") {
        gzip($packname.".php", $packname.".php.gz");
        bzip($packname.".php", $packname.".php.bz2");
    }
    if ($delunzip == true) {
        unlink($packname.".php");
    }
    return true;
}
//make_ipack_1("iDB","gzip&bzip2",false,"base64_encode","base64_decode");

function make_ipack_2($packname, $ziptype, $delunzip, $encodetypeone, $encodetypetwo, $decodetypeone, $decodetypetwo)
{
    if ($packname == null || $encodetypeone == null ||
        $decodetypeone == null) {
        return false;
    }
    if ($encodetypeone != null) {
        if (!function_exists($encodetypeone)) {
            return false;
        }
    }
    if ($encodetypetwo != null) {
        if (!function_exists($encodetypetwo)) {
            return false;
        }
    }
    if ($decodetypeone != null) {
        if (!function_exists($decodetypeone)) {
            return false;
        }
    }
    if ($decodetypetwo != null) {
        if (!function_exists($decodetypetwo)) {
            return false;
        }
    }
    $fal = file_list_dir($packname."/");
    $num = count($fal) - 1;
    $i = 0;
    $fp = fopen($packname.".php", "w+");
    $filecont = '<?php'."\n";
    $filecont = $filecont.'if (!file_exists("'.$packname.'")&&filetype("'.$packname.'")!="dir") {'."\n".'mkdir("'.$packname.'"); }'."\n".'chdir("'.$packname.'");'."\n";
    while ($i <= $num) {
        $falc[$i] = file_get_contents($packname."/".$fal[$i]);
        $falc[$i] = $encodetypeone($falc[$i]);
        $falc[$i] = $encodetypetwo($falc[$i]);
        $filecont = $filecont.'$filename['."'$i'".'] = "'.$fal[$i].'";'."\n";
        $filecont = $filecont.'$filecont['."'$i'".'] = "'.$falc[$i].'";'."\n";
        $filecont = $filecont.'$filemd5['."'$i'".'] = "'.md5_file($packname."/".$fal[$i]).'";'."\n";
        $filecont = $filecont.'$filesha1['."'$i'".'] = "'.sha1_file($packname."/".$fal[$i]).'";'."\n";
        $filecont = $filecont.'$filecont['."'$i'".'] = '.$decodetypeone.'($filecont['."'$i'".']);'."\n";
        $filecont = $filecont.'$filecont['."'$i'".'] = '.$decodetypetwo.'($filecont['."'$i'".']);'."\n";
        $filecont = $filecont.'unset($fp);'."\n".'$fp = fopen($filename['."'$i'".'],"w+");'."\n";
        $filecont = $filecont.'fwrite($fp, $filecont['."'$i'".']);'."\n".'fclose($fp);'."\n";
        ++$i;
    }
    $filecont = $filecont.'?>';
    fwrite($fp, $filecont);
    fclose($fp);
    if ($ziptype == "gzip") {
        gzip($packname.".php", $packname.".php.gz");
    }
    if ($ziptype == "bzip2") {
        bzip($packname.".php", $packname.".php.bz2");
    }
    if ($ziptype == "gzip&bzip2" || $ziptype == "bzip2&gzip" ||
        $ziptype == "gzip+bzip2" || $ziptype == "bzip2+gzip") {
        gzip($packname.".php", $packname.".php.gz");
        bzip($packname.".php", $packname.".php.bz2");
    }
    if ($delunzip == true) {
        unlink($packname.".php");
    }
    return true;
}
//make_ipack_2("Gray","gzip&bzip2",false,"gzcompress","base64_encode","base64_decode","gzuncompress");
