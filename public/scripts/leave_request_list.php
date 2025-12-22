<?php
session_start();
// Include the EmployeeHelper class
use App\Helpers\EmployeeHelper;
use App\Helpers\UserHelper;

// Correct path resolution for Laravel - use base path or proper autoloading
require_once __DIR__ . '/../../app/Helpers/EmployeeHelper.php';
require_once __DIR__ . '/../../app/Helpers/UserHelper.php';

// DB table to use
$table = 'leave_request';

// Table's primary key
$primaryKey = 'id';

    $columns = array(
        array('db' => '`u`.`id`', 'dt' => 'id', 'field' => 'id'),
        array('db' => '`u`.`emp_id`', 'dt' => 'emp_id', 'field' => 'emp_id','visible' => false),
        array('db' => '`u`.`emp_name_with_initial`', 'dt' => 'emp_name_with_initial', 'field' => 'emp_name_with_initial','visible' => false),
        array('db' => '`u`.`name`', 'dt' => 'dep_name', 'field' => 'name'),
        array('db' => '`u`.`leave_type`', 'dt' => 'leave_type', 'field' => 'leave_type'),
        array('db' => '`u`.`leave_from`', 'dt' => 'from_date', 'field' => 'leave_from'),
        array('db' => '`u`.`leave_to`', 'dt' => 'to_date', 'field' => 'leave_to'),
        array('db' => '`u`.`half_short`', 'dt' => 'half_short', 'field' => 'half_short'),
        array('db' => '`u`.`status`', 'dt' => 'leave_status', 'field' => 'status'),
        array('db' => '`u`.`approvestatus`', 'dt' => 'approvestatus', 'field' => 'approvestatus'),
        array('db' => '`u`.`leave_category`', 'dt' => 'leave_category', 'field' => 'leave_category'),
        array('db' => '`u`.`reason`', 'dt' => 'reason', 'field' => 'reason'),
        array( 'db' => '`u`.`calling_name`', 'dt' => 'calling_name', 'field' => 'calling_name','visible' => false ),
        array('db' => '`u`.`emp_id`', 'dt' => 'employee_display', 'field' => 'emp_id', 
          'formatter' => function($d, $row) {
              $employee = (object)[
                  'emp_name_with_initial' => $row['emp_name_with_initial'],
                  'calling_name' => $row['calling_name'],
                  'emp_id' => $row['emp_id']
              ];
              
              return EmployeeHelper::getDisplayName($employee);
          }
    )
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

    $sql = "SELECT 
        `leave_request`.`id`,
        `emp`.`emp_id`,
        `emp`.`emp_name_with_initial`,
        `emp`.`calling_name`,
        `departments`.`name`,
        `leave_types`.`leave_type`,
        `leave_request`.`from_date` AS `leave_from`,
        `leave_request`.`to_date` AS `leave_to`,
        `leave_request`.`request_approve_status` AS `approvestatus`,
        `leave_request`.`leave_category`,
        `leave_request`.`reason`,
        `leaves`.`half_short`,
        `leaves`.`status`
    FROM `leave_request`
    JOIN `employees` AS `emp` ON `leave_request`.`emp_id` = `emp`.`emp_id`
    LEFT JOIN `departments` ON `emp`.`emp_department` = `departments`.`id`
    LEFT JOIN `leaves` ON `leave_request`.`id` = `leaves`.`request_id`
    LEFT JOIN `leave_types` ON `leaves`.`leave_type` = `leave_types`.`id`
    WHERE `leave_request`.`status` = 1";

    if (!empty($_REQUEST['department'])) {
        $department = $_REQUEST['department'];
        $sql .= " AND `departments`.`id` = '$department'";
    }

    if (!empty($_REQUEST['employee'])) {
        $employee = $_REQUEST['employee'];
        $sql .= " AND `emp`.`emp_id` = '$employee'";
    }

    if (!empty($_REQUEST['from_date']) && !empty($_REQUEST['to_date'])) {
        $from_date = $_REQUEST['from_date'];
        $to_date = $_REQUEST['to_date'];
        $sql .= " AND `leave_request`.`from_date` BETWEEN '$from_date' AND '$to_date'";
    }

    
// new filter based on user access rightsd
    $userId = UserHelper::getLoggedInUserId();

    if ($userId) {
        $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
        
        if ($mysqli->connect_error) {
            echo json_encode(['error' => 'Database connection failed']);
            exit;
        }
        
        $accessibleEmployeeIds = UserHelper::getAccessibleEmployeeIds($userId, $mysqli);
    if (!empty($accessibleEmployeeIds)) {
        $empIds = implode(',', array_map('intval', $accessibleEmployeeIds));
        $sql .= " AND `emp`.`emp_id` IN ($empIds)";
    } else {
        $sql .= " AND 1 = 0";
    }
    $mysqli->close();
}
// end of new filter



    $joinQuery = "FROM (" . $sql . ") as `u`";
    $extraWhere = "";


    echo json_encode(SSP::simple($_REQUEST, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere));
    ?>