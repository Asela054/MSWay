<?php

// DB table to use
$table = 'branches';

// Table's primary key
$primaryKey = 'id';


// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location' ),
	array( 'db' => '`u`.`contactno`', 'dt' => 'contactno', 'field' => 'contactno' ),
	array( 'db' => '`u`.`epf`', 'dt' => 'epf', 'field' => 'epf' ),
	array( 'db' => '`u`.`etf`', 'dt' => 'etf', 'field' => 'etf' ),
	array( 'db' => '`u`.`code`', 'dt' => 'code', 'field' => 'code' ),
	array( 'db' => '`u`.`latitude`', 'dt' => 'latitude', 'field' => 'latitude' ),
	array( 'db' => '`u`.`longitude`', 'dt' => 'longitude', 'field' => 'longitude' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);



// require( 'ssp.class.php' );
require('ssp.customized.class.php' );

$company_id = $_POST['company_id'];

$joinQuery = "FROM `branches` AS `u`";
	
$extraWhere = "1=1 AND (`u`.`company_id` = '$company_id' OR `u`.`company_id` = '0')";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
