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
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`dept_name`', 'dt' => 'dept_name', 'field' => 'dept_name'),
    array('db' => '`u`.`emp_birthday`', 'dt' => 'emp_birthday', 'field' => 'emp_birthday'),
    array('db' => '`u`.`emp_mobile`', 'dt' => 'emp_mobile', 'field' => 'emp_mobile'),
    array('db' => '`u`.`emp_work_telephone`', 'dt' => 'emp_work_telephone', 'field' => 'emp_work_telephone'),
    array('db' => '`u`.`emp_national_id`', 'dt' => 'emp_national_id', 'field' => 'emp_national_id'),
    array('db' => '`u`.`emp_gender`', 'dt' => 'emp_gender', 'field' => 'emp_gender'),
    array('db' => '`u`.`emp_email`', 'dt' => 'emp_email', 'field' => 'emp_email'),
    array('db' => '`u`.`emp_address`', 'dt' => 'emp_address', 'field' => 'emp_address'),
    array('db' => '`u`.`emp_address_2`', 'dt' => 'emp_address_2', 'field' => 'emp_address_2'),
    array('db' => '`u`.`emp_addressT1`', 'dt' => 'emp_addressT', 'field' => 'emp_addressT1'),
    array('db' => '`u`.`e_status`', 'dt' => 'e_status', 'field' => 'e_status'),
    array('db' => '`u`.`title`', 'dt' => 'title', 'field' => 'title'),
    array('db' => '`u`.`emp_permanent_date`', 'dt' => 'emp_permanent_date', 'field' => 'emp_permanent_date'),
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
    `e`.`id`,
    `e`.`emp_name_with_initial`,
    `e`.`calling_name`,
    `e`.`emp_id`,
    `b`.`location`,
    `d`.`name` as `dept_name`,
    `e`.`emp_birthday`,
    `e`.`emp_mobile`,
    `e`.`emp_work_telephone`,
    `e`.`emp_national_id`,
    `e`.`emp_gender`,
    `e`.`emp_email`,
    `e`.`emp_address`,
    `e`.`emp_address_2`,
    `e`.`emp_addressT1`,
    `e`.`emp_address_T2`,
    `es`.`emp_status` as `e_status`,
    `jt`.`title`,
    `e`.`emp_permanent_date`
FROM `employees` as `e`
JOIN `job_titles` as `jt` ON `e`.`emp_job_code` = `jt`.`id`
LEFT JOIN `branches` as `b` ON `e`.`emp_location` = `b`.`id`
LEFT JOIN `departments` as `d` ON `e`.`emp_department` = `d`.`id`
LEFT JOIN `employment_statuses` as `es` ON `es`.`id` = `e`.`emp_status`
WHERE `e`.`deleted` = 0 AND `e`.`is_resigned` = 0";

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