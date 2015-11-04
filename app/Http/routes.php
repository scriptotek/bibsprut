<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function() {
	return Redirect::to('/youtube');
});


Route::group(['middleware' => 'oauth.google'], function () {

	Route::get('/youtube', 'YoutubeController@index');
	Route::resource('events', 'EventsController');
    Route::get('/oauth2callback', function() {
        return Redirect::to('/youtube');
    });

});