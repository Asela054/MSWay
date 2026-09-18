<?php
session_start();

use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

require('config.php');
require('ssp.customized.class.php');

$table = 'employees';
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_national_id`', 'dt' => 'emp_national_id', 'field' => 'emp_national_id'),
    array('db' => '`u`.`emp_etfno`', 'dt' => 'emp_etfno', 'field' => 'emp_etfno'),
    array('db' => '`u`.`department_name`', 'dt' => 'department', 'field' => 'department_name'),
    array('db' => '`u`.`company_name`', 'dt' => 'company', 'field' => 'company_name'),
    array('db' => '`u`.`shift_name`', 'dt' => 'shift', 'field' => 'shift_name'),
    array('db' => '`u`.`emp_join_date`', 'dt' => 'emp_join_date', 'field' => 'emp_join_date'),
    array('db' => '`u`.`title`', 'dt' => 'title', 'field' => 'title'),
    array('db' => '`u`.`category`', 'dt' => 'category', 'field' => 'category'),
    array('db' => '`u`.`emp_status`', 'dt' => 'emp_status', 'field' => 'emp_status'),
    array('db' => '`u`.`location`', 'dt' => 'location', 'field' => 'location'),
    array('db' => '`u`.`is_resigned`', 'dt' => 'is_resigned', 'field' => 'is_resigned'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`u`.`emp_company`', 'dt' => 'emp_company', 'field' => 'emp_company'),
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

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

$current_date_time = date('Y-m-d H:i:s');
$previous_month_date = date('Y-m-d', strtotime('-1 month'));

$sql = "SELECT
    `employees`.`id`,
    `employees`.`emp_id`,
    `employees`.`emp_national_id`,
    `employees`.`emp_etfno`,
    `departments`.`name` as `department_name`,
    `companies`.`name` as `company_name`,
    `shift_types`.`shift_name`,
    `employees`.`emp_join_date`,
    `job_titles`.`title`,
    `job_categories`.`category`,
    `employment_statuses`.`emp_status`,
    `branches`.`location`,
    `employees`.`is_resigned`,
    `employees`.`emp_name_with_initial`,
    `employees`.`calling_name`,
    `employees`.`emp_company`
FROM `employees`
LEFT JOIN `employment_statuses` ON `employees`.`emp_status` = `employment_statuses`.`id`
LEFT JOIN `companies` ON `employees`.`emp_company` = `companies`.`id`
LEFT JOIN `branches` ON `employees`.`emp_location` = `branches`.`id`
LEFT JOIN `departments` ON `employees`.`emp_department` = `departments`.`id`
LEFT JOIN `job_titles` ON `employees`.`emp_job_code` = `job_titles`.`id`
LEFT JOIN `job_categories` ON `employees`.`job_category_id` = `job_categories`.`id`
LEFT JOIN `shift_types` ON `employees`.`emp_shift` = `shift_types`.`id`
WHERE `employees`.`deleted` = 0";

// new filter based on user access rights
$userId = UserHelper::getLoggedInUserId();

    if ($userId) {
        $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

        if ($mysqli->connect_error) {
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }

         // Get company IDs - considering they might be VARCHAR values
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
            // Escape each company ID and wrap in quotes
            $escapedCompanyIds = array_map(function($id) use ($mysqli) {
                return "'" . $mysqli->real_escape_string($id) . "'";
            }, $companyIds);

            $companyIdsList = implode(',', $escapedCompanyIds);
            $sql .= " AND `employees`.`emp_company` IN ($companyIdsList)";
        }

        // Apply branch filter
        if (!empty($branchIds)) {
            $branchIdsList = implode(',', array_map('intval', $branchIds));
            $sql .= " AND `employees`.`emp_location` IN ($branchIdsList)";
        }

          $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $mysqli);

        // If no company records found, show all (no additional filter)
        if (!empty($accessibleEmployeeIds)) {
            $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
            $sql .= " AND `employees`.`emp_id` IN ($empIds)";
        } else {
            $sql .= " AND 1 = 0";
        }


        $mysqli->close();
    }
// end of new filter

if (!empty($_POST['company'])) {
    $company = $_POST['company'];
    $sql .= " AND `companies`.`id` = '$company'";
}

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $sql .= " AND `departments`.`id` = '$department'";
}
if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $sql .= " AND `employees`.`emp_id` = '$employee'";
}
if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $sql .= " AND `branches`.`id` = '$location'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $sql .= " AND `employees`.`emp_join_date` BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM (" . $sql . ") as `u`";
$extraWhere = "";

try {
    echo json_encode(
        SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>