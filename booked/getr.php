<?php

	error_reporting(0);
	session_start();
	$from=$argv[1];
	$to=$argv[2];
	$start_only=$argv[3];

	define("YOURTIMEZONE",getenv("timezone"));
	define("BOOKEDWEBSERVICESURL",getenv("url"));

	require_once(__DIR__ . "/bookedapi.php");

	$tz=New DateTimeZone(getenv("timezone"));

	if ($from!="") {
		$fromd=New DateTime(strtr($from,"_"," "));
		$fromd->setTimezone($tz);
		$from=$fromd->getTimestamp();
	} else {
		$from=time();
	}
	if ($to!="") {
		$tod=New DateTime(strtr($to,"_"," "));
		$tod->setTimezone($tz);
		$to=$tod->getTimestamp();
	} else {
		$to=time()+3600;
	}
	
	$bookedapiclient = new bookedapiclient(getenv("username"), getenv("password"));
	$bookedapiclient-> authenticate();
 	$reservations=$bookedapiclient->getReservation(null,null,null,date("c",$from),date("c",$to));
	
	$cnt=0;
	foreach ($reservations["reservations"] as $r) {
		$start=New DateTime($r["startDate"]);
		$start->setTimezone($tz);
		$end=New DateTime($r["endDate"]);
		$end->setTimezone($tz);
		$sstart=$start->getTimestamp();
		$send=$end->getTimestamp();
		#echo $fromd->format("Y-m-d H:i") . "," . $start->format("Y-m-d H:i") . "," . $end->format("Y-m-d H:i") . "," . $tod->format("Y-m-d H:i"). "\n";
		#echo $from . "," . $start->getTimestamp() . "," . $end->getTimestamp() . "," . $to . "\n";
		if ($start_only=="yes") {
			if ($sstart>=$from && $sstart<=$to) {
				$cnt++;
			}
		} else {
			if ($sstart>=$from && $send<=$to) {
				$cnt++;
			}
		}
	}

	echo $cnt;


