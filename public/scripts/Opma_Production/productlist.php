<?php

// DB table to use
$table = 'opma_styles';

// Table's primary key
$primaryKey = 'id';


$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`title`', 'dt' => 'title', 'field' => 'title' ),
	array( 'db' => '`u`.`code`', 'dt' => 'code', 'field' => 'code' ),
	array( 'db' => '`u`.`from_date`', 'dt' => 'from_date', 'field' => 'from_date' ),
	array( 'db' => '`u`.`to_date`', 'dt' => 'to_date', 'field' => 'to_date' ),
);

// SQL server connection information
require('../config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('../ssp.customized.class.php' );

$joinQuery = "FROM `opma_styles` AS `u`";

$extraWhere = "1=1";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
