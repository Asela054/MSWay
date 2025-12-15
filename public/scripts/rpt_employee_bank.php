<?php
session_start();
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'employee_banks';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`bank_ac_no`', 'dt' => 'bank_ac_no', 'field' => 'bank_ac_no'),
    array('db' => '`u`.`bank`', 'dt' => 'bank', 'field' => 'bank'),
    array('db' => '`u`.`bank_code`', 'dt' => 'bank_code', 'field' => 'bank_code'),
    array('db' => '`u`.`branch`', 'dt' => 'branch', 'field' => 'branch'),
    array('db' => '`u`.`branch_code`', 'dt' => 'branch_code', 'field' => 'branch_code'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`dept_name`', 'dt' => 'dept_name', 'field' => 'dept_name'),
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
    `eb`.`id`,
    `eb`.`bank_ac_no`,
    `b`.`bank`,
    `b`.`code` as `bank_code`,
    `bb`.`branch`,
    `bb`.`code` as `branch_code`,
    `employees`.`emp_id`,
    `employees`.`emp_name_with_initial`,
    `employees`.`calling_name`,
    `branches`.`location`,
    `departments`.`name` as `dept_name`
FROM `employee_banks` as `eb`
LEFT JOIN `bank_branches` as `bb` ON (`bb`.`code` = `eb`.`branch_code` AND `bb`.`bankcode` = `eb`.`bank_code`)
LEFT JOIN `banks` as `b` ON `b`.`code` = `bb`.`bankcode`
LEFT JOIN `employees` ON `employees`.`id` = `eb`.`emp_id`
LEFT JOIN `employment_statuses` ON `employees`.`emp_status` = `employment_statuses`.`id`
LEFT JOIN `job_titles` ON `employees`.`emp_job_code` = `job_titles`.`id`
LEFT JOIN `branches` ON `employees`.`emp_location` = `branches`.`id`
LEFT JOIN `departments` ON `employees`.`emp_department` = `departments`.`id`
WHERE 1=1";

// Add department filter
if (!empty($_REQUEST['department'])) {
    $department = $_REQUEST['department'];
    $sql .= " AND `employees`.`emp_department` = '$department'";
}

// Add banks filter
if (!empty($_REQUEST['banks'])) {
    $banks = $_REQUEST['banks'];
    $sql .= " AND `eb`.`bank_code` = '$banks'";
}

// Add user access rights filter directly to the main query
    $pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_username, $db_password);
    $userId = UserHelper::getLoggedInUserId($pdo);
    $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $pdo);
if (!empty($accessibleEmployeeIds)) {
    $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
    $sql .= " AND `employees`.`emp_id` IN ($empIds)";
} else {
    $sql .= " AND 1 = 0";
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>