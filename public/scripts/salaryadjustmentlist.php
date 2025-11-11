<?php
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

// DB table to use
$table = 'salary_adjustments';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array( 'db' => 'u.id', 'dt' => 'id', 'field' => 'id' ),
    array( 'db' => 'u.adjustment_type', 'dt' => 'adjustment_type', 'field' => 'adjustment_type' ),
    array( 'db' => 'ua.emp_name_with_initial', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial' ),
    array( 'db' => 'ub.category', 'dt' => 'category', 'field' => 'category' ),
    array( 'db' => 'uc.remuneration_name', 'dt' => 'remuneration_name', 'field' => 'remuneration_name' ),
    array( 'db' => 'u.allowance_type', 'dt' => 'allowance_type', 'field' => 'allowance_type' ),
    array( 'db' => 'u.amount', 'dt' => 'amount', 'field' => 'amount' ),
    array( 'db' => 'u.allowleave', 'dt' => 'allowleave', 'field' => 'allowleave' ),
    array( 'db' => 'u.approved_status', 'dt' => 'approved_status', 'field' => 'approved_status' ),
    array( 'db' => 'ua.calling_name', 'dt' => 'calling_name', 'field' => 'calling_name' ),
    array( 'db' => 'u.emp_id', 'dt' => 'employee_display', 'field' => 'emp_id', 
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

$joinQuery = "FROM `salary_adjustments` AS `u` 
LEFT JOIN `employees` AS `ua` ON `ua`.`emp_id` = `u`.`emp_id`
LEFT JOIN `job_categories` AS `ub` ON `ub`.`id` = `u`.`job_id`
LEFT JOIN `remunerations` AS `uc` ON `uc`.`id` = `u`.`remuneration_id`";
    
$extraWhere = "1=1";

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);