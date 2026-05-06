<?php

$table = 'kt_machines';
$primaryKey = 'id';

$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`machine_name`', 'dt' => 'machine_name', 'field' => 'machine_name' ),
	array( 'db' => '`u`.`machine_type`', 'dt' => 'machine_type', 'field' => 'machine_type' ),
	array( 'db' => '`u`.`helper_rate`', 'dt' => 'helper_rate', 'field' => 'helper_rate' ),
    array( 'db' => '`u`.`operator_rate`', 'dt' => 'operator_rate', 'field' => 'operator_rate' ),
	array( 'db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status' ),
	array( 'db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date' ),
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

$joinQuery = "FROM `kt_machines` AS `u`";

$extraWhere = "";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
