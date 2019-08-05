<?php

require_once( UKMwp_innhold::getPath() .'functions/getPage.function.php');

UKMstimuleringsmidler::addViewData(
	'page', 
	getPage('stimuleringsmidler')
);