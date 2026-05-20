<?php

$table = 'kt_inquiries';
$primaryKey = 'id';

$columns = array(
    array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
    array( 'db' => 'COALESCE(`c`.`name`, `u`.`customer_id`)','dt' => 'customer_name', 'field' => 'customer_name', 'as' => 'customer_name' ),
    array( 'db' => '`u`.`date`','dt' => 'date','field' => 'date' ),
    array( 'db' => '`u`.`remarks`','dt' => 'remarks','field' => 'remarks' ),
);

require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery = "FROM `kt_inquiries` AS `u`
    LEFT JOIN `kt_customer` AS `c` ON `c`.`id` = `u`.`customer_id`
    LEFT JOIN `kt_inquiry_details` AS `d` ON `d`.`inquiry_id` = `u`.`id`";

$extraWhere = "`u`.`status` != 3";

if (!empty($_POST['customer_id'])) {
    $customer_id = $_POST['customer_id'];
    $extraWhere .= " AND `u`.`customer_id` = '$customer_id'";
}

if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $extraWhere .= " AND `u`.`date` BETWEEN '$from_date' AND '$to_date'";
}
$groupBy    = "`u`.`id`";

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy)
);
