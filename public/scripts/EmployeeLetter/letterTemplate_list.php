<?php

$table      = 'letter_templates';
$primaryKey = 'id';

$columns = array(
    array( 'db' => '`u`.`id`',            'dt' => 'id',            'field' => 'id' ),
    array( 'db' => '`u`.`name`',          'dt' => 'name',          'field' => 'name' ),
    array( 'db' => '`lt`.`letter_type`',  'dt' => 'letter_type',   'field' => 'letter_type' ),
    array( 'db' => '`u`.`is_active`',     'dt' => 'is_active',     'field' => 'is_active' ),
);

require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery  = "FROM `letter_templates` AS `u`
               LEFT JOIN `letter_types` AS `lt` ON `u`.`letter_type_id` = `lt`.`id`";

$extraWhere = "`u`.`is_active` != 3";

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);