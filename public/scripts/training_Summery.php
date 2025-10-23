<?php
// Include the EmployeeHelper class
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

use App\Helpers\EmployeeHelper;

// DB table to use
$table = 'training_emp_allocations';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 0, 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 1, 'field' => 'emp_id'),
    array(
        'db' => '`u`.`emp_id`', 
        'dt' => 2, 
        'field' => 'emp_id',
        'formatter' => function($d, $row) {
            $employee = (object)[
                'emp_name_with_initial' => $row['emp_name_with_initial'],
                'calling_name' => $row['calling_name'],
                'emp_id' => $row['emp_id']
            ];
            return EmployeeHelper::getDisplayName($employee);
        }
    ),
    array('db' => '`u`.`training_type`', 'dt' => 3, 'field' => 'training_type'),
    array('db' => '`u`.`venue`', 'dt' => 4, 'field' => 'venue'),
    array('db' => '`u`.`start_time`', 'dt' => 5, 'field' => 'start_time'),
    array('db' => '`u`.`end_time`', 'dt' => 6, 'field' => 'end_time'),
    array('db' => '`u`.`marks`', 'dt' => 7, 'field' => 'marks'),
    array(
        'db' => '`u`.`is_attend`', 
        'dt' => 8, 
        'field' => 'is_attend',
        'formatter' => function($d, $row) {
            if ($d == 1) {
                return '<span class="badge badge-success">Attended</span>';
            } else {
                return '<span class="badge badge-warning">Not Attended</span>';
            }
        }
    ),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 9, 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 10, 'field' => 'calling_name')
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

// Get filter parameters from POST request
$type = isset($_POST['type']) ? intval($_POST['type']) : 0;
$venue = isset($_POST['venue']) ? intval($_POST['venue']) : 0;
$employee = isset($_POST['employee']) ? intval($_POST['employee']) : 0;
$from_date = isset($_POST['from_date']) ? $_POST['from_date'] : '';
$to_date = isset($_POST['to_date']) ? $_POST['to_date'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

$sql = "SELECT
    `tea`.`id`,
    `tea`.`emp_id`,
    `tea`.`allocation_id`,
    `tea`.`marks`,
    `tea`.`is_attend`,
    `e`.`emp_name_with_initial`,
    `e`.`calling_name`,
    `tt`.`name` as `training_type`,
    `ta`.`venue`,
    `ta`.`start_time`,
    `ta`.`end_time`,
    `ta`.`type_id`
FROM `training_emp_allocations` as `tea`
LEFT JOIN `employees` AS `e` ON `tea`.`emp_id` = `e`.`emp_id`
LEFT JOIN `training_allocations` AS `ta` ON `tea`.`allocation_id` = `ta`.`id`
LEFT JOIN `training_types` AS `tt` ON `ta`.`type_id` = `tt`.`id`
WHERE `tea`.`status` = 1";

// Add filters
if ($type > 0) {
    $sql .= " AND `ta`.`type_id` = " . $type;
}

if ($venue > 0) {
    $sql .= " AND `ta`.`id` = " . $venue;
}

if ($employee > 0) {
    $sql .= " AND `tea`.`emp_id` = " . $employee;
}

if (!empty($from_date)) {
    $sql .= " AND DATE(`ta`.`start_time`) >= '" . $from_date . "'";
}

if (!empty($to_date)) {
    $sql .= " AND DATE(`ta`.`start_time`) <= '" . $to_date . "'";
}

if ($status == 'attended') {
    $sql .= " AND `tea`.`is_attend` = 1";
} elseif ($status == 'not_attended') {
    $sql .= " AND `tea`.`is_attend` = 0";
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>