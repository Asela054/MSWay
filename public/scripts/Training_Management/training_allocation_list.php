<?php

// DB table to use
$table = 'training_allocations';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`training_name`', 'dt' => 'training_name', 'field' => 'training_name'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`venue`', 'dt' => 'venue', 'field' => 'venue'),
    array('db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status')
);

// SQL server connection information
require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery = "FROM `training_allocations` AS `u`";
$extraWhere = "`u`.`status` != 3";


echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
