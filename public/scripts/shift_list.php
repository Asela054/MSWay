<?php



require('config.php');
require('ssp.customized.class.php');

$table = 'employees';
$primaryKey = 'id';

$columns = array(
    array('db' => '`employees`.`id`', 'dt' => 'id', 'field' => 'id'),
    array('db' => '`employees`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id'),
    array('db' => '`employees`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name'),
    array('db' => '`employees`.`emp_first_name`', 'dt' => 'emp_first_name', 'field' => 'emp_first_name'),
    
    // ✅ shift type id
    array('db' => '`shift_types`.`id`', 'dt' => 'shift_type_id', 'field' => 'id'),
    
    array('db' => '`shift_types`.`shift_name`', 'dt' => 'shift_name', 'field' => 'shift_name'),
    array('db' => '`shift_types`.`onduty_time`', 'dt' => 'onduty_time', 'field' => 'onduty_time'),
    array('db' => '`shift_types`.`offduty_time`', 'dt' => 'offduty_time', 'field' => 'offduty_time'),
    array('db' => '`employees`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial'),
    
    // ✅ fixed department aliasing
    array('db' => '`departments`.`name`', 'dt' => 'departmentname', 'field' => 'name')
);



$sql_details = array(
    'user' => $db_username,
    'pass' => $db_password,
    'db'   => $db_name,
    'host' => $db_host
);

$current_date_time = date('Y-m-d H:i:s');
$previous_month_date = date('Y-m-d', strtotime('-1 month'));

$extraWhere = "employees.deleted = 0";

if (!empty($_POST['department'])) {
    $department = $_POST['department'];
    $extraWhere .= " AND departments.id = '$department'";
}
if (!empty($_POST['employee'])) {
    $employee = $_POST['employee'];
    $extraWhere .= " AND employees.emp_id = '$employee'";
}
if (!empty($_POST['location'])) {
    $location = $_POST['location'];
    $extraWhere .= " AND branches.id = '$location'";
}


$joinQuery = "FROM employees
LEFT JOIN employment_statuses ON employees.emp_status = employment_statuses.id
LEFT JOIN shift_types ON employees.emp_shift = shift_types.id
LEFT JOIN branches ON employees.emp_location = branches.id
LEFT JOIN departments ON employees.emp_department = departments.id
LEFT JOIN job_titles ON employees.emp_job_code = job_titles.id
LEFT JOIN job_categories ON employees.job_category_id = job_categories.id
";

try {
    echo json_encode(
        SSP::simple($_POST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
    );
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>