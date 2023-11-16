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
Route::get('/facility', [App\Http\Controllers\FacilityController::class, 'index'])->name('facility');
Route::get('facility/edit/{main_id}', [App\Http\Controllers\FacilityController::class, 'facilityEdit'])->name('facility.edit');
Route::post('facility/update', [App\Http\Controllers\FacilityController::class, 'facilityUpdate'])->name('facility.update');
Route::get('facility/vatEwt', [App\Http\Controllers\FacilityController::class, 'getVatEwt']);

Route::get('/patient/create', [App\Http\Controllers\HomeController::class, 'createPatient'])->name('patient.create');
Route::post('/patient/create/save', [App\Http\Controllers\HomeController::class, 'createPatientSave'])->name('patient.create.save');
Route::get('/patient/edit/{patient_id}', [App\Http\Controllers\HomeController::class, 'editPatient'])->name('patient.edit');
Route::post('/patient/update', [App\Http\Controllers\HomeController::class, 'updatePatient'])->name('patient.update');
Route::get('/patient/pdf', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');
Route::get('patient/pdf/{patientid}', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');
Route::get('facility/get/{province_id}', [App\Http\Controllers\HomeController::class, 'facilityGet'])->name('facility.get');
Route::get('muncity/get/{province_id}', [App\Http\Controllers\HomeController::class, 'muncityGet'])->name('muncity.get');
Route::get('barangay/get/{muncity_id}', [App\Http\Controllers\HomeController::class, 'barangayGet'])->name('barangay.get');
Route::get('transaction/get', [App\Http\Controllers\FundSourceController::class, 'transactionGet'])->name('transaction.get');
Route::get('/disbursement', [App\Http\Controllers\HomeController::class, 'disbursement'])->name('disbursement');

Route::get('/fundsource', [App\Http\Controllers\FundSourceController::class, 'fundSource'])->name('fundsource');
Route::get('fundsource/edit/{fundsourceId}', [App\Http\Controllers\FundSourceController::class, 'Editfundsource'])->name('fundsource.edit');
Route::get('/fundsource/saa/get', [App\Http\Controllers\FundSourceController::class, 'fundSourceGet'])->name('fundsource.saa.get');
Route::get('/fundsource/create', [App\Http\Controllers\FundSourceController::class, 'createFundSource'])->name('fundsource.create');
Route::post('/fundsource/create/save', [App\Http\Controllers\FundSourceController::class, 'createFundSourceSave'])->name('fundsource.create.save');
Route::get('/proponent/get/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'proponentGet'])->name('proponent.get');
Route::get('facility/get/{facilityId}',  [App\Http\Controllers\FundSourceController::class, 'facilityGet'])->name('facility.get');
Route::post('fundsource/update', [App\Http\Controllers\FundSourceController::class, 'updatefundsource'])->name('fundsource.update');

Route::get('/facility/proponent/{proponent_id}', [App\Http\Controllers\FundSourceController::class, 'facilityProponentGet'])->name('facility.proponent.get');
Route::get('/patient/code/{proponent_id}/{facility_id}', [App\Http\Controllers\FundSourceController::class, 'forPatientCode'])->name('facility.patient.code');
// Route::get('/patient/proponent/{proponent_id}/{facility_id}', [App\Http\Controllers\FundSourceController::class, 'forPatientFacilityCode'])->name('facility.patient.code');
Route::get('/patient/proponent/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'forPatientFacilityCode'])->name('facility.patient.code');


//DISBURSEMENT VOUCHER
Route::get('/dv', [App\Http\Controllers\DvController::class, 'dv'])->name('dv');
Route::get('/dv/create', [App\Http\Controllers\DvController::class, 'createDv'])->name('dv.create');


Route::get('/dv/create/save', [App\Http\Controllers\DvController::class, 'createDvSave'])->name('dv.create.save');
Route::get('/dv/edit', [App\Http\Controllers\DvController::class, 'editDv'])->name('dv.edit');

