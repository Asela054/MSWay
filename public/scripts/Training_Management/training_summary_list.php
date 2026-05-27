<?php
// Include the EmployeeHelper class
require_once(__DIR__ . '/../../../app/Helpers/EmployeeHelper.php');

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
        'formatter' => function ($d, $row) {
            $employee = (object)[
                'emp_name_with_initial' => $row['emp_name_with_initial'],
                'calling_name' => $row['calling_name'],
                'emp_id' => $row['emp_id']
            ];
            return EmployeeHelper::getDisplayName($employee);
        }
    ),

    array('db' => '`u`.`date`', 'dt' => 3, 'field' => 'date'),
    array(
        'db'    => '`u`.`id`',
        'dt'    => 'action',
        'field' => 'id',
        'formatter' => function ($d, $row) {
            $alloc = $row['allocation_id'];
            $emp   = $row['emp_id'];
            return '
            <button type="button" class="btn btn-info btn-sm open-types-modal mr-1"
                data-allocation="' . $alloc . '" data-employee="' . $emp . '">
                <i class="fas fa-eye mr-1"></i> View
            </button>
            <button type="button" class="btn btn-secondary btn-sm print-row"
                data-allocation="' . $alloc . '" data-employee="' . $emp . '">
                <i class="fas fa-print mr-1"></i> Print
            </button>';
        }
    ),
);

// SQL server connection information
require(__DIR__ . '/../config.php');

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require(__DIR__ . '/../ssp.customized.class.php');

// Get filter parameters from POST request
$allocation_id = isset($_POST['allocation_id']) ? intval($_POST['allocation_id']) : 0;
$from_date     = isset($_POST['from_date'])     ? $_POST['from_date'] : '';
$to_date       = isset($_POST['to_date'])       ? $_POST['to_date'] : '';

$sql = "SELECT
    `tea`.`id`,
    `tea`.`emp_id`,
    `tea`.`allocation_id`,
    `e`.`emp_name_with_initial`,
    `e`.`calling_name`,
    `ta`.`date`
FROM `training_emp_allocations` as `tea`
LEFT JOIN `employees` AS `e` ON `tea`.`emp_id` = `e`.`emp_id`
LEFT JOIN `training_allocations` AS `ta` ON `tea`.`allocation_id` = `ta`.`id`
WHERE `tea`.`status` = 1";

// Add filters
if ($allocation_id > 0) {
    $sql .= " AND `tea`.`allocation_id` = " . $allocation_id;
}

if (!empty($from_date)) {
    $sql .= " AND `ta`.`date` >= '" . $from_date . "'";
}

if (!empty($to_date)) {
    $sql .= " AND `ta`.`date` <= '" . $to_date . "'";
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
