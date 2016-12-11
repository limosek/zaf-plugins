<?php
	error_reporting(0);
	session_start();
	$from=$argv[1];
	$to=$argv[2];

	define("YOURTIMEZONE",getenv("timezone"));
	define("BOOKEDWEBSERVICESURL",getenv("url"));

	require_once(__DIR__ . "/bookedapi.php");

	if ($from) {
		$from=New DateTime(strtr($from,"_"," "));
		$from=$from->getTimestamp();
	} else {
		$from=time();
	}
	if ($to) {
		$to=New DateTime(strtr($to,"_"," "));
		$to=$to->getTimestamp();
	} else {
		$to=time()+3600;
	}
	
	$bookedapiclient = new bookedapiclient(getenv("username"), getenv("password"));
	$bookedapiclient-> authenticate();
 	$reservations=$bookedapiclient->getReservation(null,null,null,date("c",$from),date("c",$to));
	
	$cnt=0;
	foreach ($reservations["reservations"] as $r) {
		$start=New DateTime($r["startDate"]);
		$start->setTimezone(New DateTimeZone(getenv("timezone")));
		$end=New DateTime($r["endDate"]);
		$end->setTimezone(New DateTimeZone(getenv("timezone")));
		$sstart=$start->getTimestamp();
		$send=$end->getTimestamp();
		if ($sstart>=$from && $send<=$to) {
			$cnt++;
		}
	}

	echo $cnt;


