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
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_fp_id`', 'dt' => 'emp_fp_id', 'field' => 'emp_fp_id'),
    array('db' => '`u`.`emp_etfno`', 'dt' => 'emp_etfno', 'field' => 'emp_etfno'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`emp_join_date`', 'dt' => 'emp_join_date', 'field' => 'emp_join_date'),
    array('db' => '`ua`.`emp_status`', 'dt' => 'emp_status', 'field' => 'emp_status'),
    array('db' => '`ub`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`ue`.`title`', 'dt' => 'title', 'field' => 'title'),
    array('db' => '`uc`.`name`', 'dt' => 'name', 'field' => 'name'),
    array('db' => '`u`.`is_resigned`', 'dt' => 'is_resigned', 'field' => 'is_resigned'),
    array('db' => '`u`.`emp_national_id`', 'dt' => 'emp_national_id', 'field' => 'emp_national_id'),
    array('db' => '`ud`.`category`', 'dt' => 'category', 'field' => 'category'),
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

$joinQuery = "FROM `employees` AS `u` LEFT JOIN `employment_statuses` AS `ua` ON (`ua`.`id` = `u`.`emp_status`) LEFT JOIN `branches` AS `ub` ON (`ub`.`id` = `u`.`emp_location`) LEFT JOIN `departments` AS `uc` ON (`uc`.`id` = `u`.`emp_department`) LEFT JOIN `job_categories` AS `ud` ON (`ud`.`id` = `u`.`job_category_id`) LEFT JOIN `job_titles` AS `ue` ON (`ue`.`id` = `u`.`emp_job_code`)";

$current_date_time = date('Y-m-d H:i:s');
$previous_month_date = date('Y-m-d', strtotime('-1 month'));

$extraWhere = "`u`.`deleted` = 0 AND (`u`.`is_resigned` = 0 OR (`u`.`is_resigned` = 1 AND `u`.`resignation_date` BETWEEN '$previous_month_date' AND '$current_date_time'))";

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $extraWhere .= " AND `uc`.`id` = '$department'";
}
if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $extraWhere .= " AND `u`.`emp_id` = '$employee'";
}
if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND `u`.`emp_location` = '$location'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $extraWhere .= " AND `u`.`emp_join_date` BETWEEN '$from_date' AND '$to_date'";
}

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);

?>