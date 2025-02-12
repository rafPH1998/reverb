<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('notification', function () {
    return view('welcome');
});


Route::get('login/{$id}', function (int $id) {
    auth()->loginUsingId($id);
});