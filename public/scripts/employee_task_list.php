<?php

// DB table to use
$table = 'employee_task';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`taskname`', 'dt' => 'task', 'field' => 'taskname'),
    array('db' => '`u`.`amount`', 'dt' => 'amount', 'field' => 'amount'),
    array('db' => '`u`.`description`', 'dt' => 'description', 'field' => 'description')
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
        `ep`.`id`,
        `ep`.`emp_id`,
        `e`.`emp_name_with_initial`,
        `ep`.`date`,
        `p`.`taskname`,
        `ep`.`amount`,
        `ep`.`description`
    FROM `employee_task` AS `ep`
    LEFT JOIN `task` AS `p` ON `ep`.`task_id` = `p`.`id`
    LEFT JOIN `employees` AS `e` ON `ep`.`emp_id` = `e`.`emp_id`
    WHERE 1=1";

if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $sql .= " AND `ep`.`emp_id` = '$employee'";
}
if (!empty($_POST['task'])) {
    $task = $_POST['task'];
    $sql .= " AND `ep`.`task_id` = '$task'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $sql .= " AND `ep`.`date` BETWEEN '$from_date' AND '$to_date'";
}

$sql .= " AND `ep`.`status` = 1";

$joinQuery = "FROM (" . $sql . ") as `u`";

$extraWhere = "";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>