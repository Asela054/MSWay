<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ApiLeaveEmailController extends Controller
{
    /**
     * Leave apply karapu passe email eka yawanna.
     * $leave = save karapu Leave model eka.
     * Exception ekak awoth caller ekata throw wenawa (caller eka try/catch karanna).
     */
    public function sendLeaveApplyEmail($leave)
    {
        $employee  = $this->getEmployeeMailInfo($leave->emp_id);
        $covering  = $this->getEmployeeMailInfo($leave->emp_covering);
        $approver  = $this->getEmployeeMailInfo($leave->leave_approv_person);
        $approver2 = $this->getEmployeeMailInfo($leave->leave_approv_person_2);

        // NOTE: table name eka confirm karanna (LeaveType model eke $table)
        $leaveTypeName = DB::table('leave_types')
            ->where('id', $leave->leave_type)
            ->value('leave_type');

        $recipients = array(
            $employee['employee_email'],
            $employee['company_email'],
            $covering['employee_email'],
            $approver['employee_email'],
            $approver2['employee_email'],
            $employee['department_head_email'],
        );

        // empty saha duplicate emails ain karanna
        $recipients = array_unique(array_filter($recipients));

        if (empty($recipients)) {
            return;
        }

        $body = $this->buildLeaveEmailBody($leave, array(
            'employee_name' => $employee['name'],
            'leave_type'    => $leaveTypeName,
            'covering_name' => $covering['name'],
            'approver_name' => $approver['name'],
            'approver2_name' => $approver2['name'],
        ));

        $client = new \GuzzleHttp\Client(array('timeout' => 15));

        $client->post('https://aws.erav.lk/Temp/bf360/eravawsmail.php', array(
            'form_params' => array(
                'inquire_now' => 'HR Department - ' . $employee['company_name'],
                'replyto'     => implode(';', $recipients),
                'contsubj'    => 'Leave Application - ' . $employee['name'],
                'contbody'    => $body,
            ),
        ));
    }

    /**
     * getEmployeeCategory() eke same joins + employee name
     */
    private function getEmployeeMailInfo($empId)
    {
        $info = array(
            'name'                  => '',
            'employee_email'        => '',
            'company_email'         => '',
            'company_name'          => '',
            'department_head_email' => '',
        );

        if (!$empId) {
            return $info;
        }

        $emp = DB::table('employees')
            ->leftJoin('companies', 'companies.id', '=', 'employees.emp_company')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->leftJoin('employees as dept_heads', 'dept_heads.emp_id', '=', 'departments.dep_head_emp_id')
            ->select(
                'employees.emp_name_with_initial AS name',
                'employees.emp_email AS employee_email',
                'companies.email AS company_email',
                'companies.name AS company_name',
                'dept_heads.emp_email AS department_head_email'
            )
            ->where('employees.emp_id', $empId)
            ->first();

        if ($emp) {
            $info['name']                  = (string) $emp->name;
            $info['employee_email']        = (string) $emp->employee_email;
            $info['company_email']         = (string) $emp->company_email;
            $info['company_name']          = (string) $emp->company_name;
            $info['department_head_email'] = (string) $emp->department_head_email;
    }

        return $info;
    }

    /**
     * Web eke generateEmailBody() eke server side version eka
     */
    private function buildLeaveEmailBody($leave, $names)
    {
        $durations = array(
            '0.25' => 'Short Leave',
            '0.5'  => 'Half Day',
            '0.50' => 'Half Day',
            '1'    => 'Full Day',
            '1.00' => 'Full Day',
        );
        $key = (string) $leave->half_short;
        $duration = isset($durations[$key]) ? $durations[$key] : '';

        $rows = array(
            'Employee'          => $names['employee_name'],
            'Employee ID'       => $leave->emp_id,
            'Leave Type'        => $names['leave_type'],
            'Leave Duration'    => $duration,
            'From Date'         => $leave->leave_from,
            'To Date'           => $leave->leave_to,
            'Number of Days'    => $leave->no_of_days,
            'Reason'            => $leave->reson,
            'Covering Employee' => $names['covering_name'],
            'Approving Person'  => $names['approver_name'],
            'Approving Person 2' => $names['approver2_name'],
        );

        $body  = '<p>Dear Sir/Madam,</p>';
        $body .= '<p>A leave application has been submitted with the following details:</p>';
        $body .= '<table cellpadding="6" cellspacing="0" style="border-collapse:collapse;font-family:Arial,sans-serif;font-size:14px;">';

        foreach ($rows as $label => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $body .= '<tr>'
                . '<td style="border:1px solid #ccc;background:#f5f5f5;"><b>' . e($label) . '</b></td>'
                . '<td style="border:1px solid #ccc;">' . e($value) . '</td>'
                . '</tr>';
        }

        $body .= '</table>';
        $body .= '<p>Please review this request in the HR system.</p>';
        $body .= '<p>Regards,<br>' . e($names['employee_name'] ? $names['employee_name'] : 'Employee') . '</p>';

        return $body;
    }
}