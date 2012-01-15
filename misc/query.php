<?php
function query($query_string,$query_vars)
{
   $query_array = array(array("%i","%I","%F","%S"),array("%d","%d","%f","%s"));
   $query_string = str_replace($query_array[0], $query_array[1], $query_string);
   if (get_magic_quotes_gpc()) {
       $query_vars  = array_map("stripslashes", $query_vars); }
   $query_vars = array_map("mysql_real_escape_string", $query_vars);
   $query_val = $query_vars;
   $query_num = count($query_val);
   $query_i = 0;
   while ($query_i < $query_num) {
   $query_is = $query_i+1;
   $query_val[$query_is] = $query_vars[$query_i];
   ++$query_i; }
   $query_val[0] = $query_string;
   return call_user_func_array("sprintf",$query_val);
}
?>