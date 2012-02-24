<?php
/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the Revised BSD License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    Revised BSD License for more details.

    Copyright 2011-2012 Cool Dude 2k - http://idb.berlios.de/
    Copyright 2011-2012 Game Maker 2k - http://intdb.sourceforge.net/
    Copyright 2011-2012 Kazuki Przyborowski - https://github.com/KazukiPrzyborowski

    $FileInfo: dumpfile.php - Last Update: 02/13/2012 Ver. 2.2.5 RC 1 - Author: cooldude2k $
*/
$File3Name = basename($_SERVER['SCRIPT_NAME']);
if ($File3Name=="dumpfile.php"||$File3Name=="/dumpfile.php") {
	chdir("../");
	require("./upc.php");
	exit(); }

if($_GET['act']=="csv"||$_GET['act']=="dumpcsv") { 
@header("Content-Type: text/csv; charset=UTF-8"); 
@header("Content-Disposition: attachment; filename=\"".$sqlitedatabase.".csv\"");
?>
"upc", "description", "sizeweight"
<?php
if($_GET['subact']=="neighbor"||$_GET['subact']=="neighbors") {
if(!isset($_GET['upc'])||!is_numeric($_POST['upc'])) { 
	$_POST['upc'] = null; $_GET['subact'] = NULL; }
preg_match("/^(\d{7})/", $_GET['upc'], $fix_matches); 
$findprefix = $fix_matches[1];
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."items\" WHERE \"upc\" LIKE '".$findprefix."%';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" WHERE \"upc\" LIKE '".$findprefix."%'  ORDER BY \"upc\" ASC;"); } }
if($_GET['subact']=="lookup") {
if(!isset($_GET['upc'])||!is_numeric($_POST['upc'])) { 
	$_POST['upc'] = null; $_GET['subact'] = NULL; }
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."items\" WHERE \"upc\"='".$_POST['upc']."';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" WHERE \"upc\"='".$_POST['upc']."'  ORDER BY \"upc\" ASC;"); } }
if($_GET['subact']==NULL) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" ORDER BY \"upc\" ASC;"); } 
if($_GET['subact']=="latest") {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" ORDER BY \"lastupdate\";"); } 
if($dumpupc!=NULL) {
while ($upcinfo = sql_fetch_assoc($dumpupc)) {
$upcinfo['description'] = str_replace("\"", "\"\"", $upcinfo['description']);
$upcinfo['sizeweight'] = str_replace("\"", "\"\"", $upcinfo['sizeweight']);
?>
"<?php echo $upcinfo['upc']; ?>", "<?php echo $upcinfo['description']; ?>", "<?php echo $upcinfo['sizeweight']; ?>"
<?php } } } if($_GET['act']=="xml"||$_GET['act']=="dumpxml") { 
@header("Content-Type: text/xml; charset=UTF-8"); 
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<?xml-stylesheet type=\"text/xsl\" href=\"".$website_url.$url_file."?act=xslt\"?>\n";
?>
<!DOCTYPE <?php echo $sqlitedatabase; ?> [
<!ELEMENT <?php echo $sqlitedatabase; ?> (item*)>
<!ELEMENT item (upc,description,sizeweight)>
<!ELEMENT upc (#PCDATA)>
<!ELEMENT description (#PCDATA)>
<!ELEMENT sizeweight (#PCDATA)>
]>

<<?php echo $sqlitedatabase; ?>>

<?php
if($_GET['subact']=="neighbor"||$_GET['subact']=="neighbors") {
if(!isset($_GET['upc'])||!is_numeric($_POST['upc'])) { 
	$_POST['upc'] = null; $_GET['subact'] = NULL; }
preg_match("/^(\d{7})/", $_GET['upc'], $fix_matches); 
$findprefix = $fix_matches[1];
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."items\" WHERE \"upc\" LIKE '".$findprefix."%';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" WHERE \"upc\" LIKE '".$findprefix."%'  ORDER BY \"upc\" ASC;"); } }
if($_GET['subact']=="lookup") {
if(!isset($_GET['upc'])||!is_numeric($_POST['upc'])) { 
	$_POST['upc'] = null; $_GET['subact'] = NULL; }
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."items\" WHERE \"upc\"='".$_POST['upc']."';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" WHERE \"upc\"='".$_POST['upc']."'  ORDER BY \"upc\" ASC;"); } }
if($_GET['subact']==NULL) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" ORDER BY \"upc\" ASC;"); } 
if($_GET['subact']=="latest") {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" ORDER BY \"lastupdate\";"); } 
if($dumpupc==NULL) {
echo "<item>\n</item>\n\n"; }
if($dumpupc!=NULL) {
while ($upcinfo = sql_fetch_assoc($dumpupc)) {
$upcinfo['description'] = str_replace("\"", "\"\"", $upcinfo['description']);
$upcinfo['sizeweight'] = str_replace("\"", "\"\"", $upcinfo['sizeweight']);
?>
<item>
<upc><?php echo $upcinfo['upc']; ?></upc>
<description><?php echo htmlspecialchars($upcinfo['description'], ENT_XML1, "UTF-8"); ?></description>
<sizeweight><?php echo htmlspecialchars($upcinfo['sizeweight'], ENT_XML1, "UTF-8"); ?></sizeweight>
</item>

<?php } } ?>
</<?php echo $sqlitedatabase; ?>>
<?php } if($_GET['act']=="yaml"||$_GET['act']=="dumpyaml") { 
@header("Content-Type: text/x-yaml; charset=UTF-8"); 
@header("Content-Disposition: attachment; filename=\"".$sqlitedatabase.".yaml\"");
?>
item: 
<?php
if($_GET['subact']=="neighbor"||$_GET['subact']=="neighbors") {
if(!isset($_GET['upc'])||!is_numeric($_POST['upc'])) { 
	$_POST['upc'] = null; $_GET['subact'] = NULL; }
preg_match("/^(\d{7})/", $_GET['upc'], $fix_matches); 
$findprefix = $fix_matches[1];
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."items\" WHERE \"upc\" LIKE '".$findprefix."%';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" WHERE \"upc\" LIKE '".$findprefix."%'  ORDER BY \"upc\" ASC;"); } }
if($_GET['subact']=="lookup") {
if(!isset($_GET['upc'])||!is_numeric($_POST['upc'])) { 
	$_POST['upc'] = null; $_GET['subact'] = NULL; }
$findupc = sqlite3_query($slite3, "SELECT COUNT(*) AS COUNT FROM \"".$table_prefix."items\" WHERE \"upc\"='".$_POST['upc']."';"); 
$numupc = sql_fetch_assoc($findupc);
$numrows = $numupc['COUNT'];
if($numrows>0) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" WHERE \"upc\"='".$_POST['upc']."'  ORDER BY \"upc\" ASC;"); } }
if($_GET['subact']==NULL) {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" ORDER BY \"upc\" ASC;"); } 
if($_GET['subact']=="latest") {
$dumpupc = sqlite3_query($slite3, "SELECT * FROM \"".$table_prefix."items\" ORDER BY \"lastupdate\";"); } 
if($dumpupc!=NULL) {
while ($upcinfo = sql_fetch_assoc($dumpupc)) {
$upcinfo['description'] = str_replace("\"", "\"\"", $upcinfo['description']);
$upcinfo['sizeweight'] = str_replace("\"", "\"\"", $upcinfo['sizeweight']);
?>
   - upc:           <?php echo $upcinfo['upc']."\n"; ?>
     description:   <?php echo $upcinfo['description']."\n"; ?>
     sizeweight:    <?php echo $upcinfo['sizeweight']."\n"; ?>

<?php } } } 
if($_GET['act']=="xslt") { 
@header("Content-Type: text/xml; charset=UTF-8"); 
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:template match="/">
 <html xsl:version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/1999/xhtml">
  <body style="background-color:#FFFFFF;">
   <xsl:for-each select="<?php echo $sqlitedatabase; ?>/item">
    <xsl:element name="table">
     <xsl:element name="tr"><xsl:element name="td">EAN/UCC-13</xsl:element><xsl:element name="td"><xsl:element name="img"><xsl:attribute name="src"><?php echo $website_url.$barcode_file; ?>?act=ean13&amp;upc=<xsl:value-of select="upc"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="upc"/></xsl:attribute><xsl:attribute name="alt"><xsl:value-of select="upc"/></xsl:attribute></xsl:element></xsl:element></xsl:element>
     <xsl:element name="tr"><xsl:element name="td">Description</xsl:element><xsl:element name="td"><xsl:value-of select="description"/></xsl:element></xsl:element>
     <xsl:element name="tr"><xsl:element name="td">Size/Weight</xsl:element><xsl:element name="td"><xsl:value-of select="sizeweight"/></xsl:element></xsl:element>
    </xsl:element>
    <xsl:element name="div"><br /></xsl:element>
   </xsl:for-each>
  </body>
 </html>
</xsl:template>
</xsl:stylesheet>
<?php } ?>