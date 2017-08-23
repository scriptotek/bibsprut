<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function() {
	return Redirect::to('/videos');
});

Route::get('/oauth2init', 'GoogleAuthController@initiate');
Route::get('/oauth2callback', 'GoogleAuthController@processCallback');
Route::get('/oauth2logout', 'GoogleAuthController@logout');

Route::get('/harvests/harvest', 'HarvestsController@harvest');

Route::get('/videos', 'VideosController@index');
Route::get('/videos/{id}/hide', 'VideosController@hide')->middleware('can:edit');
Route::get('/feed', 'VideosController@feed');

Route::get('user/activation/{token}', 'Auth\LoginController@activateUser')->name('user.activate');

Route::resource('users', 'UsersController', ['only' => [
    'index', 'show'
]]);

Route::get('saml2/error', 'Auth\LoginController@error');
Route::post('logout', 'Auth\LoginController@samlLogout');  // override POST route


/*
 * Route::resource('events', 'EventsController');
 * Route::get('events/{id}/resources', 'EventsController@editResources');
 * Route::post('events/{id}/resources/store', 'EventsController@storeResource');
 * Route::post('events/{id}/resources', 'EventsController@updateResources');
 * Route::post('events/{id}', 'EventsController@update');
 */
