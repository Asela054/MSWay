<?php

// DB table to use
$table = 'emp_task_allocation';

// Table's primary key
$primaryKey = 'id';

    $columns = array(
        array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
        array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
        array('db' => '`u`.`taskname`', 'dt' => 'taskname', 'field' => 'taskname'),
        array('db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status')
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
        `epa`.`id`,
        `epa`.`date`,
        `p`.`taskname`,
        `epa`.`status`
    FROM `emp_task_allocation` AS `epa`
    LEFT JOIN `task` AS `p` ON `epa`.`task_id` = `p`.`id`
    WHERE `epa`.`status` IN (1, 2)";


    $joinQuery = "FROM (" . $sql . ") as `u`";
    $extraWhere = "";


    echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
    ?>