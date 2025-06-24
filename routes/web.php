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

// Ruta para el formulario público de registro
Route::get('/formulario', [RegistroController::class, 'formulario'])->name('registros.formulario');

// Ruta API para verificar registros de voluntario en una fecha
Route::get('/api/voluntario-registros', [RegistroController::class, 'checkVoluntarioRegistros'])->name('api.voluntario.registros');

// Ruta API para obtener datos de un voluntario
Route::get('/api/voluntario/{id}', [RegistroController::class, 'getVoluntarioInfo'])->name('api.voluntario.info');

// Ruta API para obtener la dirección de un voluntario
Route::get('/api/voluntario-direccion', [RegistroController::class, 'getVoluntarioDireccion'])->name('api.voluntario.direccion');

// Ruta para exportar registros a Excel
Route::get('/registros/export/excel', [RegistroController::class, 'exportarExcel'])->name('registros.export.excel');

// Ruta API para exportar registros a JSON
Route::get('/api/registros/export/json', [RegistroController::class, 'exportarJson'])->name('api.registros.export.json');
