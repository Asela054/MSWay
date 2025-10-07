<?php
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
// DB table to use
$table = 'employee_late_attendances';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`check_in_time`', 'dt' => 'check_in_time', 'field' => 'check_in_time'),
    array('db' => '`u`.`check_out_time`', 'dt' => 'check_out_time', 'field' => 'check_out_time'),
    array('db' => '`u`.`working_hours`', 'dt' => 'working_hours', 'field' => 'working_hours'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`dept_name`', 'dt' => 'dept_name', 'field' => 'dept_name'),
    array('db' => '`u`.`dept_id`', 'dt' => 'dept_id', 'field' => 'dept_id'),
    array('db' => '`u`.`location_id`', 'dt' => 'location_id', 'field' => 'location_id'),
    array('db' => '`u`.`is_approved`', 'dt' => 'is_approved_int', 'field' => 'is_approved'),
    array('db' => '`u`.`emp_id`', 'dt' => 'employee_display', 'field' => 'emp_id', 
        'formatter' => function($d, $row) {
            $employee = (object)[
                'emp_name_with_initial' => $row['emp_name_with_initial'],
                'calling_name' => $row['calling_name'],
                'emp_id' => $row['emp_id']
            ];
            
            return EmployeeHelper::getDisplayName($employee);
        }
    ),
    array('db' => '`u`.`is_approved`', 'dt' => 'is_approved', 'field' => 'is_approved',
        'formatter' => function($d, $row) {
            return ($d == 0) ? 'No' : 'Yes';
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

require('ssp.customized.class.php');

// Build the main SQL query
$sql = "SELECT 
    `ela`.`id`,
    `ela`.`emp_id`,
    `ela`.`date`,
    `ela`.`check_in_time`,
    `ela`.`check_out_time`,
    `ela`.`working_hours`,
    `ela`.`is_approved`,
    `employees`.`emp_name_with_initial`,
    `employees`.`calling_name`,
    `branches`.`location`,
    `branches`.`id` as `location_id`,
    `departments`.`name` as `dept_name`,
    `departments`.`id` as `dept_id`
FROM `employee_late_attendances` as `ela`
LEFT JOIN `attendances` as `at1` ON `at1`.`id` = `ela`.`id`
JOIN `employees` ON `employees`.`emp_id` = `ela`.`emp_id`
LEFT JOIN `branches` ON `at1`.`location` = `branches`.`id`
LEFT JOIN `departments` ON `departments`.`id` = `employees`.`emp_department`
LEFT JOIN `companies` ON `companies`.`id` = `departments`.`company_id`
WHERE `ela`.`is_approved` = 0";

// Add filters
if (!empty($_REQUEST['department'])) {
    $department = $_REQUEST['department'];
    $sql .= " AND `departments`.`id` = '$department'";
}

if (!empty($_REQUEST['company'])) {
    $company = $_REQUEST['company'];
    $sql .= " AND `employees`.`emp_company` = '$company'";
}

if (!empty($_REQUEST['location'])) {
    $location = $_REQUEST['location'];
    $sql .= " AND `at1`.`location` = '$location'";
}

if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $sql .= " AND `ela`.`date` BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>