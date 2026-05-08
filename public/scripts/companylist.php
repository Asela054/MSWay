<?php
session_start();

use App\Helpers\UserHelper;

require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'companies';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
	array( 'db' => '`u`.`name`', 'dt' => 'name', 'field' => 'name' ),
	array( 'db' => '`u`.`code`', 'dt' => 'code', 'field' => 'code' ),
	array( 'db' => '`u`.`logo`', 'dt' => 'logo', 'field' => 'logo' ),
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

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );

// Build base SQL
$sql = "SELECT `c`.* FROM `companies` AS `c` WHERE 1=1";

// Apply user company access filter
$userId = UserHelper::getLoggedInUserId();

if ($userId) {
    $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

    if ($mysqli->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    $companyIds = [];
    $stmt = $mysqli->prepare("SELECT company_id FROM user_has_companies WHERE user_id = ?");

    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $companyIds[] = $row['company_id'];
        }
        $stmt->close();
    }

    // If user has assigned companies, restrict to those; otherwise show all
    if (!empty($companyIds)) {
        $escapedIds = array_map(function($id) use ($mysqli) {
            return "'" . $mysqli->real_escape_string($id) . "'";
        }, $companyIds);
        $sql .= " AND `c`.`id` IN (" . implode(',', $escapedIds) . ")";
    }

    $mysqli->close();
}

$joinQuery = "FROM (" . $sql . ") AS `u`";
$extraWhere = "";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);