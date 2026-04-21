<?php
session_start();

use App\Helpers\EmployeeHelper;

require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';

$table = 'department_sections';
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`section`', 'dt' => 'name', 'field' => 'section'),
    array('db' => '`e`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial', 'visible' => false),
    array('db' => '`e`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name', 'visible' => false),
    array('db' => '`u`.`section_head_emp_id`', 'dt' => 'emp_name', 'field' => 'section_head_emp_id',
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

$department_id = $_POST['department_id'];

$joinQuery = "FROM `department_sections` AS `u` LEFT JOIN `employees` AS `e` ON `u`.`section_head_emp_id` = `e`.`emp_id` AND `u`.`section_head_emp_id` != 0";

$extraWhere = "1=1 AND `u`.`department_id` = '$department_id'";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);