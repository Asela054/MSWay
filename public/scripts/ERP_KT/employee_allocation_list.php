<?php

$table = 'kt_shift_ot';
$primaryKey = 'id';

$columns = array(
	array( 'db' => '`u`.`id`',       'dt' => 'id',            'field' => 'id' ),
	array( 'db' => '`e`.`emp_name_with_initial`', 'dt' => 'employee_name', 'field' => 'emp_name_with_initial' ),
	array( 'db' => '`u`.`date`',     'dt' => 'date',          'field' => 'date' ),
	array( 'db' => '`u`.`in_time`',  'dt' => 'in_time',       'field' => 'in_time' ),
	array( 'db' => '`u`.`out_time`', 'dt' => 'out_time',      'field' => 'out_time' ),
);

require('../config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

require('../ssp.customized.class.php' );

$joinQuery = "FROM `kt_shift_ot` AS `u`
	LEFT JOIN `employees` AS `e` ON `u`.`emp_id` = `e`.`emp_id`";

$extraWhere = "";

echo json_encode(
	SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
