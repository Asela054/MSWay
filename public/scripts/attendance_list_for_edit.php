<?php

// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

$table = 'attendances';
$primaryKey = 'id';

$columns = array(
    array('db' => '`sub`.`uid`', 'dt' => 'uid', 'field' => 'uid'),
    array('db' => '`sub`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`sub`.`first_time_stamp`', 'dt' => 'first_time_stamp', 'field' => 'first_time_stamp'),
    array('db' => '`sub`.`last_time_stamp`', 'dt' => 'last_time_stamp', 'field' => 'last_time_stamp'),
    array('db' => '`employees`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`employees`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`employees`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`branches`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`departments`.`name`', 'dt' => 'dep_name', 'field' => 'dep_name', 'as' => 'dep_name'),
    array('db' => '`employees`.`emp_id`', 'dt' => 'employee_display', 'field' => 'emp_id', 
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

$extraWhere = "`sub`.`deleted_at` IS NULL AND `sub`.`approved` = '0'";

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $extraWhere .= " AND `employees`.`emp_department` = '$department'";
}
if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $extraWhere .= " AND `employees`.`emp_id` = '$employee'";
}
if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND `sub`.`location` = '$location'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $extraWhere .= " AND `sub`.`date` BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM (
    SELECT `at1`.`uid`, `at1`.`date`, `at1`.`location`,
           MIN(`at1`.`timestamp`) AS `first_time_stamp`,
           CASE 
               WHEN MIN(`at1`.`timestamp`) = MAX(`at1`.`timestamp`) THEN NULL 
               ELSE MAX(`at1`.`timestamp`) 
           END AS `last_time_stamp`,
           `at1`.`deleted_at`, `at1`.`approved`
    FROM `attendances` AS `at1`
    GROUP BY `at1`.`uid`, `at1`.`date`, `at1`.`location`, `at1`.`deleted_at`, `at1`.`approved`
) AS `sub`
LEFT JOIN `employees` ON `sub`.`uid` = `employees`.`emp_id`
LEFT JOIN `branches` ON `sub`.`location` = `branches`.`id`
LEFT JOIN `departments` ON `employees`.`emp_department` = `departments`.`id`";

try {
    echo json_encode(
        SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>