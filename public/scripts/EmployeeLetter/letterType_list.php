<?php

$table      = 'letter_types';
$primaryKey = 'id';

$columns = array(
    array( 'db' => '`u`.`id`',          'dt' => 'id',          'field' => 'id' ),
    array( 'db' => '`u`.`letter_type`', 'dt' => 'letter_type', 'field' => 'letter_type' ),
    array( 'db' => '`u`.`remarks`',     'dt' => 'remarks',     'field' => 'remarks' ),
);

require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery  = "FROM `letter_types` AS `u`";
$extraWhere = "`u`.`status` != 3";

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);