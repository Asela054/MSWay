<?php
session_start();
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'employee_assigned_devices';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`employee_id`', 'dt' => 'employee_id', 'field' => 'employee_id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`dept_name`', 'dt' => 'dept_name', 'field' => 'dept_name'),
    array('db' => '`u`.`device_name`', 'dt' => 'device_type', 'field' => 'device_name',
        'formatter' => function($d, $row) {
            return $d ?: $row['device_type_raw'];
        }
    ),
    array('db' => '`u`.`device_type_raw`', 'dt' => 'device_type_raw', 'field' => 'device_type_raw'),
    array('db' => '`u`.`model_number`', 'dt' => 'model_number', 'field' => 'model_number'),
    array('db' => '`u`.`serial_number`', 'dt' => 'serial_number', 'field' => 'serial_number'),
    array('db' => '`u`.`other_ref_number`', 'dt' => 'other_ref_number', 'field' => 'other_ref_number'),
    array('db' => '`u`.`assigned_date`', 'dt' => 'assigned_date', 'field' => 'assigned_date'),
    array('db' => '`u`.`returned_date`', 'dt' => 'returned_date', 'field' => 'returned_date'),
    array('db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status',
        'formatter' => function($d, $row) {
            return $d == 1 ? 'In Use' : ($d == 2 ? 'Returned' : $d);
        }
    ),
    array('db' => '`u`.`emp_id`', 'dt' => 'employee_display', 'field' => 'emp_id', 
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
require('config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php');

$sql = "SELECT 
    `u`.`id`,
    `e`.`emp_name_with_initial`,
    `e`.`calling_name`,
    `e`.`emp_id` AS `employee_id`,
    `u`.`emp_id`,
    `u`.`device_type` AS `device_type_raw`,
    `d`.`device_name`,
    `dept`.`name` AS `dept_name`,
    `u`.`model_number`,
    `u`.`serial_number`,
    `u`.`other_ref_number`,
    `u`.`assigned_date`,
    `u`.`returned_date`,
    `u`.`status`
FROM `employee_assigned_devices` as `u`
LEFT JOIN `employees` as `e` ON `u`.`emp_id` = `e`.`id`
LEFT JOIN `departments` as `dept` ON `e`.`emp_department` = `dept`.`id`
LEFT JOIN `assigned_devices` as `d` ON `u`.`device_type` = `d`.`id`";

// Add company filter
if (!empty($_REQUEST['company']) && $_REQUEST['company'] != 'All') {
    $company = $_REQUEST['company'];
    $sql .= " WHERE `e`.`emp_company` = '$company'";
}

// Add department filter
if (!empty($_REQUEST['department']) && $_REQUEST['department'] != 'All') {
    $department = $_REQUEST['department'];
    $sql .= " AND `e`.`emp_department` = '$department'";
}

// Add user access rights filter directly to the main query
$userId = UserHelper::getLoggedInUserId();

if ($userId) {
    $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
    
    if ($mysqli->connect_error) {
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }

    // Get company IDs and branch IDs from user_has_companies
    $companyIds = [];
    $branchIds = [];
    $companyQuery = "SELECT company_id, branch_id FROM user_has_companies WHERE user_id = ?";
    $stmt = $mysqli->prepare($companyQuery);

    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $companyIds[] = $row['company_id'];
            $branchIds[] = $row['branch_id'];
        }
        $stmt->close();
    }

    // Apply company filter with proper escaping for VARCHAR values
    if (!empty($companyIds)) {
        $escapedCompanyIds = array_map(function($id) use ($mysqli) {
            return "'" . $mysqli->real_escape_string($id) . "'";
        }, $companyIds);

        $companyIdsList = implode(',', $escapedCompanyIds);
        $sql .= " AND `e`.`emp_company` IN ($companyIdsList)";
    }

    // Apply branch filter
    if (!empty($branchIds)) {
        $branchIdsList = implode(',', array_map('intval', $branchIds));
        $sql .= " AND `e`.`emp_location` IN ($branchIdsList)";
    }

    $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $mysqli);

    if (!empty($accessibleEmployeeIds)) {
        $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
        $sql .= " AND `e`.`emp_id` IN ($empIds)";
    } else {
        $sql .= " AND 1 = 0";
    }

    $mysqli->close();
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";



echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>