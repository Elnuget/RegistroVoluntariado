<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VoluntarioController;

Route::get('/', function () {
    return view('welcome');
});

// Rutas para el manejo de voluntarios
Route::resource('voluntarios', VoluntarioController::class);
