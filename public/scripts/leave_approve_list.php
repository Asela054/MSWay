<?php

// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

$table = 'leaves';
$primaryKey = 'id';

$columns = array(
    array('db' => '`sub`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`sub`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id', 'visible' => false),
    array('db' => '`sub`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial', 'visible' => false),
    array('db' => '`sub`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name', 'visible' => false),
    array('db' => '`sub`.`leave_type`', 'dt' => 'leave_type', 'field' => 'leave_type'),
    array('db' => '`sub`.`covering_emp_name`', 'dt' => 'covering_emp', 'field' => 'covering_emp_name'),
    array('db' => '`sub`.`dep_name`', 'dt' => 'dep_name', 'field' => 'dep_name'),
    array('db' => '`sub`.`leave_from`', 'dt' => 'leave_from', 'field' => 'leave_from'),
    array('db' => '`sub`.`leave_to`', 'dt' => 'leave_to', 'field' => 'leave_to'),
    array('db' => '`sub`.`half_short`', 'dt' => 'half_short', 'field' => 'half_short'),
    array('db' => '`sub`.`status`', 'dt' => 'status', 'field' => 'status'),
    array('db' => '`sub`.`reson`', 'dt' => 'reson', 'field' => 'reson'),
    array('db' => '`sub`.`emp_id`', 'dt' => 'employee_display', 'field' => 'emp_id', 
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

require('config.php');

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php');

$extraWhere = "1=1";

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $extraWhere .= " AND `sub`.`dep_id` = '$department'";
}
if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $extraWhere .= " AND `sub`.`emp_id` = '$employee'";
}
if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND `sub`.`location_id` = '$location'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $extraWhere .= " AND `sub`.`leave_from` BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM (
    SELECT 
        `leaves`.`id`,
        `leaves`.`emp_id`,
        `e`.`emp_name_with_initial`,
        `e`.`calling_name`,
        `e`.`emp_department` AS `dep_id`,
        `e`.`emp_location` AS `location_id`,
        `leave_types`.`leave_type`,
        `ec`.`emp_name_with_initial` AS `covering_emp_name`,
        `departments`.`name` AS `dep_name`,
        `leaves`.`leave_from`,
        `leaves`.`leave_to`,
        `leaves`.`half_short`,
        `leaves`.`status`,
        `leaves`.`reson`
    FROM `leaves`
    LEFT JOIN `leave_types` ON `leaves`.`leave_type` = `leave_types`.`id`
    LEFT JOIN `employees` AS `ec` ON `leaves`.`emp_covering` = `ec`.`emp_id`
    LEFT JOIN `employees` AS `e` ON `leaves`.`emp_id` = `e`.`emp_id`
    LEFT JOIN `branches` ON `e`.`emp_location` = `branches`.`id`
    LEFT JOIN `departments` ON `e`.`emp_department` = `departments`.`id`
) AS `sub`";

try {
    echo json_encode(
        SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>