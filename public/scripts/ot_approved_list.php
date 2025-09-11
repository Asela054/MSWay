<?php

// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
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
$table = 'ot_approved';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
	array( 'db' => '`u`.`from`', 'dt' => 'from', 'field' => 'from' ),
	array( 'db' => '`u`.`to`', 'dt' => 'to', 'field' => 'to' ),
	array( 'db' => '`u`.`hours`', 'dt' => 'hours', 'field' => 'hours' ),
	array( 'db' => '`u`.`double_hours`', 'dt' => 'double_hours', 'field' => 'double_hours' ),
	array( 'db' => '`u`.`triple_hours`', 'dt' => 'triple_hours', 'field' => 'triple_hours' ),
	array( 'db' => '`u`.`is_holiday`', 'dt' => 'is_holiday', 'field' => 'is_holiday' ),
	array( 'db' => '`emp`.`emp_shift`', 'dt' => 'emp_shift', 'field' => 'emp_shift' ),
	array( 'db' => '`emp`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial' ),
	array( 'db' => '`emp`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name' ),
	array( 'db' => '`emp`.`emp_department`', 'dt' => 'emp_department', 'field' => 'emp_department' ),
	array( 'db' => '`bran`.`b_location`', 'dt' => 'b_location', 'field' => 'b_location' ),
	array( 'db' => '`dep`.`dept_name`', 'dt' => 'dept_name', 'field' => 'dept_name' ),
	 array('db' => '`emp`.`emp_id`', 'dt' => 'employee_display', 'field' => 'emp_id', 
          'formatter' => function($d, $row) {
              $employee = (object)[
                  'emp_name_with_initial' => $row['emp_name_with_initial'],
                  'calling_name' => $row['calling_name'],
                  'emp_id' => $row['emp_id']
              ];
              
              return EmployeeHelper::getDisplayName($employee);
          }
    )
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

$department = $_POST['department'];
$employee = $_POST['employee'];
$location = $_POST['location'];
$from_date = $_POST['from_date'];
$to_date = $_POST['to_date'];
$type = $_POST['type'];


$joinQuery = "FROM `ot_approved` AS `u` 
            left join `employees` AS `emp` on `emp`.`emp_id` = u.emp_id
			left join shift_types AS `sh` ON emp.emp_shift = sh.id  
            left join (SELECT `id`,`name` AS `dept_name` FROM `departments`) AS `dep` ON emp.emp_department = dep.id 
            left join (SELECT `id`,`location` AS `b_location` FROM `branches`) AS `bran` ON emp.emp_location = bran.id";
	
	$extraWhere = "1=1";

	if ($department != '') {
		$extraWhere .= " AND `emp`.`emp_department` = '$department'";
	}	
	if ($employee != '') {
		$extraWhere .= " AND `emp`.`emp_id` = '$employee'";
	}

	if ($location != '') {
		$extraWhere .= " AND `emp`.`emp_location` = '$location'";
	}

	if ($from_date != '' && $to_date != '') {
	    $extraWhere .= " AND `u`.date BETWEEN '$from_date' AND '$to_date'";
	}


echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
