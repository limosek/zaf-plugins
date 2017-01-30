<?php


set_error_handler('terminate_on_strict');

function terminate_on_strict($errno, $errstr, $errfile, $errline)
{                               
   fprintf(STDERR,"$errstr in $errfile line $errline\n");
   exit($errno);
}

function parse_colnum($ranges) {
	$groups=preg_split("/,/",$ranges);
	foreach ($groups as $group) {
		$range=preg_split("/-/",$group);
		if (count($range)>1) {
			for ($i=$range[0];$i<=$range[1];$i++) {
				$columns[]=$i;
			}
		} else {
			$columns[]=$group;
		}
	}
	return($columns);
}

function json_init() {
	echo '{ "data":'."\n".' [ '."\n";
}

function json_row() {
	echo "{";
}

function json_row_end($last=false) {
	if ($last) {
		echo " }\n";
	} else {
		echo " },\n";
	}
}

function json_column($name,$value,$last=false) {
	echo sprintf('"{#%s}":"%s"',$name,$value);
	if (!$last) echo ","; 
}

function json_end() {
	echo ' ] }'."\n";
}

function get_replacements($header,$row) {
	$patterns=Array();
	$replacements=Array();
	foreach ($header as $n=>$h) {
		$patterns[]="/{COLUMN:$n}/";
		$replacements[]=$row[$n];
		$patterns[]="/{COLUMN:$h}/";
		$replacements[]=$row[$n];
		$patterns[]="/{column:$n}/";
		$replacements[]=strtolower($row[$n]);
		$patterns[]="/{column:$h}/";
		$replacements[]=strtolower($row[$n]);
	}
	return(Array("patterns" => $patterns, "replacements" => $replacements));
}


