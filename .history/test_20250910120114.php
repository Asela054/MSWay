<?php
$servername = "localhost";
$username = "root";
$password = "asela123";
$databse = "erav_cosmetics";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $databse);
$conn -> set_charset("utf8");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$resentID="";

date_default_timezone_set('Asia/Colombo');

$updatedatetime=date('Y-m-d h:i:s');
$filename='employee.csv';

$file = fopen($filename, 'r');
$i=1;
while (($line = fgetcsv($file)) !== FALSE) {
    // print_r($line);
    $employeID='000'.$i;
    $employeID=substr($employeID, -4);

    $epf=$line[0];
    $name=$line[1];
    $nic=$line[2];
    $join=$line[3];
    $job=$line[4];
    $adress=$line[5];
    $dob=$line[6];
    $company=$line[7];

    $insert="INSERT INTO `employees`(`emp_id`, `emp_fp_id`, `emp_etfno`, `emp_name_with_initial`, `emp_first_name`, `emp_med_name`, `emp_last_name`, `emp_nick_name`, `emp_status`, `emp_birthday`, `emp_gender`, `emp_marital_status`, `emp_nationality`, `emp_salary_grade`, `emp_join_date`, `emp_permanent_date`, `emp_assign_date`, `emp_address`, `emp_address_2`, `emp_national_id`, `emp_con_mobile`, `emp_work_telephone`, `emp_mobile`, `emp_drive_license`, `emp_license_expire_date`, `emp_work_phone_no`, `emp_email`, `emp_other_email`, `emp_home_no`, `emp_location`, `emp_shift`, `emp_city`, `emp_province`, `emp_country`, `emp_postal_code`, `emp_job_code`, `emp_company`, `deleted`, `modified_user_id`, `created_by`, `created_at`, `updated_at`) VALUES ('$employeID','0','$epf','$name','','','','','1','$dob','','','','','$join','','','$adress','','$nic','','','','','','','','','','$company','','','','','','$job','$company','0','','','','')";
    $conn->query($insert);

    $i++;
}
fclose($file);