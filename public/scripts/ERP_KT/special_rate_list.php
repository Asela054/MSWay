<?php

$table = 'kt_special_rate';
$primaryKey = 'id';

$columns = array(
    array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => "COALESCE(`m`.`machine_name`, IF(`u`.`machine_id` = 0, 'All Machines', NULL))",'dt' => 'machine_name','field' => 'machine_name','as' => 'machine_name'),
    array('db' => '`jt`.`title`', 'dt' => 'job_title', 'field' => 'title'),
    array('db' => "CONCAT(`e`.`emp_name_with_initial`, ' - ', `e`.`calling_name`)", 'dt' => 'employee', 'field' => 'employee_full_name', 'as' => 'employee_full_name'),
    array('db' => '`u`.`rate`', 'dt' => 'rate', 'field' => 'rate'),
    array('db' => '`u`.`remarks`', 'dt' => 'remarks', 'field' => 'remarks')
);

require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery = "FROM `kt_special_rate` AS `u`
              LEFT JOIN `employees` AS `e`  ON `e`.`emp_id` = `u`.`emp_id`
              LEFT JOIN `kt_machines` AS `m` ON `m`.`id` = `u`.`machine_id`
              LEFT JOIN `job_titles` AS `jt` ON `jt`.`id` = `u`.`job_title`";

$extraWhere = "";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
