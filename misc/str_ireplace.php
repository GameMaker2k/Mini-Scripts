<?php
/* str_ireplace for PHP below ver. 5 updated //
//       by Rene Johnson - Cool Dude 2k      //
//      and upaded by Rene Johnson again     */
if (!function_exists('str_ireplace')) {
    function str_ireplace($search, $replace, $subject)
    {
        if (!is_array($search) && is_array($replace)) {
            $search = array($search);
        }
        if (is_array($search) && !is_array($replace)) {
            $replace = array($replace);
        }
        if (is_array($search) && is_array($replace)) {
            $sc = count($search);
            $rc = count($replace);
            $sn = 0;
            if ($sc != $rc) {
                return false;
            }
            while ($sc > $sn) {
                $search[$sn] = preg_quote($search[$sn], "/");
                $subject = preg_replace("/".$search[$sn]."/i", $replace[$sn], $subject);
                ++$sn;
            }
        }
    }
}
