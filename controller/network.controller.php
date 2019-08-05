<?php

function UKMsmadmin_page() {

	$frister = array();
	$frister = UKMstimulering_frister();

	$today = date("c");
	
	$redDate = $today+7*3600*24; // 7 days from today
	$yellowDate = $today+21*3600*24; // 3 weeks from today

	$r = array();
	$i = 0;
	foreach ($frister as $frist) {
		$r[$i]['frist'] = $frist;
		$r[$i]['f'] = dato($frist->getTimestamp(), "d. M");
		$i++;
	}

	return $r;
}

function UKMstimulering_frister() {
	$d = new DateTime();
	$frister = array();
	
	// Få riktig frist etter ny sesong er satt opp (sept/okt)
	if( (int)get_site_option('season') > (int) date('Y') ) {
		$year = get_site_option('season');
	} else {
		$year = (int)date('Y');
	}
	
	// 1. januar
	$d->setDate($year, "04", "01");
	$d->setTime("01", "00", "00");
	// Clone ensures that PHP assigns by value, not reference.
	$frister[] = clone $d;
	// 15. april
#	$d->setDate($year, "04", "15");
#	$frister[] = clone $d;
	// 15. oktober
	$d->setDate($year, "10", "01");
	$frister[] = clone $d;

	return $frister;
}

if (!function_exists("dato")) {
	function dato($time, $format) {
		if( is_string( $time ) && !is_numeric( $time ) ) {
			$time = strtotime($time);
		}
		$date = date($format, $time);
		return str_replace(array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday',
								 'Mon','Tue','Wed','Thu','Fri','Sat','Sun',
								 'January','February','March','April','May','June',
								 'July','August','September','October','November','December',
								 'Jan','Feb','Mar','Apr','May','Jun',
								 'Jul','Aug','Sep','Oct','Nov','Dec'),
						  array('mandag','tirsdag','onsdag','torsdag','fredag','lørdag','søndag',
						  		'man','tir','ons','tor','fre','lør','søn',
						  		'januar','februar','mars','april','mai','juni',
						  		'juli','august','september','oktober','november','desember',
						  		'jan','feb','mar','apr','mai','jun',
						  		'jul','aug','sep','okt','nov','des'), 
						  $date);
	}
}