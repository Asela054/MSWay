<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'employee_loans';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array( 'db' => '`udt`.`employee_loan_id`', 'dt' => 'employee_loan_id', 'field' => 'employee_loan_id' ),
	array( 'db' => '`udt`.`installment_cancel`', 'dt' => 'installment_cancel', 'field' => 'installment_cancel' ),
	array( 'db' => '`udt`.`payroll_profile_id`', 'dt' => 'payroll_profile_id', 'field' => 'payroll_profile_id' ),
	array( 'db' => '`udt`.`installment_id`', 'dt' => 'installment_id', 'field' => 'installment_id' ),
	array( 'db' => '`udt`.`emp_first_name`', 'dt' => 'emp_first_name', 'field' => 'emp_first_name' ),
	array( 'db' => '`udt`.`location`', 'dt' => 'location', 'field' => 'location' ),
	array( 'db' => '`udt`.`loan_name`', 'dt' => 'loan_name', 'field' => 'loan_name' ),
	array( 'db' => '`udt`.`loan_amount`', 'dt' => 'loan_amount', 'field' => 'loan_amount' ),
	array( 'db' => '`udt`.`loan_paid`', 'dt' => 'loan_paid', 'field' => 'loan_paid' ),
	array( 'db' => '`udt`.`loan_balance`', 'dt' => 'loan_balance', 'field' => 'loan_balance' )
);

// SQL server connection information
require('config.php');
$sql_details = array(
	'user' => $db_username,
	'pass' => $db_password,
	'db'   => $db_name,
	'host' => $db_host
);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

// require( 'ssp.class.php' );
require('ssp.customized.class.php' );


$joinQuery = "from (SELECT employee_loans.id as employee_loan_id, employee_loan_installments.id as installment_id, payroll_profiles.id as payroll_profile_id, employees.emp_name_with_initial AS emp_first_name, branches.name as location, employee_loans.loan_name, employee_loans.loan_amount, IFNULL(drv_prog.loan_paid, 0) AS loan_paid, (employee_loans.loan_amount-IFNULL(drv_prog.loan_paid, 0)) AS loan_balance, IFNULL(employee_loan_installments.installment_cancel, 1) AS installment_cancel FROM employee_loans INNER JOIN payroll_profiles ON employee_loans.payroll_profile_id=payroll_profiles.id INNER JOIN employees ON payroll_profiles.emp_id=employees.id INNER JOIN companies AS branches ON employees.emp_company=branches.id LEFT OUTER JOIN (SELECT payroll_profile_id, MAX(emp_payslip_no) AS emp_payslip_no FROM employee_payslips GROUP BY payroll_profile_id) AS employee_payslips ON payroll_profiles.id=employee_payslips.payroll_profile_id LEFT OUTER JOIN (SELECT employee_loan_id, MAX(emp_payslip_no) AS loan_payslip_no, SUM(installment_value) AS loan_paid FROM employee_loan_installments WHERE installment_cancel=0 GROUP BY employee_loan_id) AS drv_prog ON employee_loans.id=drv_prog.employee_loan_id LEFT OUTER JOIN (SELECT id, employee_loan_id, emp_payslip_no, installment_cancel FROM employee_loan_installments) AS employee_loan_installments ON (employee_loans.id=employee_loan_installments.employee_loan_id AND IFNULL(employee_payslips.emp_payslip_no+1, 1)=employee_loan_installments.emp_payslip_no) WHERE (employee_loans.loan_amount>IFNULL(drv_prog.loan_paid, 0) OR IFNULL(drv_prog.loan_payslip_no, 1)>IFNULL(employee_payslips.emp_payslip_no, 0))) as udt";

$extraWhere = "";


echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns, $joinQuery, $extraWhere)
);
