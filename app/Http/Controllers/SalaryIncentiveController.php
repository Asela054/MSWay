<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\SalaryIncentive;
use Auth;
use Carbon\Carbon;

class SalaryIncentiveController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Convert a "yyyy-mm" or "yyyy-mm-dd" string to the first day of that month.
     */
    private function toFirstOfMonth($monthInput)
    {
        if (!$monthInput) return null;
        return Carbon::parse($monthInput)->startOfMonth()->toDateString();
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('salary-incentive-list');

        if(!$permission) {
            abort(403);
        }

        return view('Payroll.salaryIncentive.salaryIncentive_list');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('salary-incentive-create');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $month = $this->toFirstOfMonth($request->input('month'));

        // If a record already exists for this emp + month, update it instead
        $existing = SalaryIncentive::where('emp_id', $request->input('employee'))
            ->where('month', $month)
            ->where('status', '!=', 3)
            ->first();

        if ($existing) {
            $existing->paid_amount = $request->input('paid_amount');
            $existing->remark      = $request->input('remark');
            $existing->updated_by  = Auth::id();
            $existing->updated_at  = Carbon::now()->toDateTimeString();
            $existing->save();
            return response()->json(['success' => 'Salary Incentive updated successfully.']);
        }

        $incentive             = new SalaryIncentive;
        $incentive->emp_id     = $request->input('employee');
        $incentive->month      = $month;
        $incentive->paid_amount = $request->input('paid_amount');
        $incentive->remark     = $request->input('remark');
        $incentive->status     = '1';
        $incentive->paid_status = '1';
        $incentive->created_by = Auth::id();
        $incentive->created_at = Carbon::now()->toDateTimeString();
        $incentive->save();

        return response()->json(['success' => 'Salary Incentive Added successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('salary-incentive-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = DB::table('salary_incentives')
                ->leftjoin('employees', 'salary_incentives.emp_id', '=', 'employees.emp_id')
                ->select(
                    'salary_incentives.*',
                    'employees.emp_name_with_initial as employee_name',
                    'salary_incentives.emp_id as employee_id'
                )
                ->where('salary_incentives.id', $id)
                ->first();

            // Format month as "yyyy-mm" for the HTML month input field
            if ($data && $data->month) {
                $data->month = Carbon::parse($data->month)->format('Y-m');
            }

            return response()->json(['result' => $data]);
        }
    }

    /**
     * Return existing salary incentive for a given employee + month (used by formModal auto-load).
     */
    public function getByEmpMonth(Request $request)
    {
        $emp_id = $request->input('emp_id');
        $month  = $this->toFirstOfMonth($request->input('month'));

        if (!$emp_id || !$month) {
            return response()->json(['result' => null]);
        }

        $record = DB::table('salary_incentives')
            ->where('emp_id', $emp_id)
            ->where('month', $month)
            ->where('status', '!=', 3)
            ->first();

        return response()->json(['result' => $record]);
    }

    public function update(Request $request, SalaryIncentive $incentive)
    {
        $user = auth()->user();
        $permission = $user->can('salary-incentive-edit');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $form_data = array(
            'emp_id'      => $request->employee,
            'month'       => $this->toFirstOfMonth($request->month),
            'paid_amount' => $request->paid_amount,
            'remark'      => $request->remark,
            'updated_by'  => Auth::id(),
            'updated_at'  => Carbon::now()->toDateTimeString()
        );

        SalaryIncentive::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Salary Incentive is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('salary-incentive-delete');

        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = SalaryIncentive::findOrFail($id);
        $data->status = 3;
        $data->save();
        
        return response()->json(['success' => 'Deleted successfully']);
    }

    public function dpt_allocation_list(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('salary-incentive-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $department       = $request->input('department');
        $allocation_month = $this->toFirstOfMonth($request->input('allocation_month'));

        $emp_list = DB::table('employees')
            ->select('emp_id', 'emp_name_with_initial')
            ->where('emp_department', $department)
            ->where('deleted', 0)
            ->where('is_resigned', 0)
            ->orderBy('emp_name_with_initial')
            ->get();

        // Attach existing salary_incentives data for this month
        $emp_ids = $emp_list->pluck('emp_id')->toArray();
        $existing = [];
        if (!empty($emp_ids) && $allocation_month) {
            $records = DB::table('salary_incentives')
                ->whereIn('emp_id', $emp_ids)
                ->where('month', $allocation_month)
                ->where('status', '!=', 3)
                ->get()
                ->keyBy('emp_id');
            foreach ($records as $eid => $rec) {
                $existing[$eid] = $rec;
            }
        }

        $employees = $emp_list->map(function ($emp) use ($existing) {
            $rec = $existing[$emp->emp_id] ?? null;
            $emp->has_record   = $rec ? true : false;
            $emp->paid_amount  = $rec ? $rec->paid_amount : null;
            $emp->remark       = $rec ? $rec->remark : null;
            $emp->incentive_id = $rec ? $rec->id : null;
            return $emp;
        });

        return response()->json(['employees' => $employees]);
    }

    public function dpt_allocation_insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('salary-incentive-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        try {
            DB::beginTransaction();

            $allocation_month = $this->toFirstOfMonth($request->input('allocation_month'));
            $tableData        = $request->input('tableData', []);

            if (empty($tableData)) {
                return response()->json(['errors' => 'No employee data provided.']);
            }

            $errors = [];

            foreach ($tableData as $row) {
                $emp_id      = $row['emp_id'];
                $paid_amount = floatval($row['paid_amount'] ?? 0);
                $remark      = $row['remark'] ?? '';

                if ($paid_amount <= 0) {
                    $errors[] = 'Employee ' . $emp_id . ': Paid amount must be greater than zero.';
                    continue;
                }

                // Update existing record or create new
                $existing = SalaryIncentive::where('emp_id', $emp_id)
                    ->where('month', $allocation_month)
                    ->where('status', '!=', 3)
                    ->first();

                if ($existing) {
                    $existing->paid_amount = $paid_amount;
                    $existing->remark      = $remark;
                    $existing->updated_by  = Auth::id();
                    $existing->updated_at  = Carbon::now()->toDateTimeString();
                    $existing->save();
                } else {
                    $incentive              = new SalaryIncentive();
                    $incentive->emp_id      = $emp_id;
                    $incentive->month       = $allocation_month;
                    $incentive->paid_amount = $paid_amount;
                    $incentive->remark      = $remark;
                    $incentive->status      = '1';
                    $incentive->paid_status = '1';
                    $incentive->created_by  = Auth::id();
                    $incentive->updated_by  = 0;
                    $incentive->created_at  = Carbon::now()->toDateTimeString();
                    $incentive->save();
                }
            }

            DB::commit();

            if (!empty($errors)) {
                return response()->json([
                    'success'    => 'Salary incentives saved. Some rows had issues.',
                    'row_errors' => $errors,
                ]);
            }

            return response()->json(['success' => 'Department salary incentives saved successfully.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => 'An error occurred: ' . $e->getMessage()], 422);
        }
    }
}
