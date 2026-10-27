<?php
session_start();

// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;


// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'iou_requests';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array( 'db' => 'u.id', 'dt' => 'id', 'field' => 'id' ),
    array( 'db' => 'ua.emp_name_with_initial', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial' ),
    array( 'db' => 'ua.emp_id', 'dt' => 'emp_id', 'field' => 'emp_id' ),
    array( 'db' => 'ub.reason', 'dt' => 'reason', 'field' => 'reason' ),
    array( 'db' => 'u.amount', 'dt' => 'amount', 'field' => 'amount' ),
    array( 'db' => 'u.remark', 'dt' => 'remark', 'field' => 'remark' ),
    array( 'db' => 'ua.calling_name', 'dt' => 'calling_name', 'field' => 'calling_name' ),
    array( 'db' => 'u.emp_id', 'dt' => 'employee_display', 'field' => 'emp_id', 
          'formatter' => function($d, $row) {
              $employee = (object)[
                  'emp_name_with_initial' => $row['emp_name_with_initial'],
                  'calling_name' => $row['calling_name'],
                  'emp_id' => $row['emp_id']
              ];
              
              return EmployeeHelper::getDisplayName($employee);
          }
    )
);

// SQL server connection information
require('../config.php');

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$userId = UserHelper::getLoggedInUserId();

$employeeFilter = "";

if ($userId) {
    $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

    if ($mysqli->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    $companyIds = [];
    $branchIds = [];
    $companyQuery = "SELECT company_id, branch_id FROM user_has_companies WHERE user_id = ?";
    $stmt = $mysqli->prepare($companyQuery);

    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            if (!empty($row['company_id'])) {
            $companyIds[] = $row['company_id'];
            }
            if (!empty($row['branch_id'])) {
                $branchIds[] = $row['branch_id'];
            }
        }
        $stmt->close();
    }

    if (!empty($companyIds)) {
        $escapedCompanyIds = array_map(function($id) use ($mysqli) {
            return "'" . $mysqli->real_escape_string($id) . "'";
        }, $companyIds);
        $companyIdsList = implode(',', $escapedCompanyIds);
        $employeeFilter .= " AND `ua`.`emp_company` IN ($companyIdsList)";
    }

    if (!empty($branchIds)) {
        $branchIdsList = implode(',', array_map('intval', $branchIds));
        $employeeFilter .= " AND `ua`.`emp_location` IN ($branchIdsList)";
    }

    $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $mysqli);

    if (!empty($accessibleEmployeeIds)) {
        $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
        $employeeFilter .= " AND `ua`.`emp_id` IN ($empIds)";
    } else {
        $employeeFilter .= " AND 1 = 0";
    }

    $mysqli->close();
}

$joinQuery = "FROM `iou_requests` AS `u` 
LEFT JOIN `employees` AS `ua` ON `ua`.`emp_id` = `u`.`emp_id`
LEFT JOIN `iou_reasons` AS `ub` ON `ub`.`id` = `u`.`reason_id`
";


$extraWhere = "`u`.`status` != 3 AND `u`.`emp_id` != 0 AND `ua`.`is_resigned` = 0 $employeeFilter";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);