<?php

require('config.php');
require('ssp.customized.class.php');

$table = 'employees';
$primaryKey = 'id';

$columns = array(
    array('db' => 'employees.id', 'dt' => 'id', 'field' => 'id'),
    array('db' => 'employees.emp_id', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => 'employees.emp_name_with_initial', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => 'employees.emp_national_id', 'dt' => 'emp_national_id', 'field' => 'emp_national_id'),
    array('db' => 'employees.emp_etfno', 'dt' => 'emp_etfno', 'field' => 'emp_etfno'),
    array('db' => 'departments.name', 'dt' => 'name', 'field' => 'name'),
    array('db' => 'employees.emp_join_date', 'dt' => 'emp_join_date', 'field' => 'emp_join_date'),
    array('db' => 'job_titles.title', 'dt' => 'title', 'field' => 'title'),
    array('db' => 'job_categories.category', 'dt' => 'category', 'field' => 'category'),
    array('db' => 'employment_statuses.emp_status', 'dt' => 'emp_status', 'field' => 'emp_status'),
    array('db' => 'branches.location', 'dt' => 'location', 'field' => 'location'),
    array('db' => 'employees.is_resigned', 'dt' => 'is_resigned', 'field' => 'is_resigned'),
);

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

$current_date_time = date('Y-m-d H:i:s');
$previous_month_date = date('Y-m-d', strtotime('-1 month'));

$extraWhere = "employees.deleted = 0";

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $extraWhere .= " AND departments.id = '$department'";
}
if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $extraWhere .= " AND employees.emp_id = '$employee'";
}
if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND branches.id = '$location'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $extraWhere .= " AND employees.emp_join_date BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM employees
LEFT JOIN employment_statuses ON employees.emp_status = employment_statuses.id
LEFT JOIN branches ON employees.emp_location = branches.id
LEFT JOIN departments ON employees.emp_department = departments.id
LEFT JOIN job_titles ON employees.emp_job_code = job_titles.id
LEFT JOIN job_categories ON employees.job_category_id = job_categories.id
";

try {
    echo json_encode(
        SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>