<?php

// DB table to use
$table = 'training_allocations';

// Table's primary key
$primaryKey = 'id';

    $columns = array(
        array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
        array('db' => '`u`.`venue`', 'dt' => 'venue', 'field' => 'venue'),
        array('db' => '`u`.`end_time`', 'dt' => 'end_time', 'field' => 'end_time'),
        array('db' => '`u`.`start_time`', 'dt' => 'start_time', 'field' => 'start_time'),
        array('db' => '`u`.`training_type`', 'dt' => 'training_type', 'field' => 'training_type'),
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
        `epa`.`venue`,
        `epa`.`start_time`,
        `epa`.`end_time`,
        `p`.`name` AS `training_type`,
        `epa`.`status`
    FROM `training_allocations` AS `epa`
    LEFT JOIN `training_types` AS `p` ON `epa`.`type_id` = `p`.`id`
    WHERE `epa`.`status` IN (1, 2)";


    $joinQuery = "FROM (" . $sql . ") as `u`";
    $extraWhere = "";


    echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
    ?>