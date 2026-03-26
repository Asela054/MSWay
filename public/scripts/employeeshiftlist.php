<?php

$table = 'employeeshifts';

$primaryKey = 'id';


$columns = array(
    array( 'db' => '`s`.`id`', 'dt' => 'id', 'field' => 'id' ),
    array( 'db' => '`st`.`shift_name`', 'dt' => 'shift_name', 'field' => 'shift_name' ),
    array( 'db' => '`s`.`date_from`', 'dt' => 'date_from', 'field' => 'date_from' ),
    array( 'db' => '`s`.`date_to`', 'dt' => 'date_to', 'field' => 'date_to' ),
    array( 'db' => '`s`.`status`', 'dt' => 'status', 'field' => 'status' )
);

$joinQuery = "FROM `employeeshifts` AS `s` LEFT JOIN `shift_types` AS `st` ON `st`.`id` = `s`.`shift_id`";

$extraWhere = "`s`.`status` IN (1,2)";

require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);


// require( 'ssp.class.php' );
require('ssp.customized.class.php' );

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
