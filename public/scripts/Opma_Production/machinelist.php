<?php

// DB table to use
$table = 'opma_machines';

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
	array( 'db' => '`u`.`description`', 'dt' => 'description', 'field' => 'description' )
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

$joinQuery = "FROM `opma_machines` AS `u` 
              LEFT JOIN `companies` AS `c` ON `u`.`company_id` = `c`.`id`
              LEFT JOIN `branches` AS `b` ON `u`.`branch_id` = `b`.`id`";

$extraWhere = "`u`.`status` != 3";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
