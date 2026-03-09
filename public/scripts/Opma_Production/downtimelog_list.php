<?php

// DB table to use
$table = 'opma_machine_downtime';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`machine`', 'dt' => 'machine', 'field' => 'machine'),
    array('db' => '`u`.`type`', 'dt' => 'type', 'field' => 'type'),
    array('db' => '`u`.`fromtime`', 'dt' => 'fromtime', 'field' => 'fromtime'),
    array('db' => '`u`.`totime`', 'dt' => 'totime', 'field' => 'totime')
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
        `ep`.`date`,
        `m`.`machine`,
        `p`.`type`,
        `ep`.`fromtime`,
        `ep`.`totime`
    FROM `opma_machine_downtime` AS `ep`
    LEFT JOIN `opma_machines` AS `m` ON `ep`.`machine_id` = `m`.`id`
    LEFT JOIN `opma_timechanging_type` AS `p` ON `ep`.`type_id` = `p`.`id`
    WHERE 1=1";

if (!empty($_POST['machine'])) {
    $machine = $_POST['machine'];
    $sql .= " AND `ep`.`machine_id` = '$machine'";
}
if (!empty($_POST['product'])) {
    $product = $_POST['product'];
    $sql .= " AND `ep`.`type_id` = '$product'";
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