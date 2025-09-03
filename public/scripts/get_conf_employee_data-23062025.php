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
$table = 'employees';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`udt`.`payroll_profile_id`', 'dt' => 'payroll_profile_id', 'field' => 'payroll_profile_id' ),
	array( 'db' => '`udt`.`emp_etfno`', 'dt' => 'emp_etfno', 'field' => 'emp_etfno' ),
	array( 'db' => '`udt`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial' ),
	array( 'db' => '`udt`.`location`', 'dt' => 'location', 'field' => 'location' ),
	array( 'db' => '`udt`.`basic_salary`', 'dt' => 'basic_salary', 'field' => 'basic_salary' ),
	array( 'db' => '`udt`.`process_name`', 'dt' => 'process_name', 'field' => 'process_name' )
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


$joinQuery = "from (select `pp`.`id` as payroll_profile_id, `u`.`emp_etfno`, `u`.`emp_name_with_initial`, `b`.`name` AS location, `pp`.`basic_salary`, ifnull(pt.process_name, '') as process_name FROM `employees` AS `u` 
INNER JOIN `companies` AS `b` ON (`u`.`emp_company` = `b`.`id`)
INNER JOIN `payroll_profiles` AS `pp` ON (`u`.`id` = `pp`.`emp_id`)
INNER JOIN `payroll_process_types` AS `pt` ON (`pp`.`payroll_process_type_id` = `pt`.`id`)) as udt";

$extraWhere = "";


echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
