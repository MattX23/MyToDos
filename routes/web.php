<?php

use Illuminate\Support\Facades\Route;

Route::get('/', 'AuthenticationController')->name('auth');

Auth::routes();

Route::get('/home', 'HomeController')->name('home');
