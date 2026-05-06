<?php

$table = 'kt_job_details';
$primaryKey = 'id';

$columns = array(
    array('db' => '`jd`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`c`.`name`', 'dt' => 'customer_name', 'field' => 'name'),
    array('db' => '`d`.`inquiry`', 'dt' => 'inquiry', 'field' => 'inquiry'),
    array('db' => '`j`.`reading_hours`', 'dt' => 'reading_hours', 'field' => 'reading_hours'),
    array('db' => '`m`.`machine_name`', 'dt' => 'machine', 'field' => 'machine_name'),
    array('db' => '`e`.`calling_name`', 'dt' => 'employee', 'field' => 'calling_name'),
    array('db' => '`jt`.`title`', 'dt' => 'job_title', 'field' => 'title'),
    array('db' => '`jd`.`approve_status`', 'dt' => 'approve_status', 'field' => 'approve_status'),
);

require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery = "FROM `kt_job_details` AS `jd`
              LEFT JOIN `kt_job_inquiry`     AS `j`  ON `j`.`id`      = `jd`.`job_id`
              LEFT JOIN `kt_customer`        AS `c`  ON `c`.`id`      = `j`.`customer_id`
              LEFT JOIN `kt_inquiry_details` AS `d`  ON `d`.`id`      = `j`.`inquiry_id`
              LEFT JOIN `kt_machines`        AS `m`  ON `m`.`id`      = `jd`.`machine_id`
              LEFT JOIN `employees`          AS `e`  ON `e`.`emp_id`  = `jd`.`emp_id`
              LEFT JOIN `job_titles`         AS `jt` ON `jt`.`id`     = `jd`.`job_title`";

$extraWhere = "`jd`.`approve_status` = 0 AND `j`.`status` != 3";
$groupBy    = "";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy)
);
