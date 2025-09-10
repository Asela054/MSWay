<?php
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

// DB table to use
$table = 'attendances';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'at_id', 'field' => 'id'),
    array('db' => '`u`.`uid`', 'dt' => 'uid', 'field' => 'uid'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`firsttimestamp`', 'dt' => 'firsttimestamp', 'field' => 'firsttimestamp'),
    array('db' => '`u`.`date`', 'dt' => 'date_row', 'field' => 'date'),
    array('db' => '`u`.`formatted_date`', 'dt' => 'date', 'field' => 'formatted_date'),
    array('db' => '`u`.`lasttimestamp`', 'dt' => 'lasttimestamp', 'field' => 'lasttimestamp'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`dep_name`', 'dt' => 'dep_name', 'field' => 'dep_name'),
    array('db' => '`u`.`shift_name`', 'dt' => 'shift_name', 'field' => 'shift_name'),
    array('db' => '`u`.`first_time_stamp`',   'dt' => 'first_time_stamp', 'field' => 'first_time_stamp' ),
	array('db' => '`u`.`last_time_stamp`',   'dt' => 'last_time_stamp', 'field' => 'last_time_stamp' ),
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
        `at1`.`id`, 
        `at1`.`uid`, 
        `employees`.`emp_name_with_initial`, 
        `employees`.`emp_id`, 
        `employees`.`calling_name`, 
        `branches`.`location`, 
        `departments`.`name` AS `dep_name`, 
        `shift_types`.`shift_name`,
        `at1`.`date`, 
        DATE_FORMAT(`at1`.`date`, '%Y-%m-%d') AS `formatted_date`, 
        MIN(`at1`.`timestamp`) AS `firsttimestamp`, 
        CASE WHEN MIN(`at1`.`timestamp`) = MAX(`at1`.`timestamp`) THEN NULL ELSE MAX(`at1`.`timestamp`) END AS `lasttimestamp`, 
        DATE_FORMAT(MIN(`at1`.`timestamp`), '%H:%i') AS `first_time_stamp`, 
        DATE_FORMAT(CASE WHEN MIN(`at1`.`timestamp`) = MAX(`at1`.`timestamp`) THEN NULL ELSE MAX(`at1`.`timestamp`) END, '%H:%i') AS `last_time_stamp`
    FROM `attendances` AS `at1` 
    JOIN `employees` ON `at1`.`uid` = `employees`.`emp_id` 
    LEFT JOIN `branches` ON `at1`.`location` = `branches`.`id` 
    LEFT JOIN `departments` ON `departments`.`id` = `employees`.`emp_department`
    LEFT JOIN `shift_types` ON `employees`.`emp_shift` = `shift_types`.`id`
    WHERE `at1`.`deleted_at` IS NULL";

    if (!empty($_REQUEST['company'])) {
        $company = $_REQUEST['company'];
        $sql .= " AND `employees`.`emp_company` = '$company'";
    }

    if (!empty($_POST['department'])) {
        $department = $_POST['department'];
        $sql .= " AND `departments`.`id` = '$department'";
    }

    if (!empty($_POST['employee'])) {
        $employee = $_POST['employee'];
        $sql .= " AND `employees`.`emp_id` = '$employee'";
    }

    if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $sql .= " AND `at1`.`date` BETWEEN '$from_date' AND '$to_date'";
    }
    
    $sql .= " GROUP BY `at1`.`uid`, `at1`.`date`";
    
    $sql .= " HAVING COUNT(at1.timestamp) < 2";

    $joinQuery = "FROM (" . $sql . ") as `u`";
    $extraWhere = "";
    
 echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery));
 ?>