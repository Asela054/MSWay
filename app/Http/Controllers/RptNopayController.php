<?php

namespace App\Http\Controllers;

use App\Employee;
use App\EmployeePaySlip;
use App\Helpers\EmployeeHelper;
use App\Leave;
use App\PayrollProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;

class RptNopayController extends Controller
{
    public function no_pay_report(){
        $permission = Auth::user()->can('no-pay-report');
        if(!$permission){
            abort(403);
        }

        return view('Report.no_pay_report' );
    }

    public function get_no_pay_amount($emp_etfno, $emp_work, $emp_leave, $emp_nopay, $emp_ot_i, $emp_ot_ii  ){

        $sql_info = "SELECT payroll_profiles.id as payroll_profile_id, payroll_profiles.basic_salary, payroll_profiles.day_salary, payroll_process_types.pay_per_day 
                FROM `payroll_profiles` inner join payroll_process_types on payroll_profiles.payroll_process_type_id=payroll_process_types.id WHERE payroll_profiles.emp_etfno=?";
        $profiles = DB::select($sql_info, [$emp_etfno]);//70

        if(empty($profiles)){
            return 0;
        }

        $employeePayslip = EmployeePayslip::where(['payroll_profile_id'=>$profiles[0]->payroll_profile_id])
            ->latest()
            ->first();
        $emp_payslip_no = empty($employeePayslip)?1:($employeePayslip->emp_payslip_no+1);

        //
        //-2022-12-02-exemptions
        //
        $fig_ex_grp = array('NOPAY'=>0, 'OTHRS'=>0);
        $sql_exemptions="select remuneration_exemptions.exemption_fig_group_title, sum(drv_termpay.payment_amount) as exemption_fig_group_total from (SELECT remuneration_id, payment_amount FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS drv_termpay INNER JOIN remunerations ON drv_termpay.remuneration_id=remunerations.id inner join remuneration_exemptions on remunerations.id=remuneration_exemptions.remuneration_id group by remuneration_exemptions.exemption_fig_group_title";
        $exemption_list=DB::select($sql_exemptions, [$profiles[0]->payroll_profile_id, $emp_payslip_no]);
        foreach($exemption_list as $el){
            $fig_ex_grp[$el->exemption_fig_group_title]=$el->exemption_fig_group_total;
        }
        //
        //-2022-12-02-
        //

        /**/
        $sql_main="SELECT fig_name, fig_group, fig_group_title, fig_base_ratio, fig_value, fig_hidden, epf_payable, remuneration_pssc
                FROM (SELECT drv_figs.fig_name, drv_figs.fig_group, drv_figs.fig_group_title, drv_figs.fig_value AS fig_base_ratio,
                             COALESCE(NULLIF(drv_figs.fig_value*(((drv_figs.fig_group='FIXED') * ? * ?) + ((drv_figs.fig_group='FIXED') * (1 - ?)) +
                                                                 (? * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) + (? * drv_figs.work_payable * (drv_figs.fig_group='BASIC')) +
                                                                 (? * drv_figs.nopay_payable * (drv_figs.fig_group='BASIC')) + (? * (drv_figs.fig_group='OTHRS1')) + (? * (drv_figs.fig_group='OTHRS2')))
                                                 *drv_figs.pay_per_day, 0), (drv_figs.fig_value*drv_figs.fig_revise)) AS fig_value, 
                             drv_figs.fig_hidden, drv_figs.epf_payable, drv_figs.remuneration_pssc FROM (SELECT 'Basic' AS fig_name,
                                                                'BASIC' AS fig_group, 'BASIC' AS fig_group_title, COALESCE(NULLIF(CAST(?*? AS DECIMAL(10,2)), 0), ?) AS fig_value, ? AS pay_per_day, 
                                                        1 AS fig_revise, 0 AS fig_hidden, 1 AS epf_payable, 1 AS work_payable, 1 AS nopay_payable, 'BASIC' AS remuneration_pssc
                             UNION ALL SELECT 'No pay' AS fig_name, 'BASIC' AS fig_group, 'NOPAY' AS fig_group_title, ? AS fig_value, 
                                                                                                                                                                                                                                                                                                                                                                                                                   1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 1 AS nopay_payable, 'NOPAY' AS remuneration_pssc UNION ALL SELECT 'Normal OT' AS fig_name, 'OTHRS1' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS1' AS remuneration_pssc UNION ALL SELECT 'Double OT' AS fig_name, 'OTHRS2' AS fig_group, 'OTHRS' AS fig_group_title, ? AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, 0 AS epf_payable, 0 AS work_payable, 0 AS nopay_payable, 'OTHRS2' AS remuneration_pssc UNION ALL select drv_allfacility.remuneration_name AS fig_name, IFNULL(drv_allfacility.fig_group, 'BASIC') AS fig_group, 'FACILITY' AS fig_group_title, (IFNULL(drv_dayfacility.pre_eligible_amount, drv_empfacility.new_eligible_amount)*drv_allfacility.value_group) AS fig_value, 1 AS pay_per_day, 0 AS fig_revise, 0 AS fig_hidden, drv_allfacility.epf_payable, 1 AS work_payable, 0 AS nopay_payable, drv_allfacility.pssc AS remuneration_pssc from (SELECT `remuneration_id`, `new_eligible_amount` FROM `remuneration_profiles` WHERE `payroll_profile_id`=? AND `remuneration_signout`=0) AS drv_empfacility INNER JOIN (SELECT id, remuneration_name, remuneration_type, value_group, epf_payable, allocation_method AS fig_group, payslip_spec_code AS pssc FROM remunerations WHERE allocation_method='FIXED' AND remuneration_cancel=0) AS drv_allfacility ON drv_empfacility.remuneration_id=drv_allfacility.id LEFT OUTER JOIN (SELECT remuneration_id, pre_eligible_amount, 'FIXED' AS fig_group FROM remuneration_eligibility_days WHERE ? BETWEEN min_days AND max_days) AS drv_dayfacility ON drv_allfacility.id=drv_dayfacility.remuneration_id) AS drv_figs UNION ALL SELECT drv_docs.fig_name, drv_docs.fig_group, drv_docs.fig_group_title, drv_docs.fig_value AS fig_base_ratio, drv_docs.fig_value, drv_docs.fig_hidden, drv_docs.epf_payable, drv_docs.remuneration_pssc FROM (SELECT remunerations.remuneration_name AS fig_name, 'ADDITION' AS fig_group, 'ADDITION' AS fig_group_title, (employee_term_payments.payment_amount*remunerations.value_group) AS fig_value, 0 AS fig_hidden, remunerations.epf_payable, remunerations.payslip_spec_code AS remuneration_pssc FROM (SELECT remuneration_id, payment_amount FROM employee_term_payments WHERE payroll_profile_id=? AND emp_payslip_no=? AND payment_cancel=0) AS employee_term_payments INNER JOIN remunerations ON employee_term_payments.remuneration_id=remunerations.id) AS drv_docs) AS drv_main";
        $employee = DB::select($sql_main, [$emp_work, $profiles[0]->pay_per_day, $profiles[0]->pay_per_day, $emp_work, $emp_leave, $emp_nopay, $emp_ot_i, $emp_ot_ii, $profiles[0]->day_salary, $profiles[0]->pay_per_day, $profiles[0]->basic_salary, $profiles[0]->pay_per_day, ($profiles[0]->day_salary*-1), ($profiles[0]->day_salary/8), (($profiles[0]->day_salary*1)/8), $profiles[0]->payroll_profile_id, $emp_work, $profiles[0]->payroll_profile_id, $emp_payslip_no]);


        $figs_list = array();
        $epf_payable_tot = 0;

        foreach($employee as $r){
            if($r->epf_payable){
                $epf_payable_tot += $r->fig_value;
            }

            if(!isset($figs_list[$r->remuneration_pssc])){
                $figs_list[$r->remuneration_pssc]=array(
                    'fig_grp_title'=>$r->fig_group_title,
                    'fig_val'=>0,
                    'fig_base_rate'=>$r->fig_base_ratio
                );
            }

            $figs_list[$r->remuneration_pssc]['fig_val'] += $r->fig_value;
        }

        $payperiod_workdays=30; $payperiod_holidays=0;
        $payperiod_netdays=($payperiod_workdays-$payperiod_holidays)*-1;

        $reg_keys = array('NOPAY', 'OTHRS1', 'OTHRS2');
        //-2022-12-02
        /*
        $reg_cols = array('NOPAY'=>array('fig_premium'=>1, 'key_param'=>$payperiod_netdays),
            'OTHRS1'=>array('fig_premium'=>1.5, 'key_param'=>240),
            'OTHRS2'=>array('fig_premium'=>2, 'key_param'=>240)
        );
        */
        $reg_cols = array('NOPAY'=>array('fig_premium'=>1, 'key_param'=>$payperiod_netdays, 'fig_ex_key'=>'NOPAY'),
            'OTHRS1'=>array('fig_premium'=>1.5, 'key_param'=>240, 'fig_ex_key'=>'OTHRS'),
            'OTHRS2'=>array('fig_premium'=>2, 'key_param'=>240, 'fig_ex_key'=>'OTHRS')
        );
        //-2022-12-02-

        foreach($figs_list as $k=>$v){
            if(in_array($k, $reg_keys)){
                $units_tot = ($figs_list[$k]['fig_val']/$figs_list[$k]['fig_base_rate']);

                //-2022-12-02
                /*
                $new_base_rate = (($epf_payable_tot*$reg_cols[$k]['fig_premium'])/$reg_cols[$k]['key_param']);
                */
                $act_ex_key = $reg_cols[$k]['fig_ex_key'];
                $fig_ex_val = $fig_ex_grp[$act_ex_key];
                $new_base_rate = ((($epf_payable_tot-$fig_ex_val)*$reg_cols[$k]['fig_premium'])/$reg_cols[$k]['key_param']);
                //2022-12-02-

                $figs_list[$k]['fig_val']=number_format((float)($new_base_rate*$units_tot), 2, '.', '');
                $figs_list[$k]['fig_base_rate']=number_format((float)$new_base_rate, 2, '.', '');
            }
        }

        $data = array(
            'BRA_I'=> $figs_list['BRA_I']['fig_val'],
            'add_bra2'=> $figs_list['add_bra2']['fig_val'],
            'NOPAY'=> $figs_list['NOPAY']['fig_val'],
        );

        return $data;

        //return response()->json(['nopay_val' => $figs_list['NOPAY']['fig_val']]);
        //}
    }

    public function no_pay_report_list_month(Request $request)
    {
        $permission = Auth::user()->can('no-pay-report');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->get('department');
        $employee = $request->get('employee');
        $location = $request->get('location');
        $month = $request->get('month');
        $closedate = date('Y-m-t', strtotime($month));
        $closedate = \Carbon\Carbon::parse($month)->endOfMonth()->format('Y-m-d');

        $emp_query = 'SELECT  
                employees.*,  
                employees.id as emp_auto_id, 
                shift_types.onduty_time, 
                shift_types.offduty_time,
                shift_types.shift_name,
                branches.location as b_location,
                departments.name as dept_name 
                FROM `employees`   
                left join shift_types ON employees.emp_shift = shift_types.id 
                left join branches ON employees.emp_location = branches.id 
                left join departments ON employees.emp_department = departments.id 
                WHERE employees.deleted = 0  
                ';

        if($department != ''){
            $emp_query .= ' AND employees.emp_department = '.$department;
        }

        if($employee != ''){
            $emp_query .= ' AND employees.emp_id = '.$employee;
        }

        if($location != ''){
            $emp_query .= ' AND employees.emp_location = '.$location;
        }

        $emp_query .= ' order by employees.emp_id ';

        $data = DB::select($emp_query);

        //remove emp_id's which doesn't have no pay
        $index = 0;
        foreach ($data as $d){
            $no_pay_days = (new \App\Attendance)->get_no_pay_days($d->emp_id, $month,$closedate);
            if($no_pay_days == 0){
                //remove emp_id from the array
                unset($data[$index]);
            }
            $index++;
        }

        return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('month', function ($row) use ($month) {
                return $month;
            })
            ->addColumn('work_days', function($row) use ($month,$closedate) {
                $work_days = (new \App\Attendance)->get_work_days($row->emp_id, $month,$closedate);
                return $work_days;
            })
            ->addColumn('leave_days', function($row) use ($month,$closedate){
                $leave_days = (new \App\Attendance)->get_leave_days($row->emp_id, $month,$closedate);
                return $leave_days;
            })
            ->addColumn('no_pay_days_data', function ($row) use ($month,$closedate) {
                $no_pay_days = (new \App\Attendance)->get_no_pay_days($row->emp_id, $month,$closedate);

                $work_days = (new \App\Attendance)->get_work_days($row->emp_id, $month,$closedate);
                $leave_days = (new \App\Attendance)->get_leave_days($row->emp_id, $month,$closedate);

                $ot_hours = (new \App\Attendance)->get_ot_hours($row->emp_id, $month);
                $normal_rate_otwork_hrs = $ot_hours['normal_rate_otwork_hrs'];
                $double_rate_otwork_hrs = $ot_hours['double_rate_otwork_hrs'];

                $no_pay_amount_data = $this->get_no_pay_amount($row->emp_id, $work_days, $leave_days, $no_pay_days, $normal_rate_otwork_hrs, $double_rate_otwork_hrs);

                $no_pay_amount = $no_pay_amount_data['NOPAY'];

                //add 2 cols, bra_1, bra_2 Capital
                $BRA_I = $no_pay_amount_data['BRA_I'];
                $add_bra2 = $no_pay_amount_data['add_bra2'];

                //convert no_pay_amount to unsigned integer
                $no_pay_amount = abs($no_pay_amount);

                $basic_salary = '0.00';

                $emp_salary_info = PayrollProfile::where('emp_etfno', $row->emp_id)->first();
                if(!empty($emp_salary_info)){
                    $basic_salary = $emp_salary_info->basic_salary;
                }

                $view_no_pay_days_btn = $no_pay_days.' <a href="javascript:void(0)" class="btn btn-xs btn-primary view_no_pay_days" data-id="'.$row->emp_id.'" data-month="'.$month.'" data-amount="'.$no_pay_amount.'" data-basic="'.$basic_salary.'">View</a>';

                return array(
                    'no_pay_days' => $no_pay_days,
                    'basic_salary' => number_format($basic_salary, 2),
                    'BRA_I' => number_format($BRA_I, 2),
                    'add_bra2' => number_format($add_bra2, 2),
                    'amount' => number_format($no_pay_amount, 2),
                    'view_no_pay_days_btn' => $view_no_pay_days_btn
                );
            })

            ->addColumn('view_no_pay_days_btn', function ($row) use ($month,$closedate) {
                $no_pay_days = (new \App\Attendance)->get_no_pay_days($row->emp_id, $month,$closedate);

                $view_no_pay_days_btn = $no_pay_days;

                if($no_pay_days != 0 ){
                    $view_no_pay_days_btn = $no_pay_days.' <a href="javascript:void(0)" title="View Details" class="btn btn-xs btn-default view_no_pay_days_btn float-right " data-id="'.$row->emp_id.'" data-month="'.$month.'" > <i class="fa fa-eye text-primary"></i> </a>';
                }

                return $view_no_pay_days_btn;
            })
            ->addColumn('employee_display', function ($row) {
                   return EmployeeHelper::getDisplayName($row);
                   
            })
            ->filterColumn('employee_display', function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('employees.emp_name_with_initial', 'like', "%{$keyword}%")
                    ->orWhere('employees.calling_name', 'like', "%{$keyword}%")
                    ->orWhere('employees.emp_id', 'like', "%{$keyword}%");
                });
            })

            ->rawColumns(['action',
                'work_days',
                'month',
                'leave_days',
                'employee_display',
                'no_pay_days_data',
                'view_no_pay_days_btn'
            ])
            ->make(true);
    }

    public function no_pay_days_data(Request $request)
    {
        $permission = Auth::user()->can('no-pay-report');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $emp_id = $request->emp_id;
        $month = $request->month;

         $closedate = date('Y-m-t', strtotime($month));
        $closedate = \Carbon\Carbon::parse($month)->endOfMonth()->format('Y-m-d');

        $no_pay_days = (new \App\Attendance)->get_no_pay_days($emp_id, $month,$closedate);

        $no_pay_days_data = Leave::where('leave_type', 3)
            ->where('emp_id', $emp_id)
            ->where('leave_from', 'like', $month.'%')
            ->where('status', '=', 'Approved')
            ->get();

        $employee = Employee::where('emp_id', $emp_id)->first();

        $no_pay_days_data_html = '<table> 
                                    <tr>
                                        <th> Employee </th>
                                        <td> '. $employee->emp_name_with_initial .' </td>  
                                    </tr> 
                                    <tr>
                                        <th> Month </th>
                                        <td> '. $month .' </td>  
                                    </tr> 
                                    </table>';

        $no_pay_days_data_html .= '<table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Leave From</th>
                                            <th>Leave To</th>
                                            <th>No of Days</th> 
                                        </tr>
                                    </thead>
                                    <tbody>';

        foreach ($no_pay_days_data as $no_pay_day){
            $no_pay_days_data_html .= '<tr>
                                        <td>'.Carbon::parse($no_pay_day->leave_from)->format('d-m-Y').'</td>
                                        <td>'.Carbon::parse($no_pay_day->leave_to)->format('d-m-Y').'</td>
                                        <td>'.$no_pay_day->no_of_days.'</td> 
                                    </tr>';
        }

        $no_pay_days_data_html .= '<tr>
                                        <th colspan="2" class="text-right">Total</th>
                                        <th>'.$no_pay_days.'</th> 
                                    </tr>';

        $no_pay_days_data_html .= '</tbody>
                                </table>';

        return $no_pay_days_data_html;

    }
}
