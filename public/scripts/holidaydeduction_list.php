<?php

/*
 * DataTables server-side processing script for holiday_deductions table
 */

// DB table to use
$table = 'holiday_deductions';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
$columns = array(
    array( 'db' => '`hd`.`id`', 'dt' => 'id', 'field' => 'id' ),
    array( 'db' => '`jc`.`category`', 'dt' => 'category', 'field' => 'category' ),
    array( 'db' => '`r`.`remuneration_name`', 'dt' => 'remuneration_name', 'field' => 'remuneration_name' ),
    array( 'db' => '`hd`.`day_count`', 'dt' => 'day_count', 'field' => 'day_count' ),
    array( 'db' => '`hd`.`amount`', 'dt' => 'amount', 'field' => 'amount' ),
    array( 'db' => '`hd`.`job_id`', 'dt' => 'job_id', 'field' => 'job_id' ),
    array( 'db' => '`hd`.`remuneration_id`', 'dt' => 'remuneration_id', 'field' => 'remuneration_id' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

require('ssp.customized.class.php' );

$joinQuery = "FROM `holiday_deductions` AS `hd` 
              LEFT JOIN `job_categories` AS `jc` ON `hd`.`job_id` = `jc`.`id`
              LEFT JOIN `remunerations` AS `r` ON `hd`.`remuneration_id` = `r`.`id`";

$extraWhere = "1=1";

// COMPREHENSIVE FIX: Create proper POST data structure
$postData = $_POST;

// Ensure draw parameter exists
if (!isset($postData['draw'])) {
    $postData['draw'] = isset($_GET['draw']) ? intval($_GET['draw']) : 0;
}

// Ensure start parameter exists
if (!isset($postData['start'])) {
    $postData['start'] = isset($_GET['start']) ? intval($_GET['start']) : 0;
}

// Ensure length parameter exists
if (!isset($postData['length'])) {
    $postData['length'] = isset($_GET['length']) ? intval($_GET['length']) : 10;
}

// Ensure search parameter exists
if (!isset($postData['search'])) {
    $postData['search'] = array(
        'value' => isset($_GET['search']['value']) ? $_GET['search']['value'] : '',
        'regex' => isset($_GET['search']['regex']) ? $_GET['search']['regex'] : false
    );
}

// Ensure order parameter exists
if (!isset($postData['order']) || !is_array($postData['order'])) {
    $postData['order'] = array();
}

// Ensure columns parameter exists with proper structure
if (!isset($postData['columns']) || !is_array($postData['columns'])) {
    $postData['columns'] = array();
    
    // Create default columns structure based on your DataTables configuration
    $columnData = array('id', 'category', 'remuneration_name', 'day_count', 'amount', 'action');
    
    foreach ($columnData as $column) {
        $postData['columns'][] = array(
            'data' => $column,
            'name' => '',
            'searchable' => $column === 'action' ? 'false' : 'true',
            'orderable' => $column === 'action' ? 'false' : 'true',
            'search' => array(
                'value' => '',
                'regex' => 'false'
            )
        );
    }
}

// Get data from SSP
$result = SSP::simple( $postData, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere);
echo json_encode($result);
?>