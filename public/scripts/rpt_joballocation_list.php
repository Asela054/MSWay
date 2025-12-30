<?php
session_start();
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'job_attendance';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`employee_id`', 'dt' => 'emp_id', 'field' => 'employee_id'),
    array('db' => '`u`.`attendance_date`', 'dt' => 'attendance_date', 'field' => 'attendance_date'),
    array('db' => '`u`.`on_time`', 'dt' => 'on_time', 'field' => 'on_time'),
    array('db' => '`u`.`off_time`', 'dt' => 'off_time', 'field' => 'off_time'),
    array('db' => '`u`.`location_status`', 'dt' => 'location_status', 'field' => 'location_status'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`employee_id`', 'dt' => 'employee_display', 'field' => 'employee_id', 
        'formatter' => function($d, $row) {
            $employee = (object)[
                'emp_name_with_initial' => $row['emp_name_with_initial'],
                'calling_name' => $row['calling_name'],
                'emp_id' => $row['employee_id']
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

require('ssp.customized.class.php');

$sql = "SELECT 
    `ja`.`id`,
    `ja`.`employee_id`,
    `ja`.`attendance_date`,
    `ja`.`on_time`,
    `ja`.`off_time`,
    `ja`.`location_status`,
    `ja`.`location_id`,
    `employees`.`emp_name_with_initial`,
    `employees`.`calling_name`,
    `employees`.`emp_id`,
    `branches`.`location`
FROM `job_attendance` as `ja`
LEFT JOIN `employees` ON `ja`.`employee_id` = `employees`.`emp_id`
LEFT JOIN `branches` ON `ja`.`location_id` = `branches`.`id`
WHERE `ja`.`status` IN (1, 2) AND `ja`.`approve_status` = 1";

// Add filters
if (!empty($_REQUEST['location'])) {
    $location = $_REQUEST['location'];
    $sql .= " AND `ja`.`location_id` = '$location'";
}

if (!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date'])) {
    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];
    $sql .= " AND `ja`.`attendance_date` BETWEEN '$from_date' AND '$to_date'";
}

if (!empty($_REQUEST['employee_f'])) {
    $employee_f = $_REQUEST['employee_f'];
    $sql .= " AND `ja`.`employee_id` = '$employee_f'";
}

// Add user access rights filter directly to the main query
$userId = UserHelper::getLoggedInUserId();

if ($userId) {
    $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
    
    if ($mysqli->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    
    $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $mysqli);
    if (!empty($accessibleEmployeeIds)) {
        $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
        $sql .= " AND `employees`.`emp_id` IN ($empIds)";
    } else {
        $sql .= " AND 1 = 0";
    }
    $mysqli->close();
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>