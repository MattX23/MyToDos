<?php

Route::get('/get-to-dos/{user}', 'ToDoController@get');
Route::get('/get-reminder-days', 'ToDoController@getReminderDays');
Route::post('/store-to-do/{user}', 'ToDoController@store');
