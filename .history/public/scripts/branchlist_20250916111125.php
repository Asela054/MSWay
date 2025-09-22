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
$table = 'branches';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location' ),
	array( 'db' => '`u`.`code`', 'dt' => 'code', 'field' => 'code' ),
	array( 'db' => '`u`.`address`', 'dt' => 'address', 'field' => 'address' ),
	array( 'db' => '`u`.`mobile`', 'dt' => 'mobile', 'field' => 'mobile' ),
	array( 'db' => '`u`.`land`', 'dt' => 'land', 'field' => 'land' ),
	array( 'db' => '`u`.`email`', 'dt' => 'email', 'field' => 'email' ),
	array( 'db' => '`u`.`domain_name`', 'dt' => 'domain_name', 'field' => 'domain_name' ),
	array( 'db' => '`u`.`epf`', 'dt' => 'epf', 'field' => 'epf' ),
	array( 'db' => '`u`.`etf`', 'dt' => 'etf', 'field' => 'etf' ),
	array( 'db' => '`u`.`bank_account_name`', 'dt' => 'bank_account_name', 'field' => 'bank_account_name' ),
	array( 'db' => '`u`.`bank_account_number`', 'dt' => 'bank_account_number', 'field' => 'bank_account_number' ),
	array( 'db' => '`u`.`bank_account_branch_code`', 'dt' => 'bank_account_branch_code', 'field' => 'bank_account_branch_code' ),
	array( 'db' => '`u`.`employer_number`', 'dt' => 'employer_number', 'field' => 'employer_number' ),
	array( 'db' => '`u`.`zone_code`', 'dt' => 'zone_code', 'field' => 'zone_code' ),
	array( 'db' => '`u`.`ref_no`', 'dt' => 'ref_no', 'field' => 'ref_no' ),
	array( 'db' => '`u`.`vat_reg_no`', 'dt' => 'vat_reg_no', 'field' => 'vat_reg_no' ),
	array( 'db' => '`u`.`svat_no`', 'dt' => 'svat_no', 'field' => 'svat_no' )
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

$joinQuery = "FROM `branches` AS `u`";
	
$extraWhere = "1=1";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
