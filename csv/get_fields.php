#!/usr/bin/php
<?php

require_once(__DIR__."/functions.inc.php");

$csv=getenv("csv");
$range=parse_colnum(getenv("columns"));
$rangef=array_flip($range);
$delim=getenv("delimiter");

$c=fopen($csv,"r");
$header=fgetcsv($c,false,$delim);
fclose($c);

json_init();

$last1=end($header);
$last2=end($range);

foreach ($header as $num=>$column) {
	if (!array_key_exists($num,$range)) continue;
	json_row();
	json_column("COLUMN",$num);
	json_column("NAME",addslashes($column),true);
	json_row_end($last1==$column||$last2==($num+1));
}

json_end();


