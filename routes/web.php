<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('double');
});

Route::get('/double', function () {
    print_r(22);exit();
    return view('welcome');
});
