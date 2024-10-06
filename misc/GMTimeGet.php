<?php

function GMTimeGet($format, $offset, $minoffset = null, $dst = null)
{
    return GMTimeChange($format, GMTimeStamp(), $offset, $minoffset, $dst);
}
function GMTimeGetS($format, $offset, $minoffset = null, $dst = null)
{
    $dstake = null;
    if (!is_numeric($offset)) {
        $offset = "0";
    }
    if (!is_numeric($minoffset)) {
        $minoffset = "00";
    }
    $ts_array = explode(":", $offset);
    if (count($ts_array) != 2) {
        if (!isset($ts_array[0])) {
            $ts_array[0] = "0";
        }
        if (!isset($ts_array[1])) {
            $ts_array[1] = "00";
        }
        $offset = $ts_array[0].":".$ts_array[1];
    }
    if (!is_numeric($ts_array[0])) {
        $ts_array[0] = "0";
    }
    if ($ts_array[0] > 12) {
        $ts_array[0] = "12";
        $offset = $ts_array[0].":".$ts_array[1];
    }
    if ($ts_array[0] < -12) {
        $ts_array[0] = "-12";
        $offset = $ts_array[0].":".$ts_array[1];
    }
    if (!is_numeric($ts_array[1])) {
        $ts_array[1] = "00";
    }
    if ($ts_array[1] > 59) {
        $ts_array[1] = "59";
        $offset = $ts_array[0].":".$ts_array[1];
    }
    if ($ts_array[1] < 0) {
        $ts_array[1] = "00";
        $offset = $ts_array[0].":".$ts_array[1];
    }
    $tsa = array("offset" => $offset, "hour" => $ts_array[0], "minute" => $ts_array[1]);
    //$tsa['minute'] = $tsa['minute'] + $minoffset;
    if ($dst != "on" && $dst != "off") {
        $dst = "off";
    }
    if ($dst == "on") {
        if ($dstake != "done") {
            $dstake = "done";
            $tsa['hour'] = $tsa['hour'] + 1;
        }
    }
    return date($format, mktime(gmdate('h') + $tsa['hour'], gmdate('i') + $tsa['minute'], gmdate('s'), gmdate('n'), gmdate('j'), gmdate('Y')));
}
