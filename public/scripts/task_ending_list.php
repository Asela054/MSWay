<?php

// DB table to use
$table = 'emp_task_allocation';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`taskname`', 'dt' => 'taskname', 'field' => 'taskname'),
    array('db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status'),
    array('db' => '`u`.`task_status`', 'dt' => 'task_status', 'field' => 'task_status')
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
        `eta`.*,
        `t`.`taskname`
    FROM `emp_task_allocation` AS `eta`
    LEFT JOIN `task` AS `t` ON `eta`.`task_id` = `t`.`id`
    WHERE `eta`.`status` IN (1, 2)";

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>