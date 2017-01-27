#!/usr/bin/php
<?php

require_once(__DIR__."/functions.inc.php");

$csv=getenv("csv");
$range=parse_colnum(getenv("columns"));
$rangef=array_flip($range);
$head=getenv("header");
$delim=getenv("delimiter");

$c=fopen($csv,"r");

if ($head) {
	$header=fgetcsv($c,false,$delim);
} else {
	$header=Array();
	$i=0;
	foreach ($range as $c) {
		$header[$i]=sprintf("FIELD%d",$i);
		$i++;
	}
}

json_init();
$line=0;
$last=end($range);

while ($row=fgetcsv($c,false,$delim)) {
	$line++;
	json_row();
	json_column("ROW","$line");
	foreach ($range as $num) {
		json_column("FIELD$num",$header[$num]);
		json_column("VALUE$num",$row[$num],$last==$num);
	}
	json_row_end(feof($c));
}
json_end();

fclose($c);

