<?php
session_start();

use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

require_once __DIR__ . '/../../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'attendance_unmatch';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`id`', 'dt' => 'at_id', 'field' => 'id'),
    array('db' => '`u`.`uid`', 'dt' => 'uid', 'field' => 'uid'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`dep_name`', 'dt' => 'dep_name', 'field' => 'dep_name'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`date`', 'dt' => 'date_row', 'field' => 'date'),
    array('db' => '`u`.`shift_name`', 'dt' => 'shift_name', 'field' => 'shift_name'),
    array('db' => '`u`.`on_timestamp`', 'dt' => 'on_timestamp', 'field' => 'on_timestamp'),
    array('db' => '`u`.`off_timestamp`', 'dt' => 'off_timestamp', 'field' => 'off_timestamp'),
    array('db' => '`u`.`roster_shift_id`', 'dt' => 'roster_shift_id', 'field' => 'roster_shift_id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'employee_display', 'field' => 'emp_id',
        'formatter' => function ($d, $row) {
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

// Filter values escape karanna connection eka kalinma hadanawa
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

$sql = "SELECT
        `at1`.`id`,
        `at1`.`emp_id` AS `uid`,
        `employees`.`emp_id`,
        `employees`.`emp_name_with_initial`,
        `employees`.`calling_name`,
        `employees`.`emp_company`,
        `employees`.`emp_location`,
        `branches`.`location`,
        `departments`.`name` AS `dep_name`,
        `shift_types`.`shift_name`,
        `at1`.`date`,
        `at1`.`on_timestamp`,
        `at1`.`off_timestamp`,
        `at1`.`roster_shift_id`
    FROM `attendance_unmatch` AS `at1`
    JOIN `employees` ON `at1`.`emp_id` = `employees`.`emp_id`
    LEFT JOIN `branches` ON `branches`.`id` = `employees`.`emp_location`
    LEFT JOIN `departments` ON `departments`.`id` = `employees`.`emp_department`
    LEFT JOIN `shift_types` ON `shift_types`.`id` = `at1`.`roster_shift_id`
    WHERE `at1`.`status` = '1' AND `employees`.`deleted` = 0";

if (!empty($_REQUEST['company'])) {
    $company = $mysqli->real_escape_string($_REQUEST['company']);
    $sql .= " AND `employees`.`emp_company` = '$company'";
}

if (!empty($_POST['department'])) {
    $department = $mysqli->real_escape_string($_POST['department']);
    $sql .= " AND `departments`.`id` = '$department'";
}

if (!empty($_POST['employee'])) {
    $employee = $mysqli->real_escape_string($_POST['employee']);
    $sql .= " AND `employees`.`emp_id` = '$employee'";
}

if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $mysqli->real_escape_string($_POST['from_date']);
    $to_date = $mysqli->real_escape_string($_POST['to_date']);
    $sql .= " AND `at1`.`date` BETWEEN '$from_date' AND '$to_date'";
}

// Filter based on user access rights
$userId = UserHelper::getLoggedInUserId();

if ($userId) {
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

    $quote = function ($id) use ($mysqli) {
        return "'" . $mysqli->real_escape_string($id) . "'";
    };

    if (!empty($companyIds)) {
        $companyIdsList = implode(',', array_map($quote, $companyIds));
        $sql .= " AND `employees`.`emp_company` IN ($companyIdsList)";
    }

    if (!empty($branchIds)) {
        $branchIdsList = implode(',', array_map($quote, $branchIds));
        $sql .= " AND `employees`.`emp_location` IN ($branchIdsList)";
    }

    $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $mysqli);

    if (!empty($accessibleEmployeeIds)) {
        $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
        $sql .= " AND `employees`.`emp_id` IN ($empIds)";
    } else {
        $sql .= " AND 1 = 0";
    }
}

$mysqli->close();

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>