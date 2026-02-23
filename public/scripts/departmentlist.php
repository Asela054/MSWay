<?php
session_start();

use App\Helpers\EmployeeHelper;

require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

$table = 'departments';
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`name`', 'dt' => 'name', 'field' => 'name'),
    array('db' => '`e`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial', 'visible' => false),
    array('db' => '`e`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name', 'visible' => false),
    array('db' => '`u`.`dep_head_emp_id`', 'dt' => 'emp_name', 'field' => 'dep_head_emp_id',
        'formatter' => function($d, $row) {
            if (empty($d) || $d == 0) {
                return 'N/A';
            }
            $employee = (object)[
                'emp_name_with_initial' => $row['emp_name_with_initial'],
                'calling_name'          => $row['calling_name'],
                'emp_id'                => $d
            ];
            return EmployeeHelper::getDisplayName($employee);
        }
    ),
);

require('config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php');

$company_id = $_POST['company_id'];

$joinQuery = "FROM `departments` AS `u` LEFT JOIN `employees` AS `e` ON `u`.`dep_head_emp_id` = `e`.`emp_id` AND `u`.`dep_head_emp_id` != 0";

$extraWhere = "1=1 AND `u`.`company_id` = '$company_id'";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);