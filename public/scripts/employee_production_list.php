<?php

// DB table to use
$table = 'employee_production';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`machine`', 'dt' => 'machine', 'field' => 'machine'),
    array('db' => '`u`.`productname`', 'dt' => 'product', 'field' => 'productname'),
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
        `m`.`machine`,
        `p`.`productname`,
        `ep`.`amount`,
        `ep`.`description`
    FROM `employee_production` AS `ep`
    LEFT JOIN `machines` AS `m` ON `ep`.`machine_id` = `m`.`id`
    LEFT JOIN `product` AS `p` ON `ep`.`product_id` = `p`.`id`
    LEFT JOIN `employees` AS `e` ON `ep`.`emp_id` = `e`.`emp_id`
    WHERE 1=1";

if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $sql .= " AND `ep`.`emp_id` = '$employee'";
}
if (!empty($_POST['machine'])) {
    $machine = $_POST['machine'];
    $sql .= " AND `ep`.`machine_id` = '$machine'";
}
if (!empty($_POST['product'])) {
    $product = $_POST['product'];
    $sql .= " AND `ep`.`product_id` = '$product'";
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