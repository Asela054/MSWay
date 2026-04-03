<?php

// DB table to use
$table = 'opma_daily_production_summary';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`target`', 'dt' => 'target', 'field' => 'target'),
    array('db' => '`u`.`produce`', 'dt' => 'produce', 'field' => 'produce'),
    array('db' => '`u`.`difference`', 'dt' => 'difference', 'field' => 'difference'),
    array('db' => '`u`.`bonus`', 'dt' => 'bonus', 'field' => 'bonus'),
    array('db' => '`u`.`damage`', 'dt' => 'damage', 'field' => 'damage')
);

// SQL server connection information
require('../config.php');
require('../ssp.customized.class.php');


$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

$sql = "SELECT 
        `ep`.`id`,
        `ep`.`emp_id`,
        `e`.`emp_name_with_initial`,
        `ep`.`date`,
        `ep`.`target`,
        `ep`.`produce`,
        `ep`.`difference`,
        `ep`.`bonus`,
        `ep`.`damage`
    FROM `opma_daily_production_summary` AS `ep`
    LEFT JOIN `employees` AS `e` ON `ep`.`emp_id` = `e`.`emp_id`
    WHERE 1=1";

if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $sql .= " AND `ep`.`emp_id` = '$employee'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $sql .= " AND `ep`.`date` BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM (" . $sql . ") as `u`";

$extraWhere = "";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>