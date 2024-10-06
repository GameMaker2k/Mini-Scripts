<?php
header("Content-type: text/html; charset=UTF-8");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title> The World Time Server Test </title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <meta name="generator" content="Bluefish 1.0.7"/>
  <meta name="author" content="Cool Dude 2k"/>
  <meta name="keywords" content="The World Time Server, iDB, Test, Site, URLs, iDB Test Site URLs, Cool Dude 2k" />
  <meta name="description" content="The World Time Server Test" />
 </head>

 <body>
<?php
$opts = array('http' => array('header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0\r\n"));
$context = stream_context_create($opts);
$WorldTime = file_get_contents("http://www.worldtimeserver.com/city.html", false, $context);
$WorldTime = preg_replace("/aspxcity/", "aspx?city", $WorldTime);
$WorldTime = preg_replace('/\s\s+/', " ", $WorldTime);
$prepreg[1] = preg_quote("<a href=\"/current_time_in_", "/");
$prepreg[2] = preg_quote(".aspx?city=", "/");
$prepreg[3] = preg_quote("\">", "/");
$prepreg[4] = preg_quote("</a><br />", "/");
preg_match_all("/".$prepreg[1]."(.*)".$prepreg[2]."(.*)".$prepreg[3]."(.*)".$prepreg[4]."/isU", $WorldTime, $clist);
$CCode = $clist[1];
$SCode = $clist[2];
$LCode = $clist[3];
?>
<form method="get" action="">
<select id="CTimeGet" name="CTimeGet" class="TextBox"><?php
$i = 0;
$num = count($CCode);
if (isset($_GET['CTimeGet'])) {
    $PreCTimeExp = explode("&", $_GET['CTimeGet']);
}
while ($i < $num) {
    if ($PreCTimeExp[0] == $CCode[$i] && $PreCTimeExp[1] == $SCode[$i]) {
        echo "<option selected=\"selected\" value=\"".$CCode[$i]."&amp;".$SCode[$i]."\">".$LCode[$i]."</option>\n";
    }
    if ($PreCTimeExp[0] != $CCode[$i] || $PreCTimeExp[1] != $SCode[$i]) {
        echo "<option value=\"".$CCode[$i]."&amp;".$SCode[$i]."\">".$LCode[$i]."</option>\n";
    }
    ++$i;
}
?></select><br />
<input type="submit" /><input type="reset" />
</form>
<?php if (isset($_GET['CTimeGet'])) {
    $WorldTime = null;
    $prepreg = null;
    $CTimeExp = explode("&", $_GET['CTimeGet']);
    $opts = array('http' => array('header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:65.0) Gecko/20100101 Firefox/65.0\r\n"));
    $context = stream_context_create($opts);
    $WorldTime = file_get_contents("http://www.worldtimeserver.com/current_time_in_".$CTimeExp[0].".aspx?city=".$CTimeExp[1], false, $context);
    $prepreg[1] = preg_quote("<td valign=\"top\">", "/");
    $prepreg[2] = preg_quote("</td>", "/");
    preg_match_all("/".$prepreg[1]."(.*)".$prepreg[2]."/isU", $WorldTime, $timeget);
    $EUpreg["PL"] = preg_quote("images/maps/", "/");
    $timeget = $timeget[1][0];
    $timeget = preg_replace("/".$EUpreg["PL"]."/", "http://www.worldtimeserver.com/images/maps/", $timeget);
    $prepreg[3] = preg_quote("<div style=\"width: 360px\">", "/");
    $prepreg[4] = preg_quote("<div style=\"text-align: center\">", "/");
    preg_match_all("/".$prepreg[3]."(.*)".$prepreg[4]."/isU", $timeget, $cname);
    echo $cname = preg_replace('/\s\s+/', " ", $cname[1][0]);
    echo "<br />\n";
    $prepreg[7] = preg_quote("</font> -->", "/");
    $prepreg[8] = preg_quote("<br />", "/");
    preg_match_all("/".$prepreg[7]."(.*)".$prepreg[8]."/isU", $timeget, $cdate);
    echo $cdate = preg_replace('/\s\s+/', " ", $cdate[1][0]);
    $prepreg[5] = preg_quote("<span class=\"font7\">", "/");
    $prepreg[6] = preg_quote("</span>", "/");
    preg_match_all("/".$prepreg[5]."(.*)".$prepreg[6]."/isU", $timeget, $ctime);
    echo $ctime = preg_replace('/\s\s+/', " ", $ctime[1][0]);
    echo "<br />\n";
    $prepreg[9] = preg_quote("<span class=\"font1\">", "/");
    $prepreg[10] = preg_quote("</span>", "/");
    preg_match_all("/".$prepreg[9]."(.*)".$prepreg[10]."/isU", $timeget, $ctz);
    echo $ctz = preg_replace('/\s\s+/', " ", $ctz[1][0]);
    $prepreg[11] = preg_quote("<img ", "/");
    $prepreg[12] = preg_quote(" />", "/");
    preg_match_all("/".$prepreg[11]."(.*)".$prepreg[12]."/isU", $timeget, $cimg);
    echo "<br />\n";
    echo $cimg = "<img ".preg_replace('/\s\s+/', " ", $cimg[1][0])." />";
}
echo "<br />\n";
?>
The times and dates are retrieved from <a href="http://www.worldtimeserver.com/">The World Time Server</a>
 </body>
</html>
