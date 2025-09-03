<?php

$table = 'leaves';
$primaryKey = 'id';

$columns = array(
    array('db' => '`leaves`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`leaves`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`e`.`emp_name_with_initial`', 'dt' => 'emp_name', 'field' => 'emp_name_with_initial'),
    array('db' => '`leave_types`.`leave_type`', 'dt' => 'leave_type', 'field' => 'leave_type'),
    array('db' => '`ec`.`emp_name_with_initial`', 'dt' => 'covering_emp', 'field' => 'emp_name_with_initial'),
    array('db' => '`departments`.`name`', 'dt' => 'dep_name', 'field' => 'dep_name', 'as' => 'dep_name'),
    array('db' => '`leaves`.`leave_from`', 'dt' => 'leave_from', 'field' => 'leave_from'),
    array('db' => '`leaves`.`leave_to`', 'dt' => 'leave_to', 'field' => 'leave_to'),
    array('db' => '`leaves`.`half_short`', 'dt' => 'half_or_short', 'field' => 'half_short'),
    array('db' => '`leaves`.`status`', 'dt' => 'status', 'field' => 'status'),
    array('db' => '`leaves`.`reson`', 'dt' => 'reson', 'field' => 'reson'),
);

require('config.php');

$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php');

$extraWhere = "1=1";

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $extraWhere .= " AND `departments`.`id` = '$department'";
}
if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $extraWhere .= " AND `e`.`emp_id` = '$employee'";
}
if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND `e`.`emp_location` = '$location'";
}
if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $extraWhere .= " AND `leaves`.`leave_from` BETWEEN '$from_date' AND '$to_date'";
}

$joinQuery = "FROM `leaves`
    JOIN `leave_types` ON `leaves`.`leave_type` = `leave_types`.`id`
    JOIN `employees` AS `ec` ON `leaves`.`emp_covering` = `ec`.`emp_id`
    JOIN `employees` AS `e` ON `leaves`.`emp_id` = `e`.`emp_id`
    LEFT JOIN `branches` ON `e`.`emp_location` = `branches`.`id`
    LEFT JOIN `departments` ON `e`.`emp_department` = `departments`.`id`";

try {
    $data = SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere);
    
    foreach ($data['data'] as &$row) {
        $row['emp_name'] = isset($row['emp_name']) ? $row['emp_name'] : 'N/A';
        $row['covering_emp'] = isset($row['covering_emp']) ? $row['covering_emp'] : 'N/A';
        
        if ($row['half_or_short'] == 0.25) {
            $row['half_or_short'] = 'Short Leave';
        } elseif ($row['half_or_short'] == 0.5) {
            $row['half_or_short'] = 'Half Day';
        } elseif ($row['half_or_short'] == 1) {
            $row['half_or_short'] = 'Full Day';
        } else {
            $row['half_or_short'] = '';
        }
    }

    echo json_encode($data);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>
