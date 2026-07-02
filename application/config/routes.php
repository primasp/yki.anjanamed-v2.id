<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'AuthController';

$route['Login'] = 'AuthController';

$route['Admin'] = 'AdminController';

$route['Pasien-All'] = 'PasienController/pasienAll';
// $route['Pasien-Detil'] = 'PasienController/detailPasien';
$route['Pasien-Detil/(:num)'] = 'PasienController/detailPasien/$1';
$route['Pasien-Edit/(:num)'] = 'PasienController/editPasien/$1';

$route['Cetak-Pasien/(:any)'] = 'PdfController/cetakPasien/$1';




$route['Daftar-Layanan'] = 'RajalController/registLayan';
$route['Daftar-Layanan/Cari-Pasien'] = 'RajalController/cariPasien';
$route['Struk-Layan/(:any)/(:any)/(:any)'] = 'PdfController/rajalStruk/$1/$2/$3';

$route['Pasien-Create'] = 'PasienController/tambahPasienBaru';

$route['Pasien-Save'] = 'PasienController/savePasienBaru';
$route['Pasien-Edit'] = 'PasienController/editPasienBaru';

$route['Asessment'] = 'AsessmentController';
$route['Asessment-Selesai'] = 'AsessmentController/sudahAsesment';
$route['Asessment-Pending'] = 'AsessmentController/belumAsesment';
$route['Asessment-getDokter'] = 'AsessmentController/getDokterByPoli';
$route['Asessment-filterData'] = 'AsessmentController/filterData';

$route['Asessment/saveKajiAwalFull'] = 'AsessmentController/saveKajiAwalFull';
$route['Asessment/saveKajiAwal'] = 'AsessmentController/saveKajiAwal';

$route['Asessment/formMammoUsg/(:any)/(:any)'] = 'AsessmentController/formMammoUsg/$1/$2';

$route['Asessment/formKajiAwal/(:any)/(:any)'] = 'AsessmentController/formKajiAwalFull/$1/$2';

//  'redirect_url' => "AsessmentController/formPapsmear/$episode_id/$pasien_id"
$route['Asessment/formKajiAwalLengkap/(:any)/(:any)'] = 'AsessmentController/formKajiAwalFullLengkap/$1/$2';
$route['Asessment/formAsessLab/(:any)/(:any)'] = 'AsessmentController/formAssessmentLab/$1/$2';
$route['Asessment/formAsessRad/(:any)/(:any)'] = 'AsessmentController/formAssessmentRad/$1/$2';
$route['Asessment/formAsessLabRad/(:any)/(:any)'] = 'AsessmentController/formAssessmentLabRad/$1/$2';

$route['Asessment/paliatifForm']    = 'AsessmentController/paliatifForm';
$route['Asessment/savePaliatif']    = 'AsessmentController/savePaliatif';
$route['Asessment/getPaliatif']     = 'AsessmentController/getPaliatif';


$route['Asessment/saveMammoUsg']              = 'AsessmentController/saveMammoUsg';

// FORM EDIT POLI BIASA
$route['AsessmentController/editPengkajianAwal/(:any)/(:any)'] = 'AsessmentController/editPengkajianAwal/$1/$2';

// APS � PAPSMEAR
$route['AsessmentController/editPapsmear/(:any)/(:any)'] = 'AsessmentController/editPapsmear/$1/$2';

// APS � MAMMO / USG
$route['AsessmentController/editMammoUsg/(:any)/(:any)'] = 'AsessmentController/editMammoUsg/$1/$2';




$route['Dokter-Home'] = 'DokterController';
$route['Dokter-Dashboard'] = 'DokterController/dashboard';
$route['dokter/get_pasien_by_poli'] = 'DokterController/getPasienByPoli';


$route['Lab-Worklist'] = 'LabController';
$route['LabController/worklist-counts'] = 'LabController/worklist_counts';
$route['LabController/worklist-filterData'] = 'LabController/worklist_filterData';
$route['LabController/worklist-detail']     = 'LabController/worklist_detail';
$route['LabController/worklist-kerjakan']     = 'LabController/worklist_kerjakan';
$route['LabController/worklist-updateStatus'] = 'LabController/worklist_updateStatus';
$route['LabController/label-data'] = 'LabController/label_data';


$route['LabController/pap-form-data'] = 'LabController/pap_form_data';
$route['LabController/pap-form-save'] = 'LabController/pap_form_save';
// $route['LabController/pap-form-save'] = 'LabController/pap_form_save'; 
// LabDokterController/pap-form-save



$route['Lab-Doctor'] = 'LabDoctorController';
$route['LabDoctorController/pap-list'] = 'LabDoctorController/pap_list';
$route['LabDoctorController/pap-form-data'] = 'LabDoctorController/pap_form_data';
$route['LabDoctorController/pap-form-save'] = 'LabDoctorController/pap_form_save';


$route['LabController/pap-print'] = 'LabController/pap_print';
$route['LabDoctorController/pap-print'] = 'LabDoctorController/pap_print';
$route['PdfController/papResult'] = 'PdfController/papResult';


// $route['LabController/pap-print'] = 'LabController/pap_print';
// $route['LabDoctorController/pap-print'] = 'LabDoctorController/pap_print';

$route['Rad-Worklist'] = 'RadController';
$route['Rad-Worklist/worklist-filterData'] = 'RadController/worklist_filterData';
$route['Rad-Worklist/worklist-counts'] = 'RadController/worklist_counts';
$route['Rad-Worklist/worklist-detail'] = 'RadController/worklist_detail';
$route['Rad-Worklist/updateNote'] = 'RadController/updateNote';


$route['Rad-Worklist/worklist-updateStatus'] = 'RadController/worklist_updateStatus';
$route['Rad-Worklist/worklist-uploadResult'] = 'RadController/worklist_uploadResult';
$route['Rad-Worklist/worklist-resultList'] = 'RadController/worklist_resultList';
$route['Rad-Worklist/worklist-resultDelete'] = 'RadController/worklist_resultDelete';
$route['Rad-Worklist/worklist-sendToDoctor'] = 'RadController/worklist_sendToDoctor';


$route['Rad-Doctor'] = 'RadDoctorController';
$route['Rad-Doctor/worklist-filterData'] = 'RadDoctorController/worklist_filterData';



$route['Validasi-Obat'] = 'ValObatController';



$route['Master-Obat'] = 'MasterController/obatMaster';
$route['Stok-Obat'] = 'MasterController/obatStok';
$route['Master-Tindakan'] = 'MasterController/tindakanMaster';
$route['Manajemen-Obat'] = 'MasterController/ManajemenObat';


$route['satusehat/token'] = 'SatuSehatController/getTokenSatuSehat';
$route['satusehat/kirimEncounter'] = 'SatuSehatController/kirimEncounter';
$route['satusehat/encounterInProgress'] = 'SatuSehatController/EncounterInProgreess';
$route['satusehat/kirimCondition'] = 'SatuSehatController/kirimCondition';


$route['History-Pasien/(:any)/(:any)'] = 'HistoryController/index/$1/$2';

$route['Resume-Rj'] = 'ResumeController';
$route['Resume-getDokter'] = 'ResumeController/getDokterByPoli';





$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/*
| -------------------------------------------------------------------------
| Sample REST API Routes
| -------------------------------------------------------------------------
*/
$route['api/example/users/(:num)'] = 'api/example/users/id/$1'; // Example 4
$route['api/example/users/(:num)(\.)([a-zA-Z0-9_-]+)(.*)'] = 'api/example/users/id/$1/format/$3$4'; // Example 8
