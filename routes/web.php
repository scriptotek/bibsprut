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
Route::get('/harvests/log', 'HarvestsController@log');

Route::get('/videos', 'VideosController@index');
Route::get('/videos/{id}', 'VideosController@show');
Route::put('/videos/{id}/updateEntities', 'VideosController@updateEntities');
Route::get('/videos/{id}/hide', 'VideosController@hide')->middleware('can:edit');
Route::get('/feed', 'VideosController@feed');

Route::get('/entities.json', 'EntitiesController@json');
Route::resource('entities', 'EntitiesController');

Route::get('/relations.json', 'RelationController@json');
Route::resource('relations', 'RelationController');

// Route::get('/relations', 'RelationController@index');
// Route::get('/relations/create', 'RelationController@create')->middleware('can:edit');
// Route::get('/relations/edit/{id}', 'RelationController@edit')->middleware('can:edit');
// Route::post('/relations/{id}', 'RelationController@update')->middleware('can:edit');
// Route::post('/relations', 'RelationController@store')->middleware('can:edit');
// Route::get('/relations/{id}', 'RelationController@show');

Route::get('user/activation/{token}', 'Auth\LoginController@activateUser')->name('user.activate');
Route::get('user/cancel/{token}', 'Auth\LoginController@cancelActivation')->name('user.cancel');

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
