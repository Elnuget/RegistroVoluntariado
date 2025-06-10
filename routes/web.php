<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoluntarioController;
use App\Http\Controllers\RegistroController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para el manejo de voluntarios
Route::resource('voluntarios', VoluntarioController::class);

// Rutas para el manejo de registros
Route::resource('registros', RegistroController::class);
