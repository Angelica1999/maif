<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintController;
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
Route::get('/patient/edit/{patient_id}', [App\Http\Controllers\HomeController::class, 'editPatient'])->name('patient.edit');
Route::get('/patient/pdf', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');
Route::get('patient/pdf/{patientid}', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');
Route::get('facility/get/{province_id}', [App\Http\Controllers\HomeController::class, 'facilityGet'])->name('facility.get');
Route::get('muncity/get/{province_id}', [App\Http\Controllers\HomeController::class, 'muncityGet'])->name('muncity.get');
Route::get('barangay/get/{muncity_id}', [App\Http\Controllers\HomeController::class, 'barangayGet'])->name('barangay.get');

Route::get('/fundsource', [App\Http\Controllers\FundSourceController::class, 'fundSource'])->name('fundsource');
Route::get('/fundsource/create', [App\Http\Controllers\FundSourceController::class, 'createFundSource'])->name('fundsource.create');
Route::get('/fundsource/get/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'fundsourceGet'])->name('fundsource.get');
Route::post('/fundsource/create/save', [App\Http\Controllers\FundSourceController::class, 'createFundSourceSave'])->name('fundsource.create.save');
