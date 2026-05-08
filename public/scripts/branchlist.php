<?php
session_start();

use App\Helpers\UserHelper;

require_once __DIR__ . '/../../app/Helpers/UserHelper.php';


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

// Apply user branch access filter
$userId = UserHelper::getLoggedInUserId();

if ($userId) {
    $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

    if ($mysqli->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    $sql = "SELECT `b`.* FROM `branches` AS `b` 
            WHERE (`b`.`company_id` = '" . $mysqli->real_escape_string($company_id) . "' OR `b`.`company_id` = '0')";

    $branchIds = [];
    $stmt = $mysqli->prepare("SELECT branch_id FROM user_has_companies WHERE user_id = ?");

    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $branchIds[] = intval($row['branch_id']);
        }
        $stmt->close();
    }

    if (!empty($branchIds)) {
        $branchIdsList = implode(',', $branchIds);
        $sql .= " AND `b`.`id` IN ($branchIdsList)";
    }

    $mysqli->close();
} else {
    // No logged-in user — show all branches for the company unfiltered
    $sql = "SELECT `b`.* FROM `branches` AS `b` 
            WHERE (`b`.`company_id` = '" . intval($company_id) . "' OR `b`.`company_id` = '0')";
}

$joinQuery = "FROM (" . $sql . ") AS `u`";
$extraWhere = "";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
