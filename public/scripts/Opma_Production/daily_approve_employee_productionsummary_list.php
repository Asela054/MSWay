<?php

// DB table to use
$table = 'opma_daily_approval_summary';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`date`', 'dt' => 'date', 'field' => 'date'),
    array('db' => '`u`.`daily_average`', 'dt' => 'daily_average', 'field' => 'daily_average'),
    array('db' => '`u`.`target_bonus`', 'dt' => 'target_bonus', 'field' => 'target_bonus')
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

$sql = "SELECT 
        `ep`.`id`,
        `ep`.`emp_id`,
        `e`.`emp_name_with_initial`,
        `ep`.`department_id`,
        `ep`.`date`,
        `ep`.`daily_target`,
        `ep`.`daily_produce`,
        `ep`.`daily_average`,
        `ep`.`target_bonus`,
        `ep`.`status`
    FROM `opma_daily_approval_summary` AS `ep`
    LEFT JOIN `employees` AS `e` ON `ep`.`emp_id` = `e`.`emp_id`
    WHERE 1=1
    AND `ep`.`status` = 1
    AND `ep`.`daily_average` IS NOT NULL AND `ep`.`daily_average` != 0";

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $sql .= " AND `ep`.`department_id` = '$department'";
}
if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $sql .= " AND `ep`.`emp_id` = '$employee'";
}

if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $sql .= " AND `ep`.`date` BETWEEN '$from_date' AND '$to_date'";
} else {
    // no date range given -> pull the last available date's records
    // (only rows where daily_target & daily_produce are populated, status = 1)
    $lastDateSql = "SELECT MAX(`date`) AS `max_date` 
                     FROM `opma_daily_approval_summary` 
                     WHERE `status` = 1
                       AND `daily_target` IS NOT NULL AND `daily_target` != ''
                       AND `daily_produce` IS NOT NULL AND `daily_produce` != ''";

    // apply the same department/employee filters to the max-date lookup, if given
    if (!empty($_POST['department'])) {
        $lastDateSql .= " AND `department_id` = '" . $_POST['department'] . "'";
    }
    if (!empty($_POST['employee'])) {
        $lastDateSql .= " AND `emp_id` = '" . $_POST['employee'] . "'";
    }

    $sql .= " AND `ep`.`date` = ($lastDateSql)
              AND `ep`.`daily_target` IS NOT NULL AND `ep`.`daily_target` != ''
              AND `ep`.`daily_produce` IS NOT NULL AND `ep`.`daily_produce` != ''";
}

$joinQuery = "FROM (" . $sql . ") as `u`";

$extraWhere = "";

echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
?>