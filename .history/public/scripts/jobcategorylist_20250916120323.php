<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'job_categories';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`category`', 'dt' => 'category', 'field' => 'category' ),
	array( 'db' => '`u`.`annual_leaves`', 'dt' => 'annual_leaves', 'field' => 'annual_leaves' ),
	array( 'db' => '`u`.`casual_leaves`', 'dt' => 'casual_leaves', 'field' => 'casual_leaves' ),
	array( 'db' => '`u`.`medical_leaves`', 'dt' => 'medical_leaves', 'field' => 'medical_leaves' ),
    array( 'db' => '`u`.`emp_payroll_workdays`', 'dt' => 'emp_payroll_workdays', 'field' => 'emp_payroll_workdays' ),
    array( 'db' => '`u`.`emp_payroll_workhrs`', 'dt' => 'emp_payroll_workhrs', 'field' => 'emp_payroll_workhrs' ),
    array( 'db' => '`u`.`ot_app_hours`', 'dt' => 'ot_app_hours', 'field' => 'ot_app_hours' ),
    array( 'db' => '`u`.`holiday_ot_minimum_min`', 'dt' => 'holiday_ot_minimum_min', 'field' => 'holiday_ot_minimum_min' ),
    array( 'db' => '`u`.`spe_deduct_pre`', 'dt' => 'spe_deduct_pre', 'field' => 'spe_deduct_pre' ),
    array( 'db' => '`u`.`shift_hours`', 'dt' => 'shift_hours', 'field' => 'shift_hours' ),
    array( 'db' => '`u`.`work_hour_date`', 'dt' => 'work_hour_date', 'field' => 'work_hour_date' ),
    array( 'db' => '`u`.`morning_ot`', 'dt' => 'morning_ot', 'field' => 'morning_ot' ),
    array( 'db' => '`u`.`lunch_deduct_type`', 'dt' => 'lunch_deduct_type', 'field' => 'lunch_deduct_type' ),
    array( 'db' => '`u`.`lunch_deduct_min`', 'dt' => 'lunch_deduct_min', 'field' => 'lunch_deduct_min' ),
    array( 'db' => '`u`.`holiday_work_hours`', 'dt' => 'holiday_work_hours', 'field' => 'holiday_work_hours' ),
    array( 'db' => '`u`.`late_type`', 'dt' => 'late_type', 'field' => 'late_type' ),
    array( 'db' => '`u`.`is_sat_ot_type_as_act`', 'dt' => 'is_sat_ot_type_as_act', 'field' => 'is_sat_ot_type_as_act' ),
    array( 'db' => '`u`.`custom_saturday_ot_type`', 'dt' => 'custom_saturday_ot_type', 'field' => 'custom_saturday_ot_type' ),
    array( 'db' => '`u`.`is_sun_ot_type_as_act`', 'dt' => 'is_sun_ot_type_as_act', 'field' => 'is_sun_ot_type_as_act' ),
    array( 'db' => '`u`.`custom_sunday_ot_type`', 'dt' => 'custom_sunday_ot_type', 'field' => 'custom_sunday_ot_type' ),
    array( 'db' => '`u`.`shift_hours`', 'dt' => 'shift_hours', 'field' => 'shift_hours' ),
    array( 'db' => '`u`.`shift_hours`', 'dt' => 'shift_hours', 'field' => 'shift_hours' ),
    array( 'db' => '`u`.`shift_hours`', 'dt' => 'shift_hours', 'field' => 'shift_hours' ),
    array( 'db' => '`u`.`shift_hours`', 'dt' => 'shift_hours', 'field' => 'shift_hours' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );

$joinQuery = "FROM `job_categories` AS `u`";
	
$extraWhere = "1=1";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
