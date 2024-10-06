<?php

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
