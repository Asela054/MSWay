<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\SalaryAdvance;
use Auth;
use Carbon\Carbon;
use Datatables;

class SalaryAdvanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('salary-advance-list');

        if(!$permission) {
            abort(403);
        }

        return view('Payroll.salaryAdvance.salaryAdvance_list');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('salary-advance-create');
        // Employees may create advances for themselves
        $isSelf = ($user->emp_id == $request->input('employee'));

        if (!$permission && !$isSelf) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $availableResponse = $this->getAvailableAmount($request->input('employee'), $request->input('date'));
        $availableData = json_decode($availableResponse->getContent(), true);

        if (isset($availableData['errors'])) {
            return response()->json(['errors' => $availableData['errors']]);
        }

        $available_amount = $availableData['available_amount'] ?? 0;

        if ($request->input('request_amount') > $available_amount) {
            return response()->json(['errors' => 'Amount exceeds the available advance limit of ' . $available_amount]);
        }

        $advance = new SalaryAdvance;
        $advance->emp_id = $request->input('employee');
        $advance->date = $request->input('date');
        $advance->request_amount = $request->input('request_amount');
        $advance->paid_amount = 0;
        $advance->remark = $request->input('remark');
        $advance->status = '1';
        $advance->paid_status = '0';
        $advance->created_by = Auth::id();
        $advance->created_at = Carbon::now()->toDateTimeString();

        $advance->save();

        return response()->json(['success' => 'Salary Advance Added successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('salary-advance-edit');
        // Employees may edit their own records
        $record = DB::table('salary_advances')->where('id', $id)->value('emp_id');
        $isSelf = ($user->emp_id == $record);

        if (!$permission && !$isSelf) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = DB::table('salary_advances')
                ->leftjoin('employees', 'salary_advances.emp_id', '=', 'employees.emp_id')
                ->select(
                    'salary_advances.*', 
                    'employees.emp_name_with_initial as employee_name',
                    'salary_advances.emp_id as employee_id' 
                )
                ->where('salary_advances.id', $id)
                ->first();
            
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, SalaryAdvance $advance)
    {
        $user = auth()->user();
        $permission = $user->can('salary-advance-edit');
        // Employees may update their own records
        $isSelf = ($user->emp_id == $request->employee);

        if (!$permission && !$isSelf) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $availableResponse = $this->getAvailableAmount($request->employee, $request->date);
        $availableData = json_decode($availableResponse->getContent(), true);

        if (isset($availableData['errors'])) {
            return response()->json(['errors' => $availableData['errors']]);
        }

        $available_amount = $availableData['available_amount'] ?? 0;

        if ($request->request_amount > $available_amount) {
            return response()->json(['errors' => 'Amount exceeds the available advance limit of ' . $available_amount]);
        }

        $form_data = array(
            'emp_id'     => $request->employee,
            'date'       => $request->date,
            'request_amount'     => $request->request_amount,
            'remark'     => $request->remark,
            'updated_by' => Auth::id(),
            'updated_at' => Carbon::now()->toDateTimeString()
        );

        SalaryAdvance::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Salary Advance is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('salary-advance-delete');
        // Employees may delete their own unapproved records
        $record = DB::table('salary_advances')->where('id', $id)->value('emp_id');
        $isSelf = ($user->emp_id == $record);

        if (!$permission && !$isSelf) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = SalaryAdvance::findOrFail($id);
        $data->status = 3;
        $data->save();
        
        return response()->json(['success' => 'Deleted successfully']);
    }

    public function getAvailableAmount($emp_id, $request_date = null)
    {
        // If called via HTTP GET, pick up the date query param
        if ($request_date === null) {
            $request_date = request()->query('date');
        }

        // Use today's date as fallback for month/year context
        $referenceDate = $request_date ? Carbon::parse($request_date) : Carbon::now();

        // Get employee's job_category
        $employee = DB::table('employees')
            ->leftJoin('job_categories', 'employees.job_category_id', '=', 'job_categories.id')
            ->select(
                'job_categories.salary_advance_type',
                'job_categories.salary_advance_value',
                'job_categories.salary_advance_min_date',
                'employees.emp_id as emp_id',
                'employees.id as employee_id'
            )
            ->where('employees.emp_id', $emp_id)
            ->first();

        if (!$employee) {
            return response()->json(['available_amount' => 0]);
        }

        // Check minimum attendance days in the requested month
        if (!is_null($employee->salary_advance_min_date) && $employee->salary_advance_min_date > 0) {
            $monthStart = $referenceDate->copy()->startOfMonth()->toDateString();
            $selectedDate = $referenceDate->toDateString();

            $attendedDays = DB::table('attendances')
                ->where('emp_id', $employee->emp_id)
                ->whereBetween('date', [$monthStart, $selectedDate])
                ->distinct('date')
                ->count('date');

            if ($attendedDays < $employee->salary_advance_min_date) {
                return response()->json([
                    'available_amount' => 0,
                    'errors' => 'Minimum attendance of ' . $employee->salary_advance_min_date . ' day(s) not reached up to the selected date. Current attendance: ' . $attendedDays . ' day(s).'
                ]);
            }
        }

        // Get basic salary
        $payroll = DB::table('payroll_profiles')
            ->leftJoin('remuneration_profiles AS rp1', function($join) {
                $join->on('rp1.payroll_profile_id', '=', 'payroll_profiles.id')
                    ->where('rp1.remuneration_id', '=', 2);
            })
            ->leftJoin('remuneration_profiles AS rp2', function($join) {
                $join->on('rp2.payroll_profile_id', '=', 'payroll_profiles.id')
                    ->where('rp2.remuneration_id', '=', 26);
            })
            ->select(
                DB::raw('(payroll_profiles.basic_salary + IFNULL(rp1.new_eligible_amount, 0) + IFNULL(rp2.new_eligible_amount, 0)) as total_basic')
            )
            ->where('payroll_profiles.emp_id', $employee->employee_id)
            ->first();

        $basic_salary = $payroll ? $payroll->total_basic : 0;

        if ($employee->salary_advance_type == 1) {
            $available_amount = ($basic_salary * $employee->salary_advance_value) / 100;
        } else {
            $available_amount = $employee->salary_advance_value;
        }

        return response()->json(['available_amount' => round($available_amount, 2)]);
    }

    public function getPaidAmount(Request $request)
    {
        $id = $request->query('id');
        $data = DB::table('salary_advances')->where('id', $id)->first();

        if (!$data) {
            return response()->json(['error' => 'Record not found'], 404);
        }

        return response()->json(['paid_amount' => $data->paid_amount]);
    }

    public function storePaidAmount(Request $request)
    {
        $id = $request->input('recordID');
        $paid_amount = $request->input('paid_amount');

        $updated = DB::table('salary_advances')->where('id', $id)->update([
            'paid_amount'  => $paid_amount,
            'paid_status'  => 1,
            'updated_by'   => Auth::id(),
            'updated_at'   => Carbon::now()->toDateTimeString(),
        ]);

        if ($updated) {
            return response()->json(['success' => 'Paid amount saved successfully.']);
        }

        return response()->json(['errors' => ['Failed to update paid amount.']]);
    }

    public function dpt_allocation_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('salary-advance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department = $request->input('department');

        $emp_List = DB::table('employees')
            ->select(
                'emp_id',
                'emp_name_with_initial'
            )
            ->where('emp_department', $department)
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->orderBy('emp_name_with_initial')
            ->get();

        return response()->json(['employees' => $emp_List]);
    }

    public function dpt_allocation_insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('salary-advance-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            DB::beginTransaction();

            $allocation_date = $request->input('allocation_date');
            $tableData       = $request->input('tableData', []);

            if (empty($tableData)) {
                return response()->json(['errors' => 'No employee data provided.']);
            }

            $errors = [];

            foreach ($tableData as $row) {
                $emp_id         = $row['emp_id'];
                $request_amount = floatval($row['request_amount'] ?? 0);
                $remark         = $row['remark'] ?? '';

                // Server-side re-validate available amount and attendance
                $availableResponse = $this->getAvailableAmount($emp_id, $allocation_date);
                $availableData     = json_decode($availableResponse->getContent(), true);

                if (isset($availableData['errors'])) {
                    $errors[] = 'Employee ' . $emp_id . ': ' . $availableData['errors'];
                    continue;
                }

                $available_amount = floatval($availableData['available_amount'] ?? 0);

                if ($request_amount <= 0) {
                    $errors[] = 'Employee ' . $emp_id . ': Request amount must be greater than zero.';
                    continue;
                }

                if ($request_amount > $available_amount) {
                    $errors[] = 'Employee ' . $emp_id . ': Request amount exceeds available limit of ' . $available_amount . '.';
                    continue;
                }

                $advance                = new SalaryAdvance();
                $advance->emp_id        = $emp_id;
                $advance->date          = $allocation_date;
                $advance->request_amount = $request_amount;
                $advance->paid_amount   = $request_amount;
                $advance->remark        = $remark;
                $advance->status        = '1';
                $advance->paid_status   = '1';
                $advance->created_by    = Auth::id();
                $advance->updated_by    = 0;
                $advance->created_at    = Carbon::now()->toDateTimeString();
                $advance->save();
            }

            DB::commit();

            if (!empty($errors)) {
                return response()->json([
                    'success' => 'Salary advances saved. Some rows had issues.',
                    'row_errors' => $errors,
                ]);
            }

            return response()->json(['success' => 'Department salary advances saved successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => 'An error occurred: ' . $e->getMessage()], 422);
        }
    }
}
