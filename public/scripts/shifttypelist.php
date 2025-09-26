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
$table = 'shift_types';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`s`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`s`.`shift_name`', 'dt' => 'shift_name', 'field' => 'shift_name' ),
	array( 'db' => '`s`.`offduty_day`', 'dt' => 'offduty_day', 'field' => 'offduty_day' ),
	array( 'db' => '`s`.`onduty_time`', 'dt' => 'onduty_time', 'field' => 'onduty_time' ),
	array( 'db' => '`s`.`offduty_time`', 'dt' => 'offduty_time', 'field' => 'offduty_time' ),
	array( 'db' => '`s`.`saturday_onduty_time`', 'dt' => 'saturday_onduty_time', 'field' => 'saturday_onduty_time' ),
	array( 'db' => '`s`.`saturday_offduty_time`', 'dt' => 'saturday_offduty_time', 'field' => 'saturday_offduty_time' ),
	array( 'db' => '`s`.`begining_checkin`', 'dt' => 'begining_checkin', 'field' => 'begining_checkin' ),
	array( 'db' => '`s`.`begining_checkout`', 'dt' => 'begining_checkout', 'field' => 'begining_checkout' ),
	array( 'db' => '`s`.`ending_checkin`', 'dt' => 'ending_checkin', 'field' => 'ending_checkin' ),
	array( 'db' => '`s`.`ending_checkout`', 'dt' => 'ending_checkout', 'field' => 'ending_checkout' )
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

$joinQuery = "FROM `shift_types` AS `s`";
	
$extraWhere = "1=1 AND s.deleted=0";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
