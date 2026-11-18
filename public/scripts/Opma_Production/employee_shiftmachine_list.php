<?php

// DB table to use
$table = 'opma_employee_production';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`department_name`', 'dt' => 'department', 'field' => 'department_name'),   
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`shift_name`', 'dt' => 'shift_name', 'field' => 'shift_name'),   
    array('db' => '`u`.`shift_code`', 'dt' => 'shift_code', 'field' => 'shift_code'), 
    array('db' => '`u`.`machine`', 'dt' => 'machine', 'field' => 'machine'),
    array('db' => '`u`.`title`', 'dt' => 'product', 'field' => 'title'),
 
);

// SQL server connection information
require('../config.php');
require('../ssp.customized.class.php');

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);


$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

function clean_date($d)
{
    return (is_string($d) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) ? $d : '';
}

$employee  = !empty($_POST['employee']) ? $mysqli->real_escape_string($_POST['employee']) : '';
$department = !empty($_POST['department']) ? (int) $_POST['department'] : 0;
$machine   = !empty($_POST['machine']) ? (int) $_POST['machine'] : 0;
$product   = !empty($_POST['product']) ? (int) $_POST['product'] : 0;
$shift     = !empty($_POST['shift']) ? (int) $_POST['shift'] : 0;
$from_date = !empty($_POST['from_date']) ? clean_date($_POST['from_date']) : '';
$to_date   = !empty($_POST['to_date']) ? clean_date($_POST['to_date']) : '';

$hasDateRange = ($from_date !== '' && $to_date !== '');

$rosterWhere = '';
if ($hasDateRange) {
    $rosterWhere = " AND `r`.`work_date` BETWEEN '$from_date' AND '$to_date'";
}

$rosterSql = "SELECT
        `r`.`emp_id`,
        `r`.`work_date`,
        GROUP_CONCAT(DISTINCT `st`.`shift_name` ORDER BY `st`.`id` SEPARATOR ' / ') AS `shift_name`,
        GROUP_CONCAT(DISTINCT `st`.`shift_code` ORDER BY `st`.`id` SEPARATOR ' / ') AS `shift_code`,
        GROUP_CONCAT(DISTINCT `r`.`shift_id` ORDER BY `r`.`shift_id`) AS `shift_ids`
    FROM `employee_roster_details` AS `r`
    LEFT JOIN `shift_types` AS `st` ON `r`.`shift_id` = `st`.`id`
    WHERE 1=1 $rosterWhere
    GROUP BY `r`.`emp_id`, `r`.`work_date`";

$sql = "SELECT
        `ep`.`id`,
        `ep`.`emp_id`,
        `e`.`emp_name_with_initial`,
        COALESCE(`d`.`name`, '-') AS `department_name`,
        `ep`.`date`,
        COALESCE(`rs`.`shift_name`, '-') AS `shift_name`,
        COALESCE(`rs`.`shift_code`, '-') AS `shift_code`,
        `m`.`machine`,
        `p`.`title`
    FROM `opma_employee_production` AS `ep`
    LEFT JOIN `opma_machines` AS `m` ON `ep`.`machine_id` = `m`.`id`
    LEFT JOIN `opma_styles` AS `p` ON `ep`.`product_id` = `p`.`id`
    LEFT JOIN `employees` AS `e` ON `ep`.`emp_id` = `e`.`emp_id`
    LEFT JOIN `departments` AS `d` ON `e`.`emp_department` = `d`.`id`
    LEFT JOIN ($rosterSql) AS `rs`
        ON `rs`.`emp_id` = `ep`.`emp_id`
        AND `rs`.`work_date` = DATE(`ep`.`date`)
    WHERE 1=1";

if ($employee !== '') {
    $sql .= " AND `ep`.`emp_id` = '$employee'";
}
if ($department > 0) {
    $sql .= " AND `e`.`emp_department` = $department";
}
if ($machine > 0) {
    $sql .= " AND `ep`.`machine_id` = $machine";
}
if ($product > 0) {
    $sql .= " AND `ep`.`product_id` = $product";
}
if ($shift > 0) {
    $sql .= " AND FIND_IN_SET('$shift', `rs`.`shift_ids`) > 0";
}
if ($hasDateRange) {
    $sql .= " AND `ep`.`date` BETWEEN '$from_date' AND '$to_date'";
}

$sql .= " AND `ep`.`status` = 1";

$joinQuery = "FROM (" . $sql . ") as `u`";

$extraWhere = "";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>