<?php
require('config.php');

// Create database connection
$connection = new mysqli($db_host, $db_username, $db_password, $db_name);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get DataTables parameters
$draw = intval($_POST['draw']);
$start = intval($_POST['start']);
$length = intval($_POST['length']);
$search = $_POST['search']['value'];

// Build the combined query
$baseQuery = "
    SELECT 
        CONCAT('task_', `et`.`id`) as unique_id,
        `et`.`emp_id`,
        `e`.`emp_name_with_initial`,
        `et`.`date`,
        `t`.`taskname`,
        '' as productname
    FROM `employee_task` AS `et`
    LEFT JOIN `task` AS `t` ON `et`.`task_id` = `t`.`id`
    LEFT JOIN `employees` AS `e` ON `et`.`emp_id` = `e`.`emp_id`
    WHERE `et`.`status` = 1";

// Add task filter if provided
if (!empty($_POST['task'])) {
    $task = $connection->real_escape_string($_POST['task']);
    $baseQuery .= " AND `et`.`task_id` = '$task'";
}

$baseQuery .= "
    UNION ALL
    
    SELECT 
        CONCAT('prod_', `ep`.`allocation_id`) as unique_id,
        `ep`.`emp_id`,
        `e`.`emp_name_with_initial`,
        `ep`.`date`,
        '' as taskname,
        `pr`.`productname`
    FROM `employee_production` AS `ep`
    LEFT JOIN `product` AS `pr` ON `ep`.`product_id` = `pr`.`id`
    LEFT JOIN `employees` AS `e` ON `ep`.`emp_id` = `e`.`emp_id`
    WHERE `ep`.`status` = 1";

// Add product filter if provided
if (!empty($_POST['product'])) {
    $product = $connection->real_escape_string($_POST['product']);
    $baseQuery .= " AND `ep`.`product_id` = '$product'";
}

// Wrap the union query
$sql = "SELECT * FROM (" . $baseQuery . ") as combined_result WHERE 1=1";

// Apply common filters
if (!empty($_POST['employee'])) {
    $employee = $connection->real_escape_string($_POST['employee']);
    $sql .= " AND `emp_id` = '$employee'";
}

if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
    $from_date = $connection->real_escape_string($_POST['from_date']);
    $to_date = $connection->real_escape_string($_POST['to_date']);
    $sql .= " AND `date` BETWEEN '$from_date' AND '$to_date'";
}

// Add search functionality
if (!empty($search)) {
    $search = $connection->real_escape_string($search);
    $sql .= " AND (`emp_name_with_initial` LIKE '%$search%' OR `taskname` LIKE '%$search%' OR `productname` LIKE '%$search%')";
}

// Get total records without pagination
$totalQuery = "SELECT COUNT(*) as total FROM (" . $sql . ") as count_table";
$totalResult = $connection->query($totalQuery);
$totalRecords = $totalResult->fetch_assoc()['total'];

// Add pagination and ordering
$sql .= " ORDER BY `date` DESC, `emp_id`";
if ($length != -1) {
    $sql .= " LIMIT $start, $length";
}

// Execute main query
$result = $connection->query($sql);

$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = array(
            'emp_id' => $row['emp_id'],
            'emp_name' => $row['emp_name_with_initial'],
            'date' => $row['date'],
            'task' => $row['taskname'] ?: '-',
            'product' => $row['productname'] ?: '-'
        );
    }
}

$response = array(
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalRecords,
    "data" => $data
);

echo json_encode($response);
$connection->close();
?>