<?php


// DB table to use
$table = 'leaves';

// Table's primary key
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name', 'field' => 'emp_name_with_initial'),
    array('db' => '`u`.`leave_type`', 'dt' => 'leave_type', 'field' => 'leave_type'),
    array('db' => '`u`.`covering_emp_name`', 'dt' => 'covering_emp', 'field' => 'covering_emp_name'),
    array('db' => '`u`.`dep_name`', 'dt' => 'dep_name', 'field' => 'dep_name'),
    array('db' => '`u`.`leave_from`', 'dt' => 'leave_from', 'field' => 'leave_from'),
    array('db' => '`u`.`leave_to`', 'dt' => 'leave_to', 'field' => 'leave_to'),
    array('db' => '`u`.`half_short`', 'dt' => 'half_short', 'field' => 'half_short'),
    array('db' => '`u`.`status`', 'dt' => 'status', 'field' => 'status'),
    array('db' => '`u`.`reson`', 'dt' => 'reson', 'field' => 'reson')
);


// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('ssp.customized.class.php' );


    $sql = "SELECT 
        `leaves`.`id`,
        `leaves`.`emp_id`,
        `e`.`emp_name_with_initial`,
        `leave_types`.`leave_type`,
        `ec`.`emp_name_with_initial` AS `covering_emp_name`,
        `departments`.`name` AS `dep_name`,
        `leaves`.`leave_from`,
        `leaves`.`leave_to`,
        `leaves`.`half_short`,
        `leaves`.`status`,
        `leaves`.`reson`
    FROM `leaves`
    JOIN `leave_types` ON `leaves`.`leave_type` = `leave_types`.`id`
    LEFT JOIN `employees` AS `ec` ON `leaves`.`emp_covering` = `ec`.`emp_id`
    LEFT JOIN `employees` AS `e` ON `leaves`.`emp_id` = `e`.`emp_id`
    LEFT JOIN `branches` ON `e`.`emp_location` = `branches`.`id`
    LEFT JOIN `departments` ON `e`.`emp_department` = `departments`.`id`
    WHERE 1=1";

    if (!empty($_POST['department'])) {
        $department = $_POST['department'];
        $sql .= " AND `departments`.`id` = '$department'";
    }
    if (!empty($_POST['employee'])) {
        $employee = $_POST['employee'];
        $sql .= " AND `e`.`emp_id` = '$employee'";
    }
    if (!empty($_POST['location'])) {
        $location = $_POST['location'];
        $sql .= " AND `e`.`emp_location` = '$location'";
    }
    if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
        $from_date = $_POST['from_date'];
        $to_date = $_POST['to_date'];
        $sql .= " AND `leaves`.`leave_from` BETWEEN '$from_date' AND '$to_date'";
    }
    $sql .= " AND `leaves`.`leave_type`!=7";

    $joinQuery = "FROM (" . $sql . ") as `u`";

    $extraWhere = "";

    echo json_encode(SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
	?>