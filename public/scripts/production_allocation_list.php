<?php

// DB table to use
$table = 'emp_product_allocation';

// Table's primary key
$primaryKey = 'id';

    $columns = array(
        array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
        array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
        array('db' => '`u`.`machine`', 'dt' => 'machine', 'field' => 'machine'),
        array('db' => '`u`.`productname`', 'dt' => 'productname', 'field' => 'productname'),
        array('db' => '`u`.`shift_name`', 'dt' => 'shift_name', 'field' => 'shift_name'),
        array('db' => '`u`.`status`', 'dt' => 'leave_status', 'field' => 'status')
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
        `m`.`machine`,
        `p`.`productname`,
        `st`.`shift_name`,
        `epa`.`status`
    FROM `emp_product_allocation` AS `epa`
    LEFT JOIN `machines` AS `m` ON `epa`.`machine_id` = `m`.`id`
    LEFT JOIN `product` AS `p` ON `epa`.`product_id` = `p`.`id`
    LEFT JOIN `shift_types` AS `st` ON `epa`.`shift_id` = `st`.`id`
    WHERE `epa`.`status` IN (1, 2)";


    $joinQuery = "FROM (" . $sql . ") as `u`";
    $extraWhere = "";


    echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
    ?>