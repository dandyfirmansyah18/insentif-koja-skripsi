<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('app');
});

Route::get('/login', function () {
    return view('login');
});
Route::post('doLogin', array('uses' => 'LoginController@doLogin'));
Route::get('doLogout', array('uses' => 'LoginController@doLogout'));

Route::get('/exportexcel',array('uses' => 'InsentifController@exportexcel'));
Route::get('/exportexcel/{parameter}',array('uses' => 'InsentifController@exportexcel'));
Route::get('/exportSlip/{id}',array('uses' => 'InsentifController@exportSlip'));
Route::get('/download_template/{param}',array('uses' => 'InsentifController@download_template'));
Route::post('/upload_potongan',array('uses' => 'InsentifController@upload_potongan'));

Route::group(['middleware' => ['web']], function () {
    Route::resource('group', 'GroupController');
    Route::resource('employee', 'EmployeeController');
    Route::resource('getPosisi', 'EmployeeController@getPosisi');
    Route::resource('getGroup', 'EmployeeController@getGroup');
    Route::resource('getShift', 'EmployeeController@getShift');
    Route::resource('getBlock', 'EmployeeController@getBlock');
    Route::resource('getSlice', 'EmployeeController@getSlice');
    Route::resource('getDivisi', 'EmployeeController@getDivisi');
    Route::resource('getGrade', 'EmployeeController@getGrade');
    Route::resource('checkSlice', 'EmployeeController@checkSlice');
    Route::resource('getCountEmployee', 'EmployeeController@getCountEmployee');

    Route::resource('getQccGroupA', 'InsentifController@getQccGroupA');
    Route::resource('getQccGroupB', 'InsentifController@getQccGroupB');
    Route::resource('getQccGroupC', 'InsentifController@getQccGroupC');
    Route::resource('getQccGroupD', 'InsentifController@getQccGroupD');

    Route::resource('getRtgGroupA', 'InsentifController@getRtgGroupA');
    Route::resource('getRtgGroupB', 'InsentifController@getRtgGroupB');
    Route::resource('getRtgGroupC', 'InsentifController@getRtgGroupC');
    Route::resource('getRtgGroupD', 'InsentifController@getRtgGroupD');

    Route::resource('getqccrtgmin', 'InsentifController@getqccrtgmin');
    Route::resource('getPotonganKar', 'InsentifController@getPotonganKar');
    Route::resource('potonganKarSearch', 'InsentifController@potonganKarSearch');

    Route::resource('user', 'UserController');
    Route::resource('resetPassword', 'UserController@resetPassword');
    Route::resource('getPrivileges', 'UserController@getPrivileges');
    Route::resource('getUserStatus', 'UserController@getUserStatus');

    Route::resource('division', 'DivisionController');
    Route::resource('position', 'PositionController');

    Route::resource('cekSetting', 'InsentifController@cekSetting');    
    Route::resource('cekStep', 'InsentifController@cekStep');
    Route::resource('saveStep1', 'InsentifController@saveStep1');
    Route::resource('saveStep2', 'InsentifController@saveStep2');
    Route::resource('saveStep3', 'InsentifController@saveStep3');
    Route::resource('saveStep4', 'InsentifController@saveStep4');
    Route::resource('saveStep5', 'InsentifController@saveStep5');

    Route::resource('checkStep2', 'InsentifController@checkStep2');    

    Route::resource('showStep1', 'InsentifController@showStep1');
    Route::resource('showStep2', 'InsentifController@showStep2');
    Route::resource('showStep3', 'InsentifController@showStep3');

    Route::resource('previewInsentif', 'InsentifController@previewInsentif');
    Route::resource('previewInsentifsearch', 'InsentifController@previewInsentifsearch');

    Route::resource('saveBoxGroupA', 'InsentifController@saveBoxGroupA');
    Route::resource('saveBoxGroupB', 'InsentifController@saveBoxGroupB');
    Route::resource('saveBoxGroupC', 'InsentifController@saveBoxGroupC');
    Route::resource('saveBoxGroupD', 'InsentifController@saveBoxGroupD');
    Route::resource('saveBoxGroupRtgA', 'InsentifController@saveBoxGroupRtgA');
    Route::resource('saveBoxGroupRtgB', 'InsentifController@saveBoxGroupRtgB');
    Route::resource('saveBoxGroupRtgC', 'InsentifController@saveBoxGroupRtgC');
    Route::resource('saveBoxGroupRtgD', 'InsentifController@saveBoxGroupRtgD');

    Route::resource('savePotonganKar', 'InsentifController@savePotonganKar');

    Route::resource('saveBoxOjekQccA', 'InsentifController@saveBoxOjekQccA');
    Route::resource('saveBoxOjekQccB', 'InsentifController@saveBoxOjekQccB');
    Route::resource('saveBoxOjekQccC', 'InsentifController@saveBoxOjekQccC');
    Route::resource('saveBoxOjekQccD', 'InsentifController@saveBoxOjekQccD');

    Route::resource('saveBoxOjekRtgA', 'InsentifController@saveBoxOjekRtgA');
    Route::resource('saveBoxOjekRtgB', 'InsentifController@saveBoxOjekRtgB');
    Route::resource('saveBoxOjekRtgC', 'InsentifController@saveBoxOjekRtgC');
    Route::resource('saveBoxOjekRtgD', 'InsentifController@saveBoxOjekRtgD');

    Route::resource('getCountBlock1', 'InsentifController@getCountBlock1');
    Route::resource('getCountBlock2', 'InsentifController@getCountBlock2');
    Route::resource('getCountBlock3', 'InsentifController@getCountBlock3');

    Route::resource('adjustparam', 'AdjustParamController');
    Route::resource('getAdjustParam', 'AdjustParamController@getAdjustParam');
    Route::resource('saveEdit', 'AdjustParamController@saveEdit');

    Route::resource('daftarKaryawan', 'PostedInsentifController@daftarKaryawan'); 
    Route::resource('searchNameInsentif', 'PostedInsentifController@searchNameInsentif');   
    Route::resource('SearchBulanTahun', 'PostedInsentifController@SearchBulanTahun');    
    
    

});

Route::group(array('prefix'=>'/templates/'),function(){
    Route::get('{template}', array( function($template)
    {
        $template = str_replace(".html","",$template);
        View::addExtension('html','php');
        return View::make('templates.'.$template);
    }));
});
