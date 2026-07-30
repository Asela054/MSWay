<?php
/*
 * DataTables server-side script for Users + Roles + Companies
 */

// Main table
$table = 'users';
$primaryKey = 'id';

// Columns definition
// NOTE: For columns with SQL aliases, we separate the expression ('db') from
// the alias ('as') so the SSP filter() uses only the raw expression in LIKE
// clauses (no "AS alias LIKE '%s%'" error), while pluck() still adds the alias
// to the SELECT list correctly.
//
// For 'roles' we use a correlated subquery instead of GROUP_CONCAT so it can
// be searched with WHERE LIKE (aggregate GROUP_CONCAT cannot appear in WHERE).
$columns = array(
    array( 'db' => '`u`.`id`',     'dt' => 'id',     'field' => 'id' ),
    array( 'db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id' ),
    array( 'db' => '`u`.`name`',   'dt' => 'name',   'field' => 'name' ),
    array( 'db' => '`u`.`email`',  'dt' => 'email',  'field' => 'email' ),

    // Correlated subquery — evaluates per row, works in WHERE LIKE
    array(
        'db'    => '(SELECT GROUP_CONCAT(r2.`name` SEPARATOR \', \') FROM `user_has_roles` mr2 LEFT JOIN `roles` r2 ON r2.`id` = mr2.`role_id` WHERE mr2.`user_id` = `u`.`id`)',
        'dt'    => 'roles',
        'field' => 'roles',
        'as'    => 'roles'
    ),

    // 'as' key is used by pluck() for SELECT alias; filter() only uses 'db'
    array(
        'db'    => '`c`.`name`',
        'dt'    => 'company_name',
        'field' => 'company_name',
        'as'    => 'company_name'
    ),
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

// Simplified join — only companies needed (roles handled by correlated subquery above)
$joinQuery = "
    FROM `users` AS `u`
    LEFT JOIN `companies` AS `c` ON `u`.`company_id` = `c`.`id`
";

// Optional filters
$extraWhere = "1=1";

// No GROUP BY needed — correlated subquery replaces the aggregate JOIN approach
$groupBy = "";

// Return JSON response
echo json_encode(
    SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere, $groupBy)
);
