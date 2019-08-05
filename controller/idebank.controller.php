<?php

if( isset( $_GET['post'] ) ) {
	UKMstimuleringsmidler::setAction('nyhet');
}

require_once( UKMwp_innhold::getPath() .'functions/getCategory.function.php');
$category = getCategory('stimuleringsmidler');
$POST_QUERY = 'cat='. $category->term_id;

require_once( UKMwp_innhold::getPath() .'controller/news.controller.php');

foreach( ['post', 'news', 'current_user_name', 'pagination_next', 'pagination_prev', 'page'] as $key ) {
	if( isset( $TWIGdata[ $key ] ) ) {
		UKMstimuleringsmidler::addViewData( $key, $TWIGdata[$key] );
	}
}