<?php
function file_get_source($filename,$return = TRUE)
{
$phpsrc = file_get_contents($filename);
$phpsrcs = highlight_string($phpsrc,$return);
$phpsrcs = preg_replace("/\<font color=\"(.*?)\"\>/i", "<span style=\"color: \\1;\">", $phpsrcs);
$phpsrcs = preg_replace("/\<\/font>/i", "</span>", $phpsrcs);
return $phpsrcs;
}
?>
