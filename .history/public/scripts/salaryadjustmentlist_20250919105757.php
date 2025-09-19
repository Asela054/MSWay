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
$table = 'salary_adjustments';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`ua`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial' ),
    array( 'db' => '`ub`.`category`', 'dt' => 'category', 'field' => 'category' ),
	array( 'db' => '`ub`.`remuneration_name`', 'dt' => 'remuneration_name', 'field' => 'remuneration_name' ),
	array( 'db' => '`u`.`day_count`', 'dt' => 'day_count', 'field' => 'day_count' ),
	array( 'db' => '`u`.`amount`', 'dt' => 'amount', 'field' => 'amount' )
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

$joinQuery = "FROM `salary_adjustments` AS `u` 
LEFT JOIN `job_categories` AS `ua` on `ua`.`id` = u.job_id
LEFT JOIN `remunerations` AS `ub` on `ub`.`id` = u.remuneration_id";
	
$extraWhere = "1=1";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
