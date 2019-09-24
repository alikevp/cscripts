<?php

	Route::get('/callscripts', 									'Callscripts\Callscripts\HomeController@begin')					->name('begin');
	Route::post('/callscripts/questionnaire', 					'Callscripts\Callscripts\HomeController@getQuestion')			->name('getQuestion');
	Route::get('/callscripts/management', 						'Callscripts\Callscripts\ManagementController@default')			->name('managementDefault');
	Route::post('/callscripts/management/getQuestionDetails', 	'Callscripts\Callscripts\ManagementController@getQuestionData')	->name('getQuestionDetails');
	Route::post('/callscripts/management/update_question', 		'Callscripts\Callscripts\ManagementController@updateQuestion')	->name('updateQuestion');
	Route::post('/callscripts/management/create_question', 		'Callscripts\Callscripts\ManagementController@createQuestion')	->name('createQuestion');
