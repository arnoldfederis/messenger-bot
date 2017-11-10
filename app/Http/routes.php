<?php

Route::get('facebook_messenger_api', 'MessengerController@index');
Route::post('facebook_messenger_api', 'MessengerController@index');

Route::get('/', function () {
    return view('welcome');
});
