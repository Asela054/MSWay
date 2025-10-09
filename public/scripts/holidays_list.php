<?php
// DB table to use
$table = 'holidays';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array( 'db' => '`h`.`id`', 'dt' => 'id', 'field' => 'id' ),
    array( 'db' => '`h`.`holiday_name`', 'dt' => 'holiday_name', 'field' => 'holiday_name' ),
    array( 'db' => '`ht`.`name`', 'dt' => 'holiday_type_name', 'field' => 'name' ),
    array( 'db' => '`h`.`half_short`', 'dt' => 'half_short', 'field' => 'half_short' ),
    array( 'db' => '`h`.`date`', 'dt' => 'date', 'field' => 'date' ),
    array( 'db' => '`wl`.`level`', 'dt' => 'level', 'field' => 'level' ),
    array( 'db' => '`h`.`work_level`', 'dt' => 'work_level', 'field' => 'work_level' ),
    array( 'db' => '`h`.`holiday_type`', 'dt' => 'holiday_type', 'field' => 'holiday_type' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php');

// Join query
$joinQuery = "FROM `holidays` AS `h` 
              LEFT JOIN `work_levels` AS `wl` ON `h`.`work_level` = `wl`.`id`
              LEFT JOIN `holiday_types` AS `ht` ON `h`.`holiday_type` = `ht`.`id`";

// Add any extra conditions if needed
$extraWhere = ""; // You can add conditions like "`h`.`status` = 1" if needed

echo json_encode(
    SSP::simple( $_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);