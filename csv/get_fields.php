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

$last=end($header);
foreach ($header as $num=>$column) {
	if (!array_key_exists($num,$range)) continue;
	json_row();
	json_column("FIELD","$column",true); 
	json_row_end($last==$column);
}

json_end();


