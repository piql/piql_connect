<?php

Route::get('login', array('uses' => 'Auth\LoginController@showLogin'));

Route::get('/', function () {
    return view('dashboard');
});

Route::post('/', function () {
	return view('dashboard');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
