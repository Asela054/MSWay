<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'remunerations';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`remuneration_name`', 'dt' => 'remuneration_name', 'field' => 'remuneration_name' ),
	array( 'db' => '`u`.`remuneration_type`', 'dt' => 'remuneration_type', 'field' => 'remuneration_type' ),
	array( 'db' => '`u`.`epf_payable`', 'dt' => 'epf_payable', 'field' => 'epf_payable' ),
	array( 'db' => '`u`.`ot_applicable`', 'dt' => 'ot_applicable', 'field' => 'ot_applicable' ),
	array( 'db' => '`u`.`nopay_applicable`', 'dt' => 'nopay_applicable', 'field' => 'nopay_applicable' ),
	array( 'db' => '`u`.`advanced_option_id`', 'dt' => 'advanced_option_id', 'field' => 'advanced_option_id' )
);

// SQL server connection information
require('config.php');
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
require('ssp.customized.class.php' );

$joinQuery = "FROM `remunerations` AS `u`";
	
$extraWhere = "`u`.`remuneration_cancel`=0 AND `u`.`allocation_method`='FIXED'";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
