<?php

// DB table to use
$table = 'opma_sizes';

// Table's primary key
$primaryKey = 'id';


$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`size`', 'dt' => 'size', 'field' => 'size' ),
	array( 'db' => '`u`.`remark`', 'dt' => 'remark', 'field' => 'remark' )
);


// SQL server connection information
require('../config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);


// require( 'ssp.class.php' );
require('../ssp.customized.class.php' );

$joinQuery = "FROM `opma_sizes` AS `u`";

$extraWhere = "1=1";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
