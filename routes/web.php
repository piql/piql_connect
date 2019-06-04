<?php

Route::get('login', array('uses' => 'Auth\LoginController@showLogin'));

Route::get('/', function () {
    return view('dashboard');
})->middleware('auth');

Route::post('/', function () {
	return view('dashboard');
})->middleware('auth');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
