<?php
require("./functions.php");

if(($_GET['act']=="upca"||$_GET['act']=="upce"||$_GET['act']=="ean13")&&isset($_GET['upc'])) {
	if(!isset($_GET['resize'])||!is_numeric($_GET['resize'])||$_GET['resize']<1) { $_GET['resize'] = 1; }
	if(!isset($_GET['imgtype'])) { $_GET['imgtype'] = "png"; }
	if(strlen($_GET['upc'])==8&&validate_upce($_GET['upc'])===true&&$_GET['act']=="upce") {
	create_barcode($_GET['upc'],$_GET['imgtype'],true,$_GET['resize']); }
	if(strlen($_GET['upc'])==12&&validate_upca($_GET['upc'])===true&&$_GET['act']=="upca") {
	create_barcode($_GET['upc'],$_GET['imgtype'],true,$_GET['resize']); }
	if(strlen($_GET['upc'])==13&&validate_ean13($_GET['upc'])===true&&$_GET['act']=="ean13") {
	create_barcode($_GET['upc'],$_GET['imgtype'],true,$_GET['resize']); } }
?>