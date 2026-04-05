<?php

require('../config.php');
require('../ssp.customized.class.php');

$table = 'opma_performance_amount';
$primaryKey = 'id';

$columns = array(
    array('db' => 'opma_performance_amount.id', 'dt' => 'id', 'field' => 'id'),
    array('db' => 'opma_performance_amount.emp_id', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => 'employees.emp_name_with_initial', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    array('db' => 'opma_performance_amount.amount', 'dt' => 'amount', 'field' => 'amount'),
);

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

// Base where clause - only show active records
$extraWhere = "1=1"; 

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
    $extraWhere .= " AND opma_performance_amount.emp_id = '$employee'";
}

if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND employees.emp_location = '$location'";
}

$joinQuery = "FROM opma_performance_amount
LEFT JOIN employees ON opma_performance_amount.emp_id = employees.emp_id AND employees.deleted = 0
LEFT JOIN departments ON employees.emp_department = departments.id
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