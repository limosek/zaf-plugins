<?php

function help() {
	echo "Parameters: reservation_time interval\n"
		."Where interval can be 'hour','day' or two datetimes\n"
		."example1: isfirst '2016-01-01 14:00' day\n"
		."example2: isfirsr '2016-01-01 14:00' '2016-01-01 00:00' '2016-01-03 00:00'\n";
	exit(1);
}

	error_reporting(0);
	session_start();
	$time=$argv[1];
	$type=$argv[2];
	$range=$argv[3];

	define("YOURTIMEZONE",getenv("timezone"));
	define("BOOKEDWEBSERVICESURL",getenv("url"));

	require_once(__DIR__ . "/bookedapi.php");

	$tz=New DateTimeZone(getenv("timezone"));

	if ($time!="") {
		$timed=New DateTime(strtr($time,"_"," "));
		$timed->setTimezone($tz);
		$time=$timed->getTimestamp();
	} else {
		help();
	}
	switch ($type) {
		case "day":
			$startd=New DateTime("00:00",$tz);			
			$endd=New DateTime("23:59",$tz);
			$start=$startd->getTimestamp();
			$end=$endd->getTimestamp();
		break;

		case "hour":
			$startd=New DateTime(date("H").":00",$tz);
			$endd=New DateTime(date("H").":59",$tz);
			$start=$startd->getTimestamp();
			$end=$endd->getTimestamp();
		break;
		default:
			$startd=New DateTime($type,$tz);
			if ($range) {
				$endd=New DateTime($range,$tz);
				$start=$startd->getTimestamp();
				$end=$endd->getTimestamp();
			} else {
				help();
			}
	}
	
	$bookedapiclient = new bookedapiclient(getenv("username"), getenv("password"));
	$bookedapiclient-> authenticate();
 	$reservations=$bookedapiclient->getReservation(null,null,null,date("c",$start),date("c",$end));
	
	$r=$reservations["reservations"][0];
	$rstart=New DateTime($r["startDate"]);
	$rstart->setTimezone($tz);
	$rend=New DateTime($r["endDate"]);
	$rend->setTimezone($tz);
	$rsstart=$rstart->getTimestamp();
	$rsend=$rend->getTimestamp();

	#echo $rstart->format("Y-m-d H:i") . "," . $rend->format("Y-m-d H:i") . "," . $timed->format("Y-m-d H:i") . "\n";

	if ($rsstart<=$time && $rsend>=$time) {
		echo "1\n";
	} else {
		echo "0\n";
	}



