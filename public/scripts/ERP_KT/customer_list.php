<?php

$table = 'kt_customer';
$primaryKey = 'id';

$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`name`', 'dt' => 'name', 'field' => 'name' ),
	array( 'db' => '`u`.`contact_number`', 'dt' => 'contact_number', 'field' => 'contact_number' ),
	array( 'db' => '`u`.`email`', 'dt' => 'email', 'field' => 'email' ),
	array( 'db' => '`u`.`remarks`', 'dt' => 'remarks', 'field' => 'remarks' )
);

require('../config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('../ssp.customized.class.php' );

$joinQuery = "FROM `kt_customer` AS `u`";

$extraWhere = "";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
