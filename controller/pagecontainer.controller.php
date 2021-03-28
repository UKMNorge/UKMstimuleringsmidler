<?php
// echo 'test';
require_once( UKMwp_innhold::getPath() .'functions/getPage.function.php');

$PAGE_SLUG = str_replace('UKMstimulering_', '', $_GET['page']);

if( isset( $_GET['subpage'] ) ) {
    $PAGE_SLUG = $PAGE_SLUG .'/'. $_GET['subpage'];
}

UKMstimuleringsmidler::addViewData(
	'page', 
	getPage('stimuleringsmidler/' . $PAGE_SLUG)
);