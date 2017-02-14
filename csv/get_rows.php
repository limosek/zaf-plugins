#!/usr/bin/php
<?php
require_once(__DIR__ . "/functions.inc.php");

$csv = getenv("csv");
$range = parse_colnum(getenv("columns"));
$rows = parse_colnum(getenv("rows"));
$rowsf = array_flip($rows);
$rangef = array_flip($range);
$head = getenv("header");
$delim = getenv("delimiter");

$c = fopen($csv, "r");

$header = Array();
$i = 1;
if ($head) {
    $h = fgetcsv($c, false, $delim);
    foreach ($h as $val) {
        $header[$i] = $val;
        $i++;
    }
} else {
    foreach ($range as $r) {
        $header[$i] = sprintf("FIELD%d", $i + 1);
        $i++;
    }
}

json_init();
$line = 0;
$last = end($range);
$lastrow = max($rows);
$numrows = intval(exec("wc -l '$csv'"));

while ($row = fgetcsv($c, false, $delim)) {
    $line++;
    if (!array_key_exists($line, $rowsf))
        continue;
    json_row();
    json_column("ROW", "$line");
    foreach ($range as $col) {
        json_column("COLUMN$col", $header[$col]);
        json_column("VALUE$col", addslashes($row[$col - 1]), $last == $col);
    }
    echo feof($c);
    json_row_end(feof($c) || $line == $lastrow || $line == $numrows-1);
}
json_end();

fclose($c);

