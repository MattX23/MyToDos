<?php

Route::get('/get-to-dos/{user}', 'ToDoController@get');
Route::post('/store-to-do/{user}', 'ToDoController@store');
Route::post('/toggle-to-do/{toDo}/{user}', 'ToDoController@toggleStatus');
Route::put('/edit-to-do/{toDo}/{user}', 'ToDoController@edit');
Route::delete('/delete-to-do/{toDo}/{user}', 'ToDoController@delete');
