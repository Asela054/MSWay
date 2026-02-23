<?php

require('config.php');
require('ssp.customized.class.php');

$table = 'meter_reading_count';
$primaryKey = 'id';

$columns = array(
    array('db' => 'meter_reading_count.id', 'dt' => 'id', 'field' => 'id'),
    array('db' => 'meter_reading_count.emp_id', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => 'meter_reading_count.date', 'dt' => 'date', 'field' => 'date'),
    array('db' => 'employees.emp_name_with_initial', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => 'COALESCE(departments.name, "")', 'dt' => 'department_name', 'field' => 'department_name', 'as' => 'department_name'),
    array('db' => 'meter_reading_count.count', 'dt' => 'count', 'field' => 'count'),
);

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

// Base where clause - only show active records
$extraWhere = "meter_reading_count.status != '3'";

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
    $extraWhere .= " AND meter_reading_count.emp_id = '$employee'";
}

if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND employees.emp_location = '$location'";
}

if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $extraWhere .= " AND meter_reading_count.date BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM meter_reading_count
LEFT JOIN employees ON meter_reading_count.emp_id = employees.emp_id AND employees.deleted = 0
LEFT JOIN branches ON employees.emp_location = branches.id
LEFT JOIN departments ON employees.emp_department = departments.id
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