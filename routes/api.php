<?php

Route::get('/get-to-dos', 'ToDoController@get');
Route::get('/get-reminder-days', 'ToDoController@getReminderDays');
Route::post('/store-to-do', 'ToDoController@store');
