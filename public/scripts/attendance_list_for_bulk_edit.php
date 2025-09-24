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
    array('db' => '`sub`.`month`', 'dt' => 'month', 'field' => 'month'),
    array('db' => '`sub`.`firsttimestamp`', 'dt' => 'firsttimestamp', 'field' => 'firsttimestamp'),
    array('db' => '`sub`.`lasttimestamp`', 'dt' => 'lasttimestamp', 'field' => 'lasttimestamp'),
    array('db' => '`employees`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`employees`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`employees`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`branches`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`departments`.`name`', 'dt' => 'dept_name', 'field' => 'dept_name', 'as' => 'dept_name'),
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
$extraWhere .= " AND `employees`.`deleted` = 0";
$extraWhere .= " AND `employees`.`is_resigned` = 0";

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
if (!empty($_POST['date'])) {
    $date = $_POST['date'];
    $extraWhere .= " AND `sub`.`date` = '$date'";
}

$joinQuery = "FROM (
    SELECT `at1`.`uid`, `at1`.`date`, `at1`.`location`,
           DATE_FORMAT(`at1`.`date`, '%Y-%m') AS `month`,
           MIN(`at1`.`timestamp`) AS `firsttimestamp`,
           CASE 
               WHEN MIN(`at1`.`timestamp`) = MAX(`at1`.`timestamp`) THEN NULL 
               ELSE MAX(`at1`.`timestamp`) 
           END AS `lasttimestamp`,
           `at1`.`deleted_at`, `at1`.`approved`
    FROM `attendances` AS `at1`
    GROUP BY `at1`.`uid`, `at1`.`date`, `at1`.`location`, `at1`.`deleted_at`, `at1`.`approved`
) AS `sub`
LEFT JOIN `employees` ON `sub`.`uid` = `employees`.`emp_id`
LEFT JOIN `branches` ON `sub`.`location` = `branches`.`id`
LEFT JOIN `departments` ON `employees`.`emp_department` = `departments`.`id`";

    echo json_encode(
        SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
?>
