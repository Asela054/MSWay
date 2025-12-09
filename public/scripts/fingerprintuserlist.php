<?php
session_start();
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'fingerprint_users';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`userid`', 'dt' => 'userid', 'field' => 'userid' ),
	array( 'db' => '`u`.`name`', 'dt' => 'name', 'field' => 'name' ),
	array( 'db' => '`u`.`cardno`', 'dt' => 'cardno', 'field' => 'cardno' ),
	array( 'db' => '`u`.`role`', 'dt' => 'role', 'field' => 'role' ),
	array( 'db' => '`u`.`password`', 'dt' => 'password', 'field' => 'password' ),
	array( 'db' => '`u`.`devicesno`', 'dt' => 'devicesno', 'field' => 'devicesno' ),
	array( 'db' => '`br`.`location`', 'dt' => 'location', 'field' => 'location' )
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



$joinQuery = "FROM `fingerprint_users` AS `u`
              left join `branches` AS `br` on `br`.`id` = u.location";

$extraWhere = "u.deleted = 0";
	
$extraWhere = "1=1";

// new filter based on user access rights
$pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_username, $db_password);
$userId = UserHelper::getLoggedInUserId($pdo);
$accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $pdo);

if (!empty($accessibleEmployeeIds)) {
    $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
    $extraWhere .= " AND u.userid IN ($empIds)";
} else {
    $extraWhere .= " AND 1 = 0";
}
// end of new filter

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
