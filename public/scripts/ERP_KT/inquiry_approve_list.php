<?php

$table = 'kt_inquiry_details';
$primaryKey = 'id';

$columns = array(
    array( 'db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id' ),
    array( 'db' => '`c`.`name`',  'dt' => 'customer_name', 'field' => 'name' ),
    array( 'db' => '`i`.`date`','dt' => 'date_count','field' => 'date' ),
    array( 'db' => '`u`.`inquiry`','dt' => 'inquiry','field' => 'inquiry' ),
    array( 'db' => '`u`.`quotation`','dt' => 'quotation','field' => 'quotation' ),
    array( 'db' => '`u`.`approve_status`','dt' => 'approve_status','field' => 'approve_status' ),
);

require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery = "FROM `kt_inquiry_details` AS `u`
              LEFT JOIN `kt_inquiries` AS `i` ON `u`.`inquiry_id` = `i`.`id`
              LEFT JOIN `kt_customer` AS `c` ON `i`.`customer_id` = `c`.`id`";

$extraWhere = "";
$groupBy    = "`u`.`id`";

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy)
);
