<?php
session_start();

use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

require('config.php');
require('ssp.customized.class.php');

$table = 'salary_advances';
$primaryKey = 'id';

$columns = array(
    array('db' => 'salary_advances.id', 'dt' => 'id', 'field' => 'id'),
    array('db' => 'salary_advances.emp_id', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => 'salary_advances.date', 'dt' => 'date', 'field' => 'date'),
    array('db' => 'salary_advances.request_amount', 'dt' => 'request_amount', 'field' => 'request_amount'),
    array('db' => 'salary_advances.paid_amount', 'dt' => 'paid_amount', 'field' => 'paid_amount'),
    array('db' => 'salary_advances.paid_status', 'dt' => 'paid_status', 'field' => 'paid_status'),
    array('db' => 'salary_advances.approve_status', 'dt' => 'approve_status', 'field' => 'approve_status'),
    array('db' => 'employees.emp_name_with_initial', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => 'employees.calling_name', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => 'COALESCE(departments.name, "")', 'dt' => 'department_name', 'field' => 'department_name', 'as' => 'department_name'),
    array('db' => 'job_categories.category', 'dt' => 'category', 'field' => 'category', 'as' => 'category'),
    array('db' => 'employees.emp_id', 'dt' => 'employee_display', 'field' => 'emp_id', 
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

// Base where clause - only show active records
$extraWhere = "salary_advances.status != '3'";

// new filter based on user access rights
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
        $extraWhere .= " AND employees.emp_id IN ($empIds)";
    } else {
        $extraWhere .= " AND 1 = 0";
    }
    
    $mysqli->close();
}
// end of new filter

// Filter conditions
if (!empty($_POST['company'])) {
    $company = $_POST['company'];
    $extraWhere .= " AND employees.emp_company = '$company'";
}

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $extraWhere .= " AND employees.emp_department = '$department'";
}

if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $extraWhere .= " AND salary_advances.emp_id = '$employee'";
}

if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND employees.emp_location = '$location'";
}

if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $extraWhere .= " AND salary_advances.date BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM salary_advances
LEFT JOIN employees ON salary_advances.emp_id = employees.emp_id AND employees.deleted = 0
LEFT JOIN departments ON employees.emp_department = departments.id
LEFT JOIN job_categories ON employees.job_category_id = job_categories.id
LEFT JOIN branches ON employees.emp_location = branches.id
LEFT JOIN companies ON employees.emp_company = companies.id
";

try {
    echo json_encode(
        SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>