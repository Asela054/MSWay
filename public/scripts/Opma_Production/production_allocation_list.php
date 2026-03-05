<?php

// DB table to use
$table = 'opma_emp_product_allocation';

// Table's primary key
$primaryKey = 'id';

    $columns = array(
        array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
        array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
        array('db' => '`u`.`target`', 'dt' => 'target', 'field' => 'target'),
        array('db' => '`u`.`scale`', 'dt' => 'scale', 'field' => 'scale'),
        array('db' => '`u`.`machine`', 'dt' => 'machine', 'field' => 'machine'),
        array('db' => '`u`.`title`', 'dt' => 'title', 'field' => 'title'),
        array('db' => '`u`.`shift_name`', 'dt' => 'shift_name', 'field' => 'shift_name'),
        array('db' => '`u`.`size`', 'dt' => 'size', 'field' => 'size'),
        array('db' => '`u`.`status`', 'dt' => 'leave_status', 'field' => 'status')
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
        `epa`.`id`,
        `epa`.`date`,
        `epa`.`target`,
        `epa`.`scale`,
        `m`.`machine`,
        `p`.`title`,
        `st`.`shift_name`,
        `sz`.`size`,
        `epa`.`status`
    FROM `opma_emp_product_allocation` AS `epa`
    LEFT JOIN `opma_machines` AS `m` ON `epa`.`machine_id` = `m`.`id`
    LEFT JOIN `opma_styles` AS `p` ON `epa`.`product_id` = `p`.`id`
    LEFT JOIN `shift_types` AS `st` ON `epa`.`shift_id` = `st`.`id`
    LEFT JOIN `opma_sizes` AS `sz` ON `epa`.`size` = `sz`.`id`
    WHERE `epa`.`status` IN (1, 2)";


    $joinQuery = "FROM (" . $sql . ") as `u`";
    $extraWhere = "";


    echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
    ?>