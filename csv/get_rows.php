#!/usr/bin/php
<?php

require_once(__DIR__."/functions.inc.php");

$csv=getenv("csv");
$range=parse_colnum(getenv("columns"));
$rows=parse_colnum(getenv("rows"));
$rowsf=array_flip($rows);
$rangef=array_flip($range);
$head=getenv("header");
$delim=getenv("delimiter");

$c=fopen($csv,"r");

if ($head) {
	$header=fgetcsv($c,false,$delim);
} else {
	$header=Array();
	$i=0;
	foreach ($range as $r) {
		$header[$i]=sprintf("FIELD%d",$i);
		$i++;
	}
}

json_init();
$line=0;
$last=end($range);
$lastrow=max($rows);

while ($row=fgetcsv($c,false,$delim)) {
	$line++;
	if (!array_key_exists($line,$rowsf)) continue;
	json_row();
	json_column("ROW","$line");
	foreach ($range as $num) {
		$col=$num-1;
		json_column("COLUMN$col",$header[$col]);
		json_column("VALUE$col",addslashes($row[$col]),$last==$num);
	}
	json_row_end(feof($c)||$line==$lastrow);
}
json_end();

fclose($c);

