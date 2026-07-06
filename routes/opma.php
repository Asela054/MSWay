<?php

/*
|--------------------------------------------------------------------------
| Opma Web Routes
|--------------------------------------------------------------------------
*/
   //Opma Production Section Routes

Route::get('/opma_productiontaskdashboard' ,'Production_Module_Opma\ProductionTaskdashboardController@index')->name('opma_productiontaskdashboard');

// machine Controller Routes
Route::resource('OpmaMachine', 'Production_Module_Opma\MachineController');
Route::get('opma_machines', 'Production_Module_Opma\MachineController@index')->name('opma_machines');
Route::post('opma_addMachine',['uses' => 'Production_Module_Opma\MachineController@store', 'as' => 'opma_addMachine']); 
Route::post('OpmaMachine/update', 'Production_Module_Opma\MachineController@update')->name('OpmaMachine.update');
Route::get('OpmaMachine/destroy/{id}', 'Production_Module_Opma\MachineController@destroy');
Route::get('OpmaMachine/{id}/employees', 'Production_Module_Opma\MachineController@getEmployees')->name('OpmaMachine.getEmployees');
Route::post('OpmaMachine/storeEmployees', 'Production_Module_Opma\MachineController@storeEmployees')->name('OpmaMachine.storeEmployees');
Route::get('OpmaMachine/destroyEmployee/{id}', 'Production_Module_Opma\MachineController@destroyEmployee')->name('OpmaMachine.destroyEmployee');

// size Controller Routes
Route::resource('OpmaSize', 'Production_Module_Opma\SizeController');
Route::get('opma_sizes', 'Production_Module_Opma\SizeController@index')->name('opma_sizes');
Route::post('opma_addSize',['uses' => 'Production_Module_Opma\SizeController@store', 'as' => 'opma_addSize']); 
Route::post('OpmaSize/update', 'Production_Module_Opma\SizeController@update')->name('OpmaSize.update');
Route::get('OpmaSize/destroy/{id}', 'Production_Module_Opma\SizeController@destroy');

// style Controller Routes
Route::resource('OpmaStyle', 'Production_Module_Opma\ProductController');
Route::get('opma_styles', 'Production_Module_Opma\ProductController@index')->name('opma_styles');
Route::post('opma_addStyle',['uses' => 'Production_Module_Opma\ProductController@store', 'as' => 'opma_addStyle']); 
Route::post('OpmaStyle/update', 'Production_Module_Opma\ProductController@update')->name('OpmaStyle.update');
Route::get('OpmaStyle/destroy/{id}', 'Production_Module_Opma\ProductController@destroy');

  // Production Allocation Controller Routes
Route::get('opma_productionallocation', 'Production_Module_Opma\ProductionEmployeeAllocationController@index')->name('opma_productionallocation');
Route::post('opma_getMachineEmployees', 'Production_Module_Opma\ProductionEmployeeAllocationController@getMachineEmployees')->name('opma_getMachineEmployees');
Route::post('opma_getStyleSizes', 'Production_Module_Opma\ProductionEmployeeAllocationController@getStyleSizes')->name('opma_getStyleSizes');
Route::post('/opma_productallocationinsert' ,'Production_Module_Opma\ProductionEmployeeAllocationController@insert')->name('opma_productallocationinsert');
Route::post('/opma_productallocationedit' ,'Production_Module_Opma\ProductionEmployeeAllocationController@edit')->name('opma_productallocationedit');
Route::post('/opma_productallocationview' ,'Production_Module_Opma\ProductionEmployeeAllocationController@view')->name('opma_productallocationview');
Route::post('/opma_productallocationupdate' ,'Production_Module_Opma\ProductionEmployeeAllocationController@update')->name('opma_productallocationupdate');
Route::post('/opma_productallocationdelete' ,'Production_Module_Opma\ProductionEmployeeAllocationController@delete')->name('opma_productallocationdelete');
Route::post('/opma_productallocationdeletelist' ,'Production_Module_Opma\ProductionEmployeeAllocationController@deletelist')->name('opma_productallocationdeletelist');

Route::post('/opma_productdpt_allocation_list' ,'Production_Module_Opma\ProductionEmployeeAllocationController@dpt_allocation_list')->name('opma_productdpt_allocation_list');
Route::post('/opma_productdpt_allocation_insert' ,'Production_Module_Opma\ProductionEmployeeAllocationController@dpt_allocation_insert')->name('opma_productdpt_allocation_insert');

  //Production an Task Approve controller
Route::get('/opma_productiontaskapprove' ,'Production_Module_Opma\ProductionTaskApproveController@index')->name('opma_productiontaskapprove');
Route::post('/opma_productiontaskapprovegenerate' ,'Production_Module_Opma\ProductionTaskApproveController@generateproductiontask')->name('opma_productiontaskapprovegenerate');
Route::post('/opma_approveproductiontask' ,'Production_Module_Opma\ProductionTaskApproveController@approveproductiontask')->name('opma_approveproductiontask');

 // Production ending Controller Routes
Route::get('opma_productionending', 'Production_Module_Opma\ProductionEndingController@index')->name('opma_productionending');
Route::post('/opma_productionendingcancel' ,'Production_Module_Opma\ProductionEndingController@cancelproduction')->name('opma_productionendingcancel');
Route::post('/opma_productionstart' ,'Production_Module_Opma\ProductionEndingController@startproduction')->name('opma_productionstart');
Route::post('/opma_productionendingfinish' ,'Production_Module_Opma\ProductionEndingController@insert')->name('opma_productionendingfinish');
Route::get('/opma_employeeproductionreport' ,'Production_Module_Opma\ProductionEndingController@employeeproduction')->name('opma_employeeproductionreport');

// Machine Downtime Log Section Routes
Route::get('opma_timechanging', 'Production_Module_Opma\TimechangingController@index')->name('opma_timechanging');
Route::post('opma_timechanginginsert', 'Production_Module_Opma\TimechangingController@store')->name('opma_timechanginginsert');
Route::post('opma_timechangingedit', 'Production_Module_Opma\TimechangingController@edit')->name('opma_timechangingedit');
Route::post('opma_timechangingupdate', 'Production_Module_Opma\TimechangingController@update')->name('opma_timechangingupdate');
Route::post('opma_timechangingdelete', 'Production_Module_Opma\TimechangingController@destroy')->name('opma_timechangingdelete');
 
Route::get('/opma_employeeproductionreport' ,'Production_Module_Opma\ProductionEndingController@employeeproduction')->name('opma_employeeproductionreport');

// Daily Production Ending Summary
Route::get('/opma_dailyproductionapprove' ,'Production_Module_Opma\ProductionDailyApproveController@index')->name('opma_dailyproductionapprove');
Route::post('/opma_daliyproductionsummarygenerate' ,'Production_Module_Opma\ProductionDailyApproveController@generatedailysummary')->name('opma_daliyproductionsummarygenerate');
Route::post('/opma_approvedailysummary' ,'Production_Module_Opma\ProductionDailyApproveController@approvedailysummary')->name('opma_approvedailysummary');

 // Production Reports 
Route::get('/opma_reportemployeeproduction' ,'Production_Module_Opma\ProductionreportController@index')->name('opma_reportemployeeproduction');
Route::get('/opma_reportemployeeproductiondailyreport' ,'Production_Module_Opma\ProductionreportController@dailyreport')->name('opma_reportemployeeproductiondailyreport');

 // Employee performance    
Route::resource('opma_employee_performance', 'Production_Module_Opma\EmployeePerformanceController');
Route::get('opma_employee_performance',['uses' => 'Production_Module_Opma\EmployeePerformanceController@index', 'as' => 'opma_employee_performance']); 
Route::post('addopma_employee_performance',['uses' => 'Production_Module_Opma\EmployeePerformanceController@store', 'as' => 'addopma_employee_performance']); 
Route::post('opma_employee_performance/update', 'Production_Module_Opma\EmployeePerformanceController@update')->name('opma_employee_performance.update');
Route::get('opma_employee_performance/destroy/{id}', 'Production_Module_Opma\EmployeePerformanceController@destroy');

 // Production Amount
Route::resource('opma_production_amount', 'Production_Module_Opma\ProductionAmountController');
Route::get('opma_production_amount',['uses' => 'Production_Module_Opma\ProductionAmountController@index', 'as' => 'opma_production_amount']); 
Route::post('addopma_production_amount',['uses' => 'Production_Module_Opma\ProductionAmountController@store', 'as' => 'addopma_production_amount']); 
Route::post('opma_production_amount/update', 'Production_Module_Opma\ProductionAmountController@update')->name('opma_production_amount.update');
Route::get('opma_production_amount/destroy/{id}', 'Production_Module_Opma\ProductionAmountController@destroy');

Route::post('opma_getEmployeeProductionDetails', 'Production_Module_Opma\ProductionDailyApproveController@getEmployeeProductionDetails')->name('opma_getEmployeeProductionDetails');

Route::get('opma_machinechart', ['uses' => 'Additionals\OpmaDashdoardController@machinechart', 'as' => 'opma_machinechart']);



// End of Opma Production Section Routes


