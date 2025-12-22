<?php
session_start();
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'employee_late_attendances';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id', 'visible' => false),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial', 'visible' => false),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name', 'visible' => false),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`check_in_time`', 'dt' => 'check_in_time', 'field' => 'check_in_time'),
    array('db' => '`u`.`check_out_time`', 'dt' => 'check_out_time', 'field' => 'check_out_time'),
    array('db' => '`u`.`working_hours`', 'dt' => 'working_hours', 'field' => 'working_hours'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`dep_name`', 'dt' => 'dep_name', 'field' => 'dep_name'),
    array('db' => '`u`.`emp_id`', 'dt' => 'employee_display', 'field' => 'emp_id', 
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

require('ssp.customized.class.php');

$sql = "SELECT 
    `ela`.`id`,
    `ela`.`emp_id`,
    `ela`.`date`,
    `ela`.`check_in_time`,
    `ela`.`check_out_time`,
    `ela`.`working_hours`,
    `employees`.`emp_name_with_initial`,
    `employees`.`calling_name`,
    `branches`.`location`,
    `departments`.`name` as `dep_name`
FROM `employee_late_attendances` as `ela`
JOIN `employees` ON `ela`.`emp_id` = `employees`.`emp_id`
LEFT JOIN `attendances` as `at1` ON `at1`.`id` = `ela`.`attendance_id`
LEFT JOIN `branches` ON `at1`.`location` = `branches`.`id`
LEFT JOIN `departments` ON `departments`.`id` = `employees`.`emp_department`
WHERE 1=1";

// Add filters
if (!empty($_REQUEST['department'])) {
    $department = $_REQUEST['department'];
    $sql .= " AND `departments`.`id` = '$department'";
}

if (!empty($_REQUEST['employee'])) {
    $employee = $_REQUEST['employee'];
    $sql .= " AND `employees`.`emp_id` = '$employee'";
}

if (!empty($_REQUEST['location'])) {
    $location = $_REQUEST['location'];
    $sql .= " AND `at1`.`location` = '$location'";
}

if (!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date'])) {
    $from_date = $_REQUEST['from_date'];
    $to_date = $_REQUEST['to_date'];
    $sql .= " AND `ela`.`date` BETWEEN '$from_date' AND '$to_date'";
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
        $sql .= " AND `ela`.`emp_id` IN ($empIds)";
    } else {
        $sql .= " AND 1 = 0";
    }
    $mysqli->close();
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";






echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>