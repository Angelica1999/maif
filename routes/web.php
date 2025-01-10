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
Route::get('/mpu-returned', [App\Http\Controllers\HomeController::class, 'returnedPatients'])->name('returned.patients');
// Route::get('/data/{type}', [App\Http\Controllers\HomeController::class, 'getData'])->name('get.data');
Route::get('/facilities', [App\Http\Controllers\FacilityController::class, 'index'])->name('facility');
Route::get('facility/edit/{main_id}', [App\Http\Controllers\FacilityController::class, 'facilityEdit'])->name('facility.edit');
Route::post('facility/update', [App\Http\Controllers\FacilityController::class, 'facilityUpdate'])->name('facility.update');
Route::get('facility/vatEwt', [App\Http\Controllers\FacilityController::class, 'getVatEwt']);
Route::get('facility/get/{fundsource_id}', [App\Http\Controllers\DvController::class, 'facilityGet'])->name('facility.get');
//creating new route, method for modification in selecting facility first before the fundsource
Route::get('fetch/fundsource/{facility_id}', [App\Http\Controllers\DvController::class, 'getFundsource'])->name('fetch.fundsource');
Route::get('fetch_dv/update/{dv_id}', [App\Http\Controllers\DvController::class, 'updateDV'])->name('update.dv');
Route::post('/dv/update/save', [App\Http\Controllers\DvController::class, 'saveUpdateDV'])->name('dv.update.save');
Route::get('/dv/update/save', [App\Http\Controllers\DvController::class, 'saveUpdateDV'])->name('dv.update.save');

Route::post('/patient/create/save', [App\Http\Controllers\HomeController::class, 'createPatientSave'])->name('patient.create.save');
Route::get('/patient/edit/{patient_id}', [App\Http\Controllers\HomeController::class, 'editPatient'])->name('patient.edit');
Route::post('/patient/update/{id}', [App\Http\Controllers\HomeController::class, 'updatePatient'])->name('patient.update');
Route::get('/patient/remove/{id}', [App\Http\Controllers\HomeController::class, 'removePatient'])->name('patient.remove');
Route::get('/patient/{id}', [App\Http\Controllers\HomeController::class, 'fetchPatient'])->name('patient.fetch');
Route::get('/patient', [App\Http\Controllers\HomeController::class, 'fetchAdditionalData'])->name('home.addditional');
Route::get('/group/remove_patient/{id}', [App\Http\Controllers\HomeController::class, 'groupRemovePatient'])->name('patient.group_remove');
Route::get('/patient/send-to-facility/{id}', [App\Http\Controllers\HomeController::class, 'facilitySend'])->name('facility.send');
Route::match(['get', 'post'],'/patient/return/{id}', [App\Http\Controllers\HomeController::class, 'returnPatient'])->name('patient.return');
Route::get('/patient/accept/{id}', [App\Http\Controllers\HomeController::class, 'acceptPat'])->name('patient.accept');

Route::get('/mail/history/{id}', [App\Http\Controllers\HomeController::class, 'mailHistory'])->name('mail.history');
Route::get('/patient/history/{id}', [App\Http\Controllers\HomeController::class, 'patientHistory'])->name('patient.history');


Route::get('/patient/pdf', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');
Route::get('patient/pdf/{patientid}', [App\Http\Controllers\PrintController::class, 'patientPdf'])->name('patient.pdf');

Route::get('patient/sendpdf/{patientid}', [App\Http\Controllers\PrintController::class, 'sendpatientPdf'])->name('patient.sendpdf');


Route::get('dv/pdf/{dvId}', [App\Http\Controllers\PrintController::class, 'dvPDF'])->name('dv.pdf');


// Route::get('facility/get/{province_id}', [App\Http\Controllers\HomeController::class, 'facilityGet'])->name('facility.get');
Route::get('muncity/get/{province_id}', [App\Http\Controllers\HomeController::class, 'muncityGet'])->name('muncity.get');
Route::get('barangay/get/{muncity_id}', [App\Http\Controllers\HomeController::class, 'barangayGet'])->name('barangay.get');
Route::get('transaction/get', [App\Http\Controllers\FundSourceController::class, 'transactionGet'])->name('transaction.get');
// Route::get('/disbursement', [App\Http\Controllers\HomeController::class, 'disbursement'])->name('disbursement');

Route::post('dv/create/save',  [App\Http\Controllers\DvController::class, 'createDvSave'])->name('dv.create.save');
Route::get('facility/dv/{facility_id}', [App\Http\Controllers\DvController::class, 'dvfacility'])->name('facility.dv');

Route::get('/fundsources/mpu', [App\Http\Controllers\FundSourceController::class, 'fundSource'])->name('fundsource');
Route::get('fundsource/edit/{fundsourceId}/{proponent_id}', [App\Http\Controllers\FundSourceController::class, 'Editfundsource'])->name('fundsource.edit');
Route::get('/fundsource/saa/get', [App\Http\Controllers\FundSourceController::class, 'fundSourceGet'])->name('fundsource.saa.get');
Route::get('/fundsource/create', [App\Http\Controllers\FundSourceController::class, 'createFundSource'])->name('fundsource.create');
Route::post('/fundsource/create/save', [App\Http\Controllers\FundSourceController::class, 'createFundSourceSave'])->name('fundsource.create.save');
Route::get('/proponent/get/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'proponentGet'])->name('proponent.get');
// Route::get('facility/get/{facilityId}',  [App\Http\Controllers\FundSourceController::class, 'facilityGet'])->name('facility.get');
Route::post('fundsource/update', [App\Http\Controllers\FundSourceController::class, 'updatefundsource'])->name('fundsource.update');
Route::get('fundsource/transfer_funds/{info_id}', [App\Http\Controllers\FundSourceController::class, 'transferFunds'])->name('fundsource.transfer');
Route::post('fundsource/transfer/save', [App\Http\Controllers\FundSourceController::class, 'saveTransferFunds'])->name('transfer.save');
Route::get('proponent/{proponent_id}', [App\Http\Controllers\FundSourceController::class, 'fetchProponent'])->name('proponent.fetch');

Route::get('/rem_balance/{facility_id}/{proponent_id}', [App\Http\Controllers\FundSourceController::class, 'getBalance'])->name('balance.get');
Route::get('/facility/proponent/{facility_id}', [App\Http\Controllers\FundSourceController::class, 'facilityProponentGet'])->name('facility.proponent.get');
Route::get('/patient/code/{proponent_id}/{facility_id}', [App\Http\Controllers\FundSourceController::class, 'forPatientCode'])->name('facility.patient.code');
Route::get('/patient/proponent/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'forPatientFacilityCode'])->name('facility.patient.code');

//DISBURSEMENT VOUCHER
Route::get('/dv1', [App\Http\Controllers\DvController::class, 'dv'])->name('dv');

// Route::get('/dv1/pending', [App\Http\Controllers\DvController::class, 'dvPending'])->name('dv.pending');
// Route::get('/dv1/obligated', [App\Http\Controllers\DvController::class, 'dvObligate'])->name('dv.obligated');
// Route::get('/dv1/w_dvno', [App\Http\Controllers\DvController::class, 'withDvNO'])->name('dv.withdv');
// Route::get('/dv1/without_dvno', [App\Http\Controllers\DvController::class, 'withoutDvNO'])->name('dv.withoutdv');

Route::get('/dv/create', [App\Http\Controllers\DvController::class, 'createDv'])->name('dv.create');
Route::get('/getFund/{facilityId}{fund_source}',[App\Http\Controllers\DvController::class, 'getFund']);
Route::get('/getvatEwt/{facilityId}',[App\Http\Controllers\DvController::class, 'getvatEwt'])->name('getvatEwt');
Route::get('/getallocated/{facilityId}',[App\Http\Controllers\DvController::class, 'getAlocated'])->name('getallocated');
//modify im getting proponent_info since not all saa is same facility due to cvchd that can be used by another facility;
Route::get('/balance',[App\Http\Controllers\DvController::class, 'getAllInfo'])->name('proponent_info.all');
Route::get('/dv/history/{route_no}',[App\Http\Controllers\DvController::class, 'dvHistory'])->name('dv.history');


Route::get('/dv/create/save', [App\Http\Controllers\DvController::class, 'createDvSave'])->name('dv.create.save');
Route::get('getDv/{dvId}', [App\Http\Controllers\DvController::class, 'getDv'])->name('getDv');
Route::get('user/{userid}', [App\Http\Controllers\DvController::class, 'getUser'])->name('getUser');

// Route::get('tracking/{fundsourceId}/{proponentInfoId}/{facilityId}', [App\Http\Controllers\UtilizationController::class, 'tracking'])->name('tracking');
Route::get('tracking/{info_id}', [App\Http\Controllers\UtilizationController::class, 'tracking'])->name('tracking');
Route::get('budget/tracking/{fundsourceId}/{type}', [App\Http\Controllers\UtilizationController::class, 'trackingBudget'])->name('budget.tracking');


//DISBURSEMENT VOUCHER v2
Route::get('/dv2', [App\Http\Controllers\Dv2Controller::class, 'dv2'])->name('dv2');
Route::get('/dv2/{route_no}', [App\Http\Controllers\Dv2Controller::class, 'createDv2'])->name('dv2.create');
Route::match(['get', 'post'], '/dv2/save', [App\Http\Controllers\Dv2Controller::class, 'saveDv2'])->name('dv2.save');
Route::match(['get', 'post'], '/dv2/update', [App\Http\Controllers\Dv2Controller::class, 'updateDv2'])->name('dv2.update');

Route::get('/dv2/pdf', [App\Http\Controllers\PrintController::class, 'dv2Pdf'])->name('dv2.pdf');
Route::get('dv2/pdf/{route_no}', [App\Http\Controllers\PrintController::class, 'dv2Pdf'])->name('dv2.pdf');
Route::get('dv2/image/{route_no}', [App\Http\Controllers\PrintController::class, 'dv2Image'])->name('dv2.image');

Route::match(['get', 'post'],'/update/amount/{patientId}/{amount}', [App\Http\Controllers\HomeController::class, 'updateAmount'])->name('update.amount');
Route::match(['get', 'post'],'/save/group/', [App\Http\Controllers\HomeController::class, 'saveGroup'])->name('save.group');
Route::get('/group/{facility_id}/{proponentId}', [App\Http\Controllers\Dv2Controller::class, 'getGroup'])->name('group.get'); 
Route::get('/proponentInfo/{facility_id}/{pro_group}', [App\Http\Controllers\Dv2Controller::class, 'getProponentInfo'])->name('proponent_info.get'); 
Route::get('/group', [App\Http\Controllers\HomeController::class, 'group'])->name('group');
Route::get('/group/patients/list/{group_id}', [App\Http\Controllers\HomeController::class, 'getPatientGroup'])->name('group.patients');
Route::match(['get', 'post'],'/proponent/report/{pro_group}', [App\Http\Controllers\HomeController::class, 'getProponentReport'])->name('proponent.report');
Route::match(['get', 'post'],'/facility/report/{facility_id}', [App\Http\Controllers\HomeController::class, 'getFacilityReport'])->name('facility.report');
Route::get('/report/proponent', [App\Http\Controllers\HomeController::class, 'report'])->name('report');
Route::get('/report/facility', [App\Http\Controllers\HomeController::class, 'reportFacility'])->name('report.facility');
Route::match(['get', 'post'],'patient/mails', [App\Http\Controllers\PrintController::class, 'sendMultiple'])->name('sent.mails');
Route::match(['get', 'post'],'patient/{patient_id}', [App\Http\Controllers\HomeController::class, 'getPatient'])->name('get.patient');
Route::match(['get', 'post'],'group/patient/{facility_id}/{proponent_id}', [App\Http\Controllers\HomeController::class, 'getPatients'])->name('get.patients');
Route::post('group/patient/list', [App\Http\Controllers\HomeController::class, 'updateGroupList'])->name('save.patients');

Route::get('/fundsource_budget', [App\Http\Controllers\FundSourceController2::class, 'fundSource2'])->name('fundsource_budget');
Route::post('/fundsource_budget/create', [App\Http\Controllers\FundSourceController2::class, 'createfundSource2'])->name('fundsource_budget.save');
Route::get('/dvlist/{type}', [App\Http\Controllers\FundSourceController2::class, 'pendingDv'])->name('fundsource_budget.pendingDv');
Route::get('/budget/fundsource/{id}', [App\Http\Controllers\FundSourceController2::class, 'budgetTracking'])->name('budget.tracking');
Route::get('/budget/funds/{id}', [App\Http\Controllers\FundSourceController2::class, 'fundsTracking'])->name('funds.tracking');
Route::match(['get', 'post'],'/budget/confirm/{route_no}', [App\Http\Controllers\FundSourceController2::class, 'confirmDV'])->name('dv.confirmation');
Route::get('/confirm/{id}', [App\Http\Controllers\FundSourceController2::class, 'confirm'])->name('dv.confirm');
Route::get('/admin/cost/{id}', [App\Http\Controllers\FundSourceController2::class, 'addCost'])->name('add.cost');
Route::post('/cost/save', [App\Http\Controllers\FundSourceController2::class, 'saveCost'])->name('save.cost');
Route::get('/confirm-budget/{id}', [App\Http\Controllers\FundSourceController2::class, 'confirmBudget'])->name('confirm.budget');
Route::post('util/ors_no', [App\Http\Controllers\FundSourceController2::class, 'orsNo'])->name('add.ors');
Route::post('util/uacs', [App\Http\Controllers\FundSourceController2::class, 'uacs'])->name('add.uacs');

//creating breakdowns
Route::get('fundsource/breakdowns/{fundsourceId}', [App\Http\Controllers\FundSourceController::class, 'createBDowns'])->name('fundsource.create_breakdowns');
Route::match(['get','post'],'fundsource/breakdowns', [App\Http\Controllers\FundSourceController::class, 'saveBDowns'])->name('fundsource.save_breakdowns');
Route::get('facilities/get/{type}', [App\Http\Controllers\FundSourceController::class, 'facilitiesGet'])->name('facilities.get');
Route::get('pro_div/get', [App\Http\Controllers\FundSourceController::class, 'pro_divGet'])->name('pro_div.get');
Route::match(['post', 'get'],'fundsource/{type}/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'getFundsource'])->name('update.fundsource');

Route::match(['get', 'post'],'dv/obligate', [App\Http\Controllers\DvController::class, 'obligate'])->name('dv.obligate');
Route::get('dv/{route_no}/{type}', [App\Http\Controllers\FundSourceController2::class, 'dv_display'])->name('display.dv');

Route::get('/cashier/{type}', [App\Http\Controllers\FundSourceController2::class, 'cashierPending'])->name('cashier');
Route::get('/cashier/paid', [App\Http\Controllers\FundSourceController2::class, 'cashierPaid'])->name('cashier.paid');
Route::match(['get', 'post'],'dv/pay', [App\Http\Controllers\DvController::class, 'payDv'])->name('dv.pay');
Route::match(['get', 'post'],'dv/add_dvno', [App\Http\Controllers\DvController::class, 'addDvNo'])->name('dv.add_dvno');

Route::get('getsections/{id}', [App\Http\Controllers\DvController::class, 'getSections'])->name('division.section');
Route::post('/document/release', [App\Http\Controllers\DvController::class, 'addRelease'])->name('document.release');
Route::get('/sample', [App\Http\Controllers\FundSourceController2::class, 'sample'])->name('sample');

Route::match(['get', 'post'],'proponentInfo/{infoId}', [App\Http\Controllers\FundSourceController::class, 'removeInfo'])->name('remove.proInfo');
Route::get('/admin_cost', [App\Http\Controllers\FundSourceController::class, 'adminCost'])->name('admin_cost');
Route::get('/admin_cost/balance/{fundsource_id}', [App\Http\Controllers\FundSourceController::class, 'costBalance'])->name('admin_cost.balance');
Route::match(['get', 'post'],'admin_cost/usage', [App\Http\Controllers\FundSourceController::class, 'addUsage'])->name('admin_cost.usage');

Route::match(['get', 'post'],'/file', [App\Http\Controllers\FundSourceController::class, 'fileUpload'])->name('file');
Route::post('/fundsource/files', [App\Http\Controllers\FundSourceController::class, 'uploadFiles'])->name('upload.files');
Route::match(['get', 'post'],'/fundsources/remove/{id}', [App\Http\Controllers\FundSourceController::class, 'removeImage'])->name('image.remove');
Route::match(['get', 'post'],'/dv2/remove/{route_no}', [App\Http\Controllers\Dv2Controller::class, 'removeDv2'])->name('dv2.remove');
Route::match(['get', 'post'],'/dv1/remove/{route_no}', [App\Http\Controllers\DvController::class, 'removeDv'])->name('remove.dv');
Route::get('fundsources/proponents/lists/{fundsource_id}',[App\Http\Controllers\FundSourceController::class, 'fetchFundsource'])->name('fundsource.get');
Route::get('fundsources/transfer/proponentInfo/{fundsource_id}/{proponent_id}',[App\Http\Controllers\FundSourceController::class, 'fetchInfo'])->name('info.get');
Route::get('fundsources/transfer/facility/{facility_id}',[App\Http\Controllers\FundSourceController::class, 'fetchFacility'])->name('facility.get');

Route::get('/proponents', [App\Http\Controllers\ProponentController::class, 'proponentList'])->name('proponents');
Route::get('/on-hold', [App\Http\Controllers\ProponentController::class, 'onHold'])->name('on-hold');
Route::get('/release/{code}', [App\Http\Controllers\ProponentController::class, 'release'])->name('release');
Route::post('/hold/proponents', [App\Http\Controllers\ProponentController::class, 'holdPro'])->name('hold.proponent');
Route::match(['get', 'post'],'proponent/update', [App\Http\Controllers\ProponentController::class, 'updateProponent'])->name('proponent.update');
Route::get('/proponent-fundsource', [App\Http\Controllers\ProponentController::class, 'fundsource'])->name('proponents.fundsource');
Route::get('/proponent/util/{code}', [App\Http\Controllers\ProponentController::class, 'tracking'])->name('pro.tracking');
Route::get('/proponent/supplemental/{proponent}/{amount}', [App\Http\Controllers\ProponentController::class, 'supplemental'])->name('proponent.supplemental');
Route::get('/proponent/subtracted/{proponent}/{amount}', [App\Http\Controllers\ProponentController::class, 'subtracted'])->name('proponent.subtracted');
Route::get('/proponent/sup-details/{proponent}', [App\Http\Controllers\ProponentController::class, 'supDetails'])->name('details.supplemental');
Route::get('/proponent/sup-update/{id}/{amount}', [App\Http\Controllers\ProponentController::class, 'supUpdate'])->name('update.supplemental');
Route::get('/proponent/patient-delete/{id}', [App\Http\Controllers\ProponentController::class, 'delGL'])->name('delete.gl');
Route::get('/proponent/patient-sort/{code}/{id}/{userid}/{on}', [App\Http\Controllers\ProponentController::class, 'filterData'])->name('filter.gl');
Route::get('/proponent/patient-print/{code}/{ids}', [App\Http\Controllers\ProponentController::class, 'excelData'])->name('excel.gl');
Route::get('/proponent/sub-details/{proponent}', [App\Http\Controllers\ProponentController::class, 'subDetails'])->name('details.subtracted');
Route::get('/proponent/sub-update/{id}/{amount}', [App\Http\Controllers\ProponentController::class, 'subUpdate'])->name('update.subtraction');
Route::post('/proponent/supplemental-updated', [App\Http\Controllers\ProponentController::class, 'supplementalv2'])->name('proponent.supplementalv2');
Route::post('/proponent/manage-funds', [App\Http\Controllers\ProponentController::class, 'manageFunds'])->name('manage.funds');

Route::match(['get', 'post'],'/proponents/excel/', [App\Http\Controllers\FundSourceController::class, 'generateExcel'])->name('proponent.excel');
Route::match(['get', 'post'],'/data/update/', [App\Http\Controllers\FacilityController::class, 'updateData'])->name('update.data');
Route::match(['get', 'post'],'/test/sample/', [App\Http\Controllers\UtilizationController::class, 'test'])->name('test.test');
Route::get('report/saa', [App\Http\Controllers\ReportController::class, 'reportSaa'])->name('report.saa');

Route::get('/dv3', [App\Http\Controllers\Dv3Controller::class, 'dv3'])->name('dv3');
Route::match(['get', 'post'],'/dv3/save', [App\Http\Controllers\Dv3Controller::class, 'dv3Save'])->name('dv3.save.create');
Route::get('/dv3/create', [App\Http\Controllers\Dv3Controller::class, 'createDv3'])->name('dv3.create');
Route::get('clone/saa/{id}', [App\Http\Controllers\Dv3Controller::class, 'cloneSaa'])->name('saa.get');
Route::get('dv3/pdf/{route_no}', [App\Http\Controllers\PrintController::class, 'dv3Pdf'])->name('dv3.pdf');
Route::get('dv3/remove/{route_no}', [App\Http\Controllers\Dv3Controller::class, 'dv3Remove'])->name('dv3.remove');
Route::match(['get', 'post'],'/dv3/update/{route_no}', [App\Http\Controllers\Dv3Controller::class, 'dv3Update'])->name('dv3.update');
Route::match(['get', 'post'],'/dv3/save/{route_no}', [App\Http\Controllers\Dv3Controller::class, 'saveUpdate'])->name('dv3.update.save');
Route::get('disbursement3/lists/{type}', [App\Http\Controllers\Dv3Controller::class, 'pendingDv3'])->name('budget.dv3');
Route::match(['get','post'],'disbursement3/process/{type}', [App\Http\Controllers\Dv3Controller::class, 'processDv3'])->name('process.dv3');
Route::post('/dv3/remarks', [App\Http\Controllers\Dv3Controller::class, 'updateRemarks'])->name('update.remarks');

//modified utilization dv tracking
Route::get('checkdv/{route_no}', [App\Http\Controllers\UtilizationController::class, 'getDv'])->name('assigned.dv');
Route::get('/pusher', [App\Http\Controllers\PusherController::class, 'push'])->name('push.notify');
Route::get('/pusher/save', [App\Http\Controllers\PusherController::class, 'save'])->name('notify.save');

Route::match(['get', 'post'],'/tasks', [App\Http\Controllers\PusherController::class, 'tasks'])->name('tasks');
Route::get('/notepad', [App\Http\Controllers\PusherController::class, 'note'])->name('add.note');
Route::post('/notepad/save', [App\Http\Controllers\PusherController::class, 'save'])->name('save.note');
Route::get('/notepad/remove/{id}', [App\Http\Controllers\PusherController::class, 'delete'])->name('delete.note');
Route::get('/notepad/{id}', [App\Http\Controllers\PusherController::class, 'process'])->name('process.note');
Route::post('/notepad/update', [App\Http\Controllers\PusherController::class, 'update'])->name('update.note');
Route::get('/gl/update/{id}', [App\Http\Controllers\HomeController::class, 'updateGl'])->name('update.gl');
Route::get('list/fundsources/{facility_id}', [App\Http\Controllers\DV3Controller::class, 'getFundsourceList'])->name('fundsource.list');

Route::get('/pre-dv/pre_list', [App\Http\Controllers\PreDvController::class, 'pre_dv'])->name('pre_dv');
Route::get('/pre-dv/proponent-clone/{facility_id}', [App\Http\Controllers\PreDvController::class, 'cloneProponent'])->name('clone.proponent');
Route::get('/pre-dv/saa-clone/{facility_id}', [App\Http\Controllers\PreDvController::class, 'cloneSAA'])->name('clone.saa');
Route::get('/pre-dv/control-clone', [App\Http\Controllers\PreDvController::class, 'cloneControl'])->name('clone.control');
Route::match(['post', 'get'],'/pre-dv/save', [App\Http\Controllers\PreDvController::class, 'savePreDV'])->name('pre_dv.save');
Route::get('pre-dv/update/{id}', [App\Http\Controllers\PreDvController::class, 'displayPreDV'])->name('pre.display');
Route::get('pre-dv/delete/{id}', [App\Http\Controllers\PreDvController::class, 'deletePreDV'])->name('pre.delete');
Route::get('pre-dv/v1/{id}', [App\Http\Controllers\PreDvController::class, 'v1View'])->name('v1.view');
Route::get('pre-dv/v2/{id}', [App\Http\Controllers\PreDvController::class, 'v2View'])->name('v2.view');
Route::match(['post', 'get'],'pre-dv/save/update', [App\Http\Controllers\PreDvController::class, 'updatePreDV'])->name('pre_update.save');
Route::get('/pre-dv/version-1', [App\Http\Controllers\PreDvController::class, 'pre_dv1'])->name('pre_dv1');
Route::get('/pre-dv/version-2', [App\Http\Controllers\PreDvController::class, 'pre_dv2'])->name('pre_dv2');
Route::post('/pre-dv/dv/new', [App\Http\Controllers\PreDvController::class, 'newDV'])->name('dv_new.save');
Route::get('/pre-dv/version-2/pdf/{id}', [App\Http\Controllers\PrintController::class, 'newDVPDF'])->name('new_dv.pdf');
Route::get('/pre-dv/v2/delete/{route_no}', [App\Http\Controllers\PreDvController::class, 'v2Delete'])->name('v2.delete');
Route::get('/pre-dv/budget/pre_lists/{type}', [App\Http\Controllers\PreDvController::class, 'pre_dvBudget'])->name('pre_dv_budget');
Route::get('/pre-dv/budget/v2/{type}/{id}', [App\Http\Controllers\PreDvController::class, 'budgetV2'])->name('budget.v2');
Route::match(['post', 'get'],'/pre-dv/dv/process', [App\Http\Controllers\PreDvController::class, 'processNew'])->name('pre_dv.process');
Route::get('/pre-dv/control_nos/{facility_id}', [App\Http\Controllers\PreDvController::class, 'controlList'])->name('control.list');
Route::get('/pre-dv/check', [App\Http\Controllers\PreDvController::class, 'check'])->name('check');
Route::post('/pre-dv2/remarks', [App\Http\Controllers\PreDvController::class, 'dv2Remarks'])->name('dv2.remarks');

Route::get('/users', [App\Http\Controllers\UserController::class, 'users'])->name('users');
Route::get('/users/verified/{id}', [App\Http\Controllers\UserController::class, 'verifyuser'])->name('verify.user');
Route::get('/users/reset/{id}', [App\Http\Controllers\UserController::class, 'reset'])->name('reset.user');
Route::get('/users/save', [App\Http\Controllers\UserController::class, 'save'])->name('save');
Route::match(['get', 'post'],'/users/cancel/{id}', [App\Http\Controllers\UserController::class, 'cancel'])->name('cancel.user');

Route::get('/bills', [App\Http\Controllers\FacilityController::class, 'bills'])->name('bills');
Route::get('/bills/tracking/{id}', [App\Http\Controllers\FacilityController::class, 'tracking'])->name('tracking');
Route::get('/bills/view/{id}', [App\Http\Controllers\FacilityController::class, 'viewBills'])->name('view.bills');
Route::match(['get', 'post'], '/bills/process/{type}/{id}', [App\Http\Controllers\FacilityController::class, 'processBills'])->name('process.bills');

Route::get('/pre-dv/pdf/{id}', [App\Http\Controllers\PrintController::class, 'prePDF'])->name('pre.pdf');
Route::get('/pre-dv/image/{id}', [App\Http\Controllers\PrintController::class, 'preImage'])->name('pre.image');
Route::get('/version2/{id}', [App\Http\Controllers\FundSourceController::class, 'version2'])->name('version2');
Route::get('/patients', [App\Http\Controllers\HomeController::class, 'patients'])->name('patients');

Route::post('/mpu/activate', [App\Http\Controllers\UserController::class, 'mpu'])->name('mpu');

Route::get('/incoming', [App\Http\Controllers\FacilityController::class, 'incoming'])->name('incoming');
Route::get('/transmittal/{id}', [App\Http\Controllers\FacilityController::class, 'getTrans'])->name('transmittal.get');
Route::get('/logbook', [App\Http\Controllers\FacilityController::class, 'logbook'])->name('logbook');
Route::post('logbook/save', [App\Http\Controllers\FacilityController::class, 'logbookSave'])->name('logbook.save');
Route::get('/transmittal/references/{type}/{id}', [App\Http\Controllers\FacilityController::class, 'references'])->name('references');
Route::post('/transmittal/return', [App\Http\Controllers\FacilityController::class, 'returnTrans'])->name('transmittal.return');
Route::get('/returned', [App\Http\Controllers\FacilityController::class, 'returned'])->name('returned');
Route::get('/returned/details/{id}', [App\Http\Controllers\FacilityController::class, 'returnedDetails'])->name('returned.details');
Route::get('/transmittal/accept/{id}', [App\Http\Controllers\FacilityController::class, 'acceptTrans'])->name('transmittal.accept');
Route::get('/accepted', [App\Http\Controllers\FacilityController::class, 'accepted'])->name('accepted');
Route::get('/transmittal/details/{id}/{facility_id}', [App\Http\Controllers\FacilityController::class, 'transDetails'])->name('transmittal.details');
// Route::get('/sample/image/{id}', [App\Http\Controllers\PrintController::class, 'genPreImage'])->name('pre.image');
Route::post('/accepted/remarks', [App\Http\Controllers\FacilityController::class, 'transRem'])->name('accepted.remarks');
Route::get('/angelica/{route_no}', [App\Http\Controllers\PreDvController::class, 'angelica'])->name('angelica');
Route::get('/transmittal/received/{control_no}/{name}', [App\Http\Controllers\FacilityController::class, 'received'])->name('received.transmittal');
//sample
Route::get('/proposal', [App\Http\Controllers\SampleController::class, 'proposal'])->name('proposal');
Route::get('/rotate', [App\Http\Controllers\PrintController::class, 'roImage'])->name('roImage');

Route::get('/pre-dvv/version-2/pdf/{id}', [App\Http\Controllers\PrintController::class, 'sampleMe'])->name('new_dv.sample');
Route::get('sampsamp/{id}', [App\Http\Controllers\FundSourceController::class, 'getAmount'])->name('amount.get');










