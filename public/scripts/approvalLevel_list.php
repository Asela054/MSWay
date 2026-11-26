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
$table = 'leave_approval_hierarchy';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`',              'dt' => 'id',            'field' => 'id' ),
	array( 'db' => '`b`.`title`',           'dt' => 'title',         'field' => 'title' ), 
	array( 'db' => '`c`.`name`',            'dt' => 'name',          'field' => 'name' ), 
	array( 'db' => '`u`.`approval_level`',  'dt' => 'approval_level','field' => 'approval_level' ),
	array( 'db' => '`u`.`department_id`',   'dt' => 'department_id', 'field' => 'department_id' ),
	array( 'db' => '`u`.`created_at`',      'dt' => 'created_at',    'field' => 'created_at' )
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

$joinQuery = "FROM `leave_approval_hierarchy` AS `u` 
              LEFT JOIN `departments` AS `c` ON `u`.`department_id` = `c`.`id`
              LEFT JOIN `job_titles` AS `b` ON `u`.`title_id` = `b`.`id`";

$extraWhere = "";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
