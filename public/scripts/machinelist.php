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
$table = 'machines';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`machine`', 'dt' => 'machine', 'field' => 'machine' ),
	array( 'db' => '`b`.`location`', 'dt' => 'branch', 'field' => 'location' ), 
	array( 'db' => '`u`.`full_complete`', 'dt' => 'full_complete', 'field' => 'full_complete' ),
	array( 'db' => '`u`.`semi_complete`', 'dt' => 'semi_complete', 'field' => 'semi_complete' ),
	array( 'db' => '`u`.`target_count`', 'dt' => 'target_count', 'field' => 'target_count' ),
	array( 'db' => '`u`.`description`', 'dt' => 'description', 'field' => 'description' )
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

$joinQuery = "FROM `machines` AS `u` 
              LEFT JOIN `companies` AS `c` ON `u`.`company_id` = `c`.`id`
              LEFT JOIN `branches` AS `b` ON `u`.`branch_id` = `b`.`id`";

$extraWhere = "`u`.`status` != 3";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
