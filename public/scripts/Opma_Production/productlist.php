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
	array( 'db' => '`u`.`request_qty_edited`', 'dt' => 'request_qty_edited', 'field' => 'request_qty_edited' ),
	array( 'db' => '`u`.`currency_type`', 'dt' => 'currency_type', 'field' => 'currency_type' ),
	array( 'db' => '`c`.`currency`', 'dt' => 'currency_name', 'field' => 'currency_name', 'as' => 'currency_name'),
	array( 'db' => '`u`.`unit_price`', 'dt' => 'unit_price', 'field' => 'unit_price' ),
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

$joinQuery = "FROM `opma_styles` AS `u` LEFT JOIN `opma_currency_types` AS `c` ON `u`.`currency_type` = `c`.`id`";

$extraWhere = "`status` = 1";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
