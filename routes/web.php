<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('reservations');
});

Route::get('/upload', [App\Http\Controllers\ReservationController::class, 'uploadForm']);
Route::post('/upload', [App\Http\Controllers\ReservationController::class, 'storeCSV'])->name('store.csv');

Route::get('/reservations', [App\Http\Controllers\ReservationController::class, 'index'])->name('reservations.index');
Route::get('/reservations/create', [App\Http\Controllers\ReservationController::class, 'create'])->name('reservations.create');
Route::get('/reservations/{reservation}/edit', [App\Http\Controllers\ReservationController::class, 'edit'])->name('reservations.edit');
Route::put('/reservations/{reservation}', [App\Http\Controllers\ReservationController::class, 'update'])->name('reservations.update');
Route::delete('/reservations/{reservation}', [App\Http\Controllers\ReservationController::class, 'destroy'])->name('reservations.destroy');
