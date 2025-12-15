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
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id' ,'visible' => false),
    array('db' => '`u`.`attendance_date`', 'dt' => 'attendance_date', 'field' => 'attendance_date'),
    array('db' => '`u`.`employee_id`', 'dt' => 'employee_id', 'field' => 'employee_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'employee_name', 'field' => 'emp_name_with_initial','visible' => false),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name','visible' => false),
    array('db' => '`u`.`shift_id`', 'dt' => 'shift_id', 'field' => 'shift_id'),
    array('db' => '`u`.`on_time`', 'dt' => 'on_time', 'field' => 'on_time'),
    array('db' => '`u`.`off_time`', 'dt' => 'off_time', 'field' => 'off_time'),
    array('db' => '`u`.`reason`', 'dt' => 'reason', 'field' => 'reason'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`location_id`', 'dt' => 'location_id', 'field' => 'location_id'),
    array('db' => '`u`.`location_status`', 'dt' => 'location_status', 'field' => 'location_status'),
    array('db' => '`u`.`approve_status`', 'dt' => 'approve_status', 'field' => 'approve_status'),
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
        `ja`.`id`,
        `ja`.`attendance_date`,
        `ja`.`employee_id`,
        `e`.`emp_name_with_initial`,
        `e`.`emp_id`,
        `e`.`calling_name`,
        `ja`.`shift_id`,
        `ja`.`on_time`,
        `ja`.`off_time`,
        `ja`.`reason`,
        `ja`.`location_id`,
        `b`.`location`,
        `ja`.`location_status`,
        `ja`.`approve_status`
    FROM `job_attendance` AS `ja`
    LEFT JOIN `employees` AS `e` ON `ja`.`employee_id` = `e`.`emp_id`
    LEFT JOIN `branches` AS `b` ON `ja`.`location_id` = `b`.`id`
    WHERE 1=1";


$sql .= " AND `ja`.`status` != 3";

// new filter based on user access rights
$pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_username, $db_password);
$userId = UserHelper::getLoggedInUserId($pdo);
$accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $pdo);

if (!empty($accessibleEmployeeIds)) {
    $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
    $sql .= " AND e.emp_id IN ($empIds)";
} else {
    $sql .= " AND 1 = 0";
}
// end of new filter


$joinQuery = "FROM (" . $sql . ") as `u`";

$extraWhere = "";



echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>