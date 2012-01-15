<?php
function rot13($str) { $ml = strlen($str); $i = 0;
while ($i < $ml) { $ordvalue = ord($str[$i]);
if(($ordvalue>=65&&$ordvalue<=90)||($ordvalue>=97&&$ordvalue<=122)) { 
if(($ordvalue>=65&&$ordvalue<=77)||($ordvalue>=97&&$ordvalue<=109)) {
$nordvalue = $ordvalue + 13; }
if(($ordvalue>=78&&$ordvalue<=90)||($ordvalue>=110&&$ordvalue<=122)) {
$nordvalue = $ordvalue - 13; }
$str[$i] = chr($nordvalue); } ++$i; } return $str; }
?>
