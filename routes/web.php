<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Ruta temporal para demostración del login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

