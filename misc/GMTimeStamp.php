<?php

// Make a GMT Time Stamp
function GMTimeStamp()
{
    $GMTHour = gmdate("H");
    $GMTMinute = gmdate("i");
    $GMTSecond = gmdate("s");
    $GMTMonth = gmdate("n");
    $GMTDay = gmdate("d");
    $GMTYear = gmdate("Y");
    return mktime($GMTHour, $GMTMinute, $GMTSecond, $GMTMonth, $GMTDay, $GMTYear);
}
function GMTimeStampS()
{
    return time() - date('Z', time());
}
