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
|routesAdminController
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
Route::get('facility/get/{fundsource_id}', [App\Http\Controllers\DvController::class, 'facilityGet'])->name('facility.get');
//creating new route, method for modification in selecting facility first before the fundsource
Route::get('fetch/fundsource/{facility_id}', [App\Http\Controllers\DvController::class, 'getFundsource'])->name('fetch.fundsource');
Route::get('fetch_dv/update/{dv_id}', [App\Http\Controllers\DvController::class, 'updateDV'])->name('update.dv');
Route::post('/dv/update/save', [App\Http\Controllers\DvController::class, 'saveUpdateDV'])->name('dv.update.save');
Route::get('/dv/update/save', [App\Http\Controllers\DvController::class, 'saveUpdateDV'])->name('dv.update.save');


Route::get('/patient/create', [App\Http\Controllers\HomeController::class, 'createPatient'])->name('patient.create');
Route::post('/patient/create/save', [App\Http\Controllers\HomeController::class, 'createPatientSave'])->name('patient.create.save');
Route::get('/patient/edit/{patient_id}', [App\Http\Controllers\HomeController::class, 'editPatient'])->name('patient.edit');
Route::post('/patient/update', [App\Http\Controllers\HomeController::class, 'updatePatient'])->name('patient.update');
Route::get('/patient/pdf', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');
Route::get('patient/pdf/{patientid}', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');

Route::get('patient/sendpdf/{patientid}', [App\Http\Controllers\PrintController::class, 'sendpatientPdf'])->name('patient.sendpdf');


Route::get('dv/pdf/{dvId}', [App\Http\Controllers\PrintController::class, 'dvPDF'])->name('dv.pdf');


Route::get('facility/get/{province_id}', [App\Http\Controllers\HomeController::class, 'facilityGet'])->name('facility.get');
Route::get('muncity/get/{province_id}', [App\Http\Controllers\HomeController::class, 'muncityGet'])->name('muncity.get');
Route::get('barangay/get/{muncity_id}', [App\Http\Controllers\HomeController::class, 'barangayGet'])->name('barangay.get');
Route::get('transaction/get', [App\Http\Controllers\FundSourceController::class, 'transactionGet'])->name('transaction.get');
Route::get('/disbursement', [App\Http\Controllers\HomeController::class, 'disbursement'])->name('disbursement');

Route::post('dv/create/save',  [App\Http\Controllers\DvController::class, 'createDvSave'])->name('dv.create.save');
Route::get('facility/dv/{facility_id}', [App\Http\Controllers\DvController::class, 'dvfacility'])->name('facility.dv');

Route::get('/fundsource', [App\Http\Controllers\FundSourceController::class, 'fundSource'])->name('fundsource');
Route::get('fundsource/edit/{fundsourceId}/{proponent_id}', [App\Http\Controllers\FundSourceController::class, 'Editfundsource'])->name('fundsource.edit');
Route::get('/fundsource/saa/get', [App\Http\Controllers\FundSourceController::class, 'fundSourceGet'])->name('fundsource.saa.get');
Route::get('/fundsource/create', [App\Http\Controllers\FundSourceController::class, 'createFundSource'])->name('fundsource.create');
Route::post('/fundsource/create/save', [App\Http\Controllers\FundSourceController::class, 'createFundSourceSave'])->name('fundsource.create.save');
Route::get('/proponent/get/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'proponentGet'])->name('proponent.get');
Route::get('facility/get/{facilityId}',  [App\Http\Controllers\FundSourceController::class, 'facilityGet'])->name('facility.get');
Route::post('fundsource/update', [App\Http\Controllers\FundSourceController::class, 'updatefundsource'])->name('fundsource.update');
Route::get('fundsource/transfer_funds/{fundsourceId}/{proponent_id}/{facility_id}', [App\Http\Controllers\FundSourceController::class, 'transferFunds'])->name('fundsource.transfer');
Route::post('fundsource/transfer/save', [App\Http\Controllers\FundSourceController::class, 'saveTransferFunds'])->name('transfer.save');
Route::get('proponent/{proponent_id}', [App\Http\Controllers\FundSourceController::class, 'fetchProponent'])->name('proponent.fetch');

Route::get('/rem_balance/{facility_id}/{proponent_id}', [App\Http\Controllers\FundSourceController::class, 'getBalance'])->name('balance.get');
Route::get('/facility/proponent/{facility_id}', [App\Http\Controllers\FundSourceController::class, 'facilityProponentGet'])->name('facility.proponent.get');
Route::get('/patient/code/{proponent_id}/{facility_id}', [App\Http\Controllers\FundSourceController::class, 'forPatientCode'])->name('facility.patient.code');
Route::get('/patient/proponent/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'forPatientFacilityCode'])->name('facility.patient.code');


//DISBURSEMENT VOUCHER
Route::get('/dv1', [App\Http\Controllers\DvController::class, 'dv'])->name('dv');
Route::get('/dv/create', [App\Http\Controllers\DvController::class, 'createDv'])->name('dv.create');
Route::get('/getFund/{facilityId}{fund_source}',[App\Http\Controllers\DvController::class, 'getFund']);
Route::get('/getvatEwt/{facilityId}',[App\Http\Controllers\DvController::class, 'getvatEwt'])->name('getvatEwt');
Route::get('/getallocated/{facilityId}',[App\Http\Controllers\DvController::class, 'getAlocated'])->name('getallocated');
//modify im getting proponent_info since not all saa is same facility due to cvchd that can be used by another facility;
Route::get('/balance',[App\Http\Controllers\DvController::class, 'getAllInfo'])->name('proponent_info.all');



Route::get('/dv/create/save', [App\Http\Controllers\DvController::class, 'createDvSave'])->name('dv.create.save');
Route::get('getDv/{dvId}', [App\Http\Controllers\DvController::class, 'getDv'])->name('getDv');
Route::get('user/{userid}', [App\Http\Controllers\DvController::class, 'getUser'])->name('getUser');

Route::get('tracking/{fundsourceId}/{proponentInfoId}/{facilityId}', [App\Http\Controllers\UtilizationController::class, 'tracking'])->name('tracking');
Route::get('budget/tracking/{fundsourceId}/{type}', [App\Http\Controllers\UtilizationController::class, 'trackingBudget'])->name('budget.tracking');


//DISBURSEMENT VOUCHER v2
Route::get('/dv2', [App\Http\Controllers\Dv2Controller::class, 'dv2'])->name('dv2');
Route::get('/dv2/{route_no}', [App\Http\Controllers\Dv2Controller::class, 'createDv2'])->name('dv2.create');
Route::match(['get', 'post'], '/dv2/save', [App\Http\Controllers\Dv2Controller::class, 'saveDv2'])->name('dv2.save');

Route::get('/dv2/pdf', [App\Http\Controllers\PrintController::class, 'dv2Pdf'])->name('dv2.pdf');
Route::get('dv2/pdf/{route_no}', [App\Http\Controllers\PrintController::class, 'dv2Pdf'])->name('dv2.pdf');
Route::get('dv2/image/{route_no}', [App\Http\Controllers\PrintController::class, 'dv2Image'])->name('dv2.image');

Route::match(['get', 'post'],'/update/amount/{patientId}', [App\Http\Controllers\HomeController::class, 'updateAmount'])->name('update.amount');
Route::match(['get', 'post'],'/save/group/', [App\Http\Controllers\HomeController::class, 'saveGroup'])->name('save.group');
Route::get('/group/{facility_id}/{proponentId}', [App\Http\Controllers\Dv2Controller::class, 'getGroup'])->name('group.get'); 
Route::get('/proponentInfo/{facility_id}/{pro_group}', [App\Http\Controllers\Dv2Controller::class, 'getProponentInfo'])->name('proponent_info.get'); 
Route::get('/group', [App\Http\Controllers\HomeController::class, 'group'])->name('group');
Route::get('/group/patients/list/{group_id}', [App\Http\Controllers\HomeController::class, 'getPatientGroup'])->name('group.patients');
Route::match(['get', 'post'],'/proponent/report/{pro_group}', [App\Http\Controllers\HomeController::class, 'getProponentReport'])->name('proponent.report');
Route::match(['get', 'post'],'/facility/report/{facility_id}', [App\Http\Controllers\HomeController::class, 'getFacilityReport'])->name('facility.report');
Route::get('/report', [App\Http\Controllers\HomeController::class, 'report'])->name('report');
Route::get('/report/facility', [App\Http\Controllers\HomeController::class, 'reportFacility'])->name('report.facility');
Route::match(['get', 'post'],'patient/mails', [App\Http\Controllers\PrintController::class, 'sendMultiple'])->name('sent.mails');
Route::match(['get', 'post'],'patient/{patient_id}', [App\Http\Controllers\HomeController::class, 'getPatient'])->name('get.patient');
Route::match(['get', 'post'],'group/patient/{facility_id}/{proponent_id}', [App\Http\Controllers\HomeController::class, 'getPatients'])->name('get.patients');
Route::post('group/patient/list', [App\Http\Controllers\HomeController::class, 'updateGroupList'])->name('save.patients');

Route::get('/fundsource_budget', [App\Http\Controllers\FundSourceController2::class, 'fundSource2'])->name('fundsource_budget');
Route::post('/fundsource_budget/create', [App\Http\Controllers\FundSourceController2::class, 'createfundSource2'])->name('fundsource_budget.save');
Route::get('/dvlist', [App\Http\Controllers\FundSourceController2::class, 'pendingDv'])->name('fundsource_budget.pendingDv');
//creating breakdowns
Route::get('fundsource/breakdowns/{fundsourceId}', [App\Http\Controllers\FundSourceController::class, 'createBDowns'])->name('fundsource.create_breakdowns');
Route::match(['get','post'],'fundsource/breakdowns', [App\Http\Controllers\FundSourceController::class, 'saveBDowns'])->name('fundsource.save_breakdowns');
Route::get('facilities/get/{type}', [App\Http\Controllers\FundSourceController::class, 'facilitiesGet'])->name('facilities.get');
Route::get('pro_div/get', [App\Http\Controllers\FundSourceController::class, 'pro_divGet'])->name('pro_div.get');
Route::match(['get', 'post'],'dv/obligate', [App\Http\Controllers\DvController::class, 'obligate'])->name('dv.obligate');
Route::get('dv/{route_no}/{dv_no}/{type}', [App\Http\Controllers\FundSourceController2::class, 'dv_display'])->name('display.dv');
















