<?php

$table      = 'issued_letters';
$primaryKey = 'id';

$columns = array(
    array( 'db' => '`u`.`id`',                       'dt' => 'id',                    'field' => 'id' ),
    array( 'db' => '`e`.`emp_name_with_initial`',    'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial' ),
    array( 'db' => '`lt`.`letter_type`',             'dt' => 'letter_type',           'field' => 'letter_type' ),
    array( 'db' => '`tmpl`.`name`',                  'dt' => 'template_name',         'field' => 'name' ),
    array( 'db' => '`u`.`issued_date`',              'dt' => 'issued_date',           'field' => 'issued_date' ),
);

require('../config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('../ssp.customized.class.php');

$joinQuery  = "FROM `issued_letters` AS `u`
               LEFT JOIN `employees`        AS `e`    ON `u`.`employee_id`    = `e`.`emp_id`
               LEFT JOIN `letter_types`     AS `lt`   ON `u`.`letter_type_id` = `lt`.`id`
               LEFT JOIN `letter_templates` AS `tmpl` ON `u`.`template_id`    = `tmpl`.`id`";

$extraWhere = "";

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere )
);