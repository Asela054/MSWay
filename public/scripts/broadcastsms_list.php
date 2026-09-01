<?php

// DB table to use
$table = 'broadcast_messages';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`type_label`', 'dt' => 'type_label', 'field' => 'type_label'),
    array('db' => '`u`.`department_name`', 'dt' => 'department_name', 'field' => 'department_name'),
    array('db' => '`u`.`employee_label`', 'dt' => 'employee_label', 'field' => 'employee_label'),
    array('db' => '`u`.`message`', 'dt' => 'message', 'field' => 'message')
);

// SQL server connection information
require('config.php');
require('ssp.customized.class.php' );


$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

$sql = "SELECT
        `bm`.`id`,
        `bm`.`date`,
        CASE `bm`.`type`
            WHEN 1 THEN 'All Departments'
            WHEN 2 THEN 'Selected Department'
            WHEN 3 THEN 'Selected Employees'
            ELSE '-'
        END AS `type_label`,
        CASE `bm`.`type`
            WHEN 1 THEN 'All Departments'
            WHEN 2 THEN COALESCE(`d`.`name`, '-')
            WHEN 3 THEN '-'
            ELSE '-'
        END AS `department_name`,
        CASE `bm`.`type`
            WHEN 1 THEN 'All Employees'
            WHEN 2 THEN 'All in Department'
            WHEN 3 THEN CONCAT(
                (LENGTH(`bm`.`employee_ids`) - LENGTH(REPLACE(`bm`.`employee_ids`, ',', '')) + 1),
                ' Employee(s)'
            )
            ELSE '-'
        END AS `employee_label`,
        `bm`.`message`,
        `bm`.`department_id`
    FROM `broadcast_messages` AS `bm`
    LEFT JOIN `departments` AS `d` ON `bm`.`department_id` = `d`.`id`
    WHERE 1=1";

if (!empty($_POST['department']) && $_POST['department'] != 'All') {
    $department = $_POST['department'];
    $sql .= " AND `bm`.`department_id` = '$department'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $sql .= " AND `bm`.`date` BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM (" . $sql . ") as `u`";

$extraWhere = "";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>