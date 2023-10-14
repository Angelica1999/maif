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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/patient/create', [App\Http\Controllers\HomeController::class, 'createPatient'])->name('patient.create');
Route::post('/patient/create/save', [App\Http\Controllers\HomeController::class, 'createPatientSave'])->name('patient.create.save');
Route::get('/patient/pdf', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');

Route::get('/fundsource', [App\Http\Controllers\FundSourceController::class, 'fundSource'])->name('fundsource');
