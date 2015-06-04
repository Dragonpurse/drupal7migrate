<?php

function migrate_articles_migrate_api() {
	$api = array(
		'api' => 2,
		'groups' => array(
		  'articles' => array(
		    'title' => t('Articles migration'),
		  ),
		),
		'migrations' => array(
		  	'Article' => array(
		  		'class_name' => 'ArticleMigration',
		  		'group_name' => 'articles'
			),
		),
	);

	return $api;
}
