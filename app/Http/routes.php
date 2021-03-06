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

Route::resource('/', 'DonkeyController');

Route::resource('/new-markets', 'DonkeyController');

Route::get('saved-markets/{market_id}', 'DonkeyController@getStoredMarket');
Route::resource('saved-markets', 'DonkeyController@getStoredMartkets');

Route::get('market/{id}', 'DonkeyController@getStoredMarket');

Route::get('/login/{user_id}', 'DonkeyController@login');

Route::post('/store-market', 'DonkeyController@storeMarket');

Route::post('get-bets', 'DonkeyController@getMarketBook');

Route::post('update-books', 'DonkeyController@updateMarketBook');
