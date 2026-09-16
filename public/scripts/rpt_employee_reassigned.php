<?php
session_start();
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'employees';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`',                   'dt' => 'id',                   'field' => 'id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial','field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`',          'dt' => 'calling_name',         'field' => 'calling_name'),
    array('db' => '`u`.`emp_id`',                'dt' => 'emp_id',               'field' => 'emp_id'),
    array('db' => '`u`.`emp_national_id`',       'dt' => 'emp_national_id',      'field' => 'emp_national_id'),
    array('db' => '`u`.`location`',              'dt' => 'location',             'field' => 'location'),
    array('db' => '`u`.`dept_name`',             'dt' => 'dept_name',            'field' => 'dept_name'),
    array('db' => '`u`.`category`',              'dt' => 'category',             'field' => 'category'),
    array('db' => '`u`.`emp_join_date`',         'dt' => 'emp_join_date',        'field' => 'emp_join_date'),
    array('db' => '`u`.`resignation_date`',      'dt' => 'resignation_date',     'field' => 'resignation_date'),
    array('db' => '`u`.`emp_id`',                'dt' => 'employee_display',     'field' => 'emp_id',
        'formatter' => function($d, $row) {
            $employee = (object)[
                'emp_name_with_initial' => $row['emp_name_with_initial'],
                'calling_name'          => $row['calling_name'],
                'emp_id'                => $row['emp_id']
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

// Build the main query.
// Only return employees whose emp_national_id appears more than once in the
// employees table (i.e. they resigned and rejoined under a different emp_id).
$sql = "SELECT
    `e`.`id`,
    `e`.`emp_name_with_initial`,
    `e`.`calling_name`,
    `e`.`emp_id`,
    `e`.`emp_national_id`,
    `b`.`location`,
    `d`.`name`  as `dept_name`,
    `jt`.`category`,
    `e`.`emp_join_date`,
    `e`.`resignation_date`
FROM `employees` as `e`
LEFT JOIN `job_categories` as `jt`        ON `e`.`job_category_id`  = `jt`.`id`
LEFT JOIN `branches` as `b`           ON `e`.`emp_location`  = `b`.`id`
LEFT JOIN `departments` as `d`        ON `e`.`emp_department` = `d`.`id`
WHERE `e`.`deleted` = 0
  AND `e`.`emp_national_id` IN (
      SELECT `emp_national_id`
      FROM   `employees`
      WHERE  `deleted` = 0
        AND  `emp_national_id` IS NOT NULL
        AND  `emp_national_id` != ''
      GROUP  BY `emp_national_id`
      HAVING COUNT(*) > 1
  )";

// Open connection for filtering
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Add department filter (escaped to prevent SQL injection)
if (!empty($_REQUEST['department']) && $_REQUEST['department'] != 'All') {
    $department = $mysqli->real_escape_string($_REQUEST['department']);
    $sql .= " AND `e`.`emp_department` = '$department'";
}

// Add company filter (escaped to prevent SQL injection)
if (!empty($_REQUEST['company']) && $_REQUEST['company'] != 'All') {
    $company = $mysqli->real_escape_string($_REQUEST['company']);
    $sql .= " AND `e`.`emp_company` = '$company'";
}

// Add user access rights filter
$userId = UserHelper::getLoggedInUserId();

if ($userId) {
    // Get company IDs and branch IDs from user_has_companies
    $companyIds = [];
    $branchIds  = [];
    $companyQuery = "SELECT company_id, branch_id FROM user_has_companies WHERE user_id = ?";
    $stmt = $mysqli->prepare($companyQuery);

    if ($stmt) {
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $companyIds[] = $row['company_id'];
            $branchIds[]  = $row['branch_id'];
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
}

$mysqli->close();

$joinQuery  = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>