<?php

$table = 'kt_job_inquiry';
$primaryKey = 'id';

$columns = array(
    array('db' => '`j`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`c`.`name`', 'dt' => 'customer_name', 'field' => 'name'),
    array('db' => '`d`.`inquiry`', 'dt' => 'inquiry', 'field' => 'inquiry'),
    array('db' => '`j`.`start_from`','dt' => 'start_from', 'field' => 'start_from'),
    array('db' => '`j`.`end_at`',  'dt' => 'end_at', 'field' => 'end_at'),
);

require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery = "FROM `kt_job_inquiry` AS `j`
              LEFT JOIN `kt_inquiry_details` AS `d` ON `d`.`id` = `j`.`inquiry_id`
              LEFT JOIN `kt_inquiries`       AS `i` ON `i`.`id` = `d`.`inquiry_id`
              LEFT JOIN `kt_customer`        AS `c` ON `c`.`id` = `j`.`customer_id`";

$extraWhere = "`d`.`approve_status` = 1 AND `j`.`status` != 3";
$groupBy    = "`j`.`id`";

echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy)
);
