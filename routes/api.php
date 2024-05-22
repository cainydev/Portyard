<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/auth/token', function (Request $request) {
    return $request->user();
})->middleware('auth.basic');
