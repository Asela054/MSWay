<?php

require('../config.php');
require('../ssp.customized.class.php');

$table = 'opma_production_amount';
$primaryKey = 'id';

$columns = array(
    array('db' => 'opma_production_amount.id', 'dt' => 'id', 'field' => 'id'),
    array('db' => 'departments.name', 'dt' => 'dept_name', 'field' => 'name'),
    array('db' => 'job_titles.title', 'dt' => 'job_title', 'field' => 'title'),
    array('db' => 'opma_production_amount.end_precentage', 'dt' => 'end_precentage', 'field' => 'end_precentage'),
    array('db' => 'opma_production_amount.amount', 'dt' => 'amount', 'field' => 'amount'),
);

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

// Base where clause - only show active records
$extraWhere = "1=1"; 

$joinQuery = "FROM opma_production_amount
LEFT JOIN departments ON opma_production_amount.department_id = departments.id
LEFT JOIN job_titles ON opma_production_amount.jobtitle = job_titles.id
";

try {
    echo json_encode(
        SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>