<?php

// Make fractions from decimals by Kazuki
function dec2fraction($number, $altver = false)
{
    if (!is_float($number)) {
        echo "The number is not a decimal.";
        return false;
    }
    if ($altver !== true && $altver !== false) {
        $altver = false;
    }
    $returnvar = null;
    $numexp = explode(".", $number);
    $number = $numexp[0];
    $decnum = pow(10, strlen($numexp[1]));
    if ((int) $decnum === 0 && $number === 0) {
        echo "Cannot divide by zero.";
        return false;
    }
    if (isset($number)) {
        if (!is_numeric($number)) {
            $number = 0;
        }
        if (is_numeric($number) && is_string($number)) {
            $number = (int) $number;
        }
    }
    if (!is_numeric($numexp[0])) {
        $numexp[0] = 0;
    }
    if (is_numeric($numexp[0]) && is_string($numexp[0])) {
        $numerator = (int) $numexp[0];
    }
    if (!is_numeric($decnum)) {
        $decnum = 0;
    }
    if (is_numeric($decnum) && is_string($decnum)) {
        $decnum = (int) $decnum;
    }
    if ($altver === false) {
        if (isset($number)) {
            $returnvar = $number." ";
        }
        if ($numexp[1] > 0 && $decnum > 0) {
            return $returnvar .= $numexp[1]."/".$decnum;
        }
    }
    if ($altver === true) {
        if (isset($number)) {
            $returnvar['number'] = $number;
        }
        if ($numexp[1] > 0 && $decnum > 0) {
            $returnvar['numerator'] = $numexp[1];
            $returnvar['denominator'] = $decnum;
        }
        if ($numexp[1] === 0 && $decnum === 0) {
            $returnvar['numerator'] = 0;
            $returnvar['denominator'] = 0;
        }
        return $returnvar;
    }
}
