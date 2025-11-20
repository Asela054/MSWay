<?php
/*
 * DataTables server-side script for Users + Roles + Companies
 */

// Main table
$table = 'users';
$primaryKey = 'id';

// Columns definition
$columns = array(
    array( 'db' => '`u`.`id`',       'dt' => 'id',           'field' => 'id' ),
    array( 'db' => '`u`.`emp_id`',   'dt' => 'emp_id',       'field' => 'emp_id' ),
    array( 'db' => '`u`.`name`',     'dt' => 'name',         'field' => 'name' ),
    array( 'db' => '`u`.`email`',    'dt' => 'email',        'field' => 'email' ),
    array( 'db' => 'GROUP_CONCAT(`r`.`name` SEPARATOR ", ") AS roles', 'dt' => 'roles', 'field' => 'roles' ),

    array( 'db' => '`c`.`name` AS company_name', 'dt' => 'company_name', 'field' => 'company_name' ),
);


// DB connection info
require('config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php');

// ✅ Proper join for Spatie + company
$joinQuery = "
    FROM `users` AS `u`
    LEFT JOIN `user_has_roles` AS `mr`
        ON `mr`.`user_id` = `u`.`id` 
    LEFT JOIN `roles` AS `r` ON `r`.`id` = `mr`.`role_id`
    LEFT JOIN `companies` AS `c` ON `u`.`company_id` = `c`.`id`
";

// Optional filters
$extraWhere = "1=1";

// Group by user to avoid duplicate rows if user has multiple roles
$groupBy = "`u`.`id`";

// Return JSON response
echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy)
);
