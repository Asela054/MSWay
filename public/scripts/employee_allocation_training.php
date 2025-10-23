<?php
// Include the EmployeeHelper class
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

use App\Helpers\EmployeeHelper;

// DB table to use
$table = 'training_emp_allocations';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name', 'field' => 'emp_name_with_initial', 'visible' => false),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name', 'visible' => false),
    array('db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status', 'visible' => false),
    array('db' => '`u`.`allocation_id`', 'dt' => 'allocation_id', 'field' => 'allocation_id', 'visible' => false),
    array(
        'db' => '`u`.`emp_id`', 
        'dt' => 'employee_display', 
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
    array(
        'db' => '`u`.`id`', 
        'dt' => 'action', 
        'field' => 'id',
        'formatter' => function($d, $row) {
            return '<button type="button" name="delete" id="'.$d.'" class="delete btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>';
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

// Get allocation_id from POST request
$allocation_id = isset($_POST['allocation_id']) ? intval($_POST['allocation_id']) : 0;

$sql = "SELECT
    `training_emp_allocations`.`id`,
    `training_emp_allocations`.`emp_id`,
    `training_emp_allocations`.`allocation_id`,
    `e`.`emp_name_with_initial`,
    `e`.`calling_name`,
    `training_emp_allocations`.`status`
FROM `training_emp_allocations`
LEFT JOIN `employees` AS `e` ON `training_emp_allocations`.`emp_id` = `e`.`emp_id`
WHERE `training_emp_allocations`.`status` = 1";

// Add allocation_id filter if provided
if ($allocation_id > 0) {
    $sql .= " AND `training_emp_allocations`.`allocation_id` = " . $allocation_id;
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "`u`.`status` = 1";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>