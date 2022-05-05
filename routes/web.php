<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\CursosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::controller(ClientesController::class)->group(function() {
    Route::get('/', 'index');
    Route::post('/registro', 'store');
});

Route::controller(CursosController::class)->group(function() {
    Route::get('/cursos', 'index');
    Route::get('/mostrar', 'show');
    Route::get('/insertar', 'store');
    Route::get('/actualizar', 'update');
    Route::get('/eliminar', 'destroy');
});
