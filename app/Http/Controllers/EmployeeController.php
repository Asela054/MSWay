<?php

namespace App\Http\Controllers;

use App\Department;
use App\Employee;
use App\JobCategory;
use App\User;
use App\EmployeeDependent;
use App\EmployeeImmigration;
use App\EmployeePicture;
use App\EmploymentStatus;
use App\FingerprintDevice;
use App\Branch;
use App\JobTitle;
use App\JobStatus;
use App\Shift;
use App\ShiftType;
use App\Company;
use App\WorkCategory;
use Carbon\Carbon;
use App\DSDivision;
use App\GNSDivision;
use App\PayrollProfile;
use App\Policestation;
use DB;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Validator;

use Illuminate\Http\Request;
use Session;
use Yajra\Datatables\Datatables;

use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $permission = Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }

        $lastid = DB::table('employees')
            ->latest()
            ->first();
        $employmentstatus = EmploymentStatus::orderBy('id', 'asc')->get();
        $branch = Branch::orderBy('id', 'asc')->get();
        $title = JobTitle::orderBy('id', 'asc')->get();
        $shift_type = ShiftType::where('deleted', 0)->orderBy('id', 'asc')->get();
        $company = Company::orderBy('id', 'asc')->get();
        $departments = Department::orderBy('id', 'asc')->get();

        if (isset($lastid)) {

            $newid = ($lastid->id + 1);
        } else {

            $newid = '0001';
        }
        $device = FingerprintDevice::orderBy('id', 'asc')->where('status', '=', 1)->get();

        return view('Employee.employeeAdd', compact('newid', 'employmentstatus', 'branch', 'device', 'title', 'shift_type', 'company', 'departments'));
    }

    public function employeelist()
    {
        $permission = Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }
        $device = FingerprintDevice::orderBy('id', 'asc')->where('status', '=', 1)->get();
        $employee = DB::table('employees')
            ->join('employment_statuses', 'employees.emp_status', '=', 'employment_statuses.id')
            ->join('branches', 'employees.emp_location', '=', 'branches.id')
            ->select('employees.*', 'employment_statuses.emp_status', 'branches.branch')
            ->get();

        return view('Employee.employeeList', compact('employee', 'device'));
    }

    public function store(Request $request)
    {
        $permission = Auth::user()->can('employee-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $rules = array(
            'emp_id' => 'max:15|unique:employees,emp_id',
            'etfno' => [
                'nullable',
                Rule::unique('employees', 'emp_etfno')
                    ->where('emp_company', $request->input('employeecompany'))
                    ->where('emp_location', $request->input('location'))
                    ->where(function ($query) {
                        $query->where('emp_etfno', '!=', '0')
                            ->whereNotNull('emp_etfno');
                    })
            ],
            'emp_name_with_initial' => 'string|max:255',
            'calling_name' => 'string|max:255',
            'firstname' => 'string|max:255',
            'middlename' => 'max:255',
            'lastname' => 'max:255',
            'emp_id_card' => 'max:12',
            'emp_mobile' => 'max:10',
            'emp_work_telephone' => 'max:10',
            'telephone' => 'max:10',
            'status' => '',
            'photograph' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'location' => '',
            'employeejob' => '',
            'shift' => '',
            'employeecompany' => '',
            'department' => '',
//            'no_of_casual_leaves'  => 'required_if:status,2',
//            'no_of_annual_leaves'  => 'required_if:status,2'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }
        if ($request->hasFile('photograph')) {
            $image = $request->file('photograph');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $image->move($destinationPath, $name);

            $employeepicture = new EmployeePicture;
            $employeepicture->emp_id = $request->input('emp_id');
            $employeepicture->emp_pic_filename = $name;
            $employeepicture->save();

        }
        $Employee = new Employee;
        $Employee->emp_id = $request->input('emp_id');
        $Employee->emp_etfno = $request->input('etfno');
        $Employee->emp_name_with_initial = $request->input('emp_name_with_initial');
        $Employee->calling_name = $request->input('calling_name');
        $Employee->emp_first_name = $request->input('firstname');
        $Employee->emp_med_name = $request->input('middlename');
        $Employee->emp_last_name = $request->input('lastname');
        $Employee->emp_national_id = $request->input('emp_id_card');
        $Employee->emp_mobile = $request->input('emp_mobile');
        $Employee->emp_status = $request->input('status');
        $Employee->emp_location = $request->input('location');
        $Employee->emp_job_code = $request->input('employeejob');
        $Employee->emp_shift = $request->input('shift');
        $Employee->emp_company = $request->input('employeecompany');
        $Employee->emp_department = $request->input('department');
        $Employee->no_of_casual_leaves = 0; //$request->input('no_of_casual_leaves');
        $Employee->no_of_annual_leaves = 0; //$request->input('no_of_annual_leaves');
        $Employee->emp_work_telephone = $request->input('emp_work_telephone');
        $Employee->tp1 = $request->input('telephone');
        $Employee->emp_fullname = $request->input('emp_fullname');
        $Employee->save();
        $insertedId = $Employee->id;

        //Check that there is a profile ot not then Create Payroll profile
        $existingProfile = PayrollProfile::where('emp_id', $insertedId)->first();
        if(!$existingProfile){
            $payrollprofile = new PayrollProfile();
            $payrollprofile->emp_id = $insertedId;
            $payrollprofile->emp_etfno = $request->input('etfno');
            $payrollprofile->payroll_process_type_id = 1;
            $payrollprofile->payroll_act_id = 1;
            $payrollprofile->employee_bank_id = '0';
            $payrollprofile->employee_executive_level = 0;
            $payrollprofile->basic_salary =  0;
            $payrollprofile->day_salary =  0;
            $payrollprofile->epfetf_contribution = 'ACTIVE';
            $payrollprofile->created_by = Auth::id();
            $payrollprofile->updated_by = '0';
            $payrollprofile->save();
        }
        return response()->json(['success' => 'Data Added successfully.']);
    }

    private function get_emp_available_leaves($join_date_f, $emp_id){
        //$join_date_f = '2021-12-27';
        $join_year = Carbon::parse($join_date_f)->year;
        $join_month = Carbon::parse($join_date_f)->month;
        $join_date = Carbon::parse($join_date_f)->day;
        $full_date = '2022-'.$join_month.'-'.$join_date;

        $q_data = DB::table('quater_leaves')
            ->where('from_date', '<', $full_date)
            ->where('to_date', '>', $full_date)
            ->first();

        $total_taken_annual_leaves = DB::table('leaves')
            ->where('leaves.emp_id', '=', $emp_id)
            ->where('leaves.leave_type', '=', '1')
            ->sum('no_of_days');

        $leaves = 0;
        if($join_year == date('y')){
            $leaves = $q_data->leaves;
        }else{
            $leaves = 14;
        }
        // - taken leaves for current year
        // + leaves from previous year
        return $leaves;
    }

    public function show($id)
    {
        $permission = Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }
        $employee = Employee::where('id', $id)->first();
        $branch = Branch::orderBy('id', 'asc')->get();
        $shift_type = ShiftType::where('deleted',0)->orderBy('id', 'asc')->get();
        $employmentstatus = EmploymentStatus::orderBy('id', 'asc')->get();
        $jobtitles = JobTitle::orderBy('id', 'asc')->get();
        $company = Company::orderBy('id', 'asc')->get();
        $departments = Department::orderBy('id', 'asc')->get();
        $job_categories = JobCategory::orderBy('id', 'asc')->get();
        $work_categories = WorkCategory::orderBy('id', 'asc')->get();
        $dsdivisions = DSDivision::orderBy('id', 'asc')->where('status', '=', 1)->get();
        $gsndivision = GNSDivision::orderBy('id', 'asc')->where('status', '=', 1)->get();
        $policestation = Policestation::orderBy('id', 'asc')->where('status', '=', 1)->get();

        return view('Employee.viewEmployee', compact( 'job_categories', 'employee', 'id', 'jobtitles', 'employmentstatus', 'branch', 'shift_type', 'company', 'departments', 'work_categories'
          ,'dsdivisions','gsndivision','policestation'));
    }

    public function edit(REQUEST $request)
    {
        $permission = Auth::user()->can('employee-edit');
        if (!$permission) {
            abort(403);
        }
        $id = $request->id;
        // Add validation rules
        $validator = Validator::make($request->all(), [
            'emp_id' => 'required|max:15|unique:employees,emp_id,'.$id,
            'emp_etfno' => [
                                'nullable',
                                Rule::unique('employees', 'emp_etfno')
                                    ->ignore($id)
                                    ->where('emp_company', $request->input('employeecompany'))
                                    ->where('emp_location', $request->input('location'))
                                    ->where(function ($query) {
                                        $query->where('emp_etfno', '!=', '0')
                                            ->whereNotNull('emp_etfno');
                                    })
                            ],
            'photograph' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:100',
        ], [
            'emp_id.unique' => 'The employee ID already exists',
            'emp_etfno.unique' => 'The EPF number already exists',
            'photograph.image' => 'The file must be an image',
            'photograph.mimes' => 'Only jpeg, png, jpg, gif files are allowed',
            'photograph.max' => 'The image must be less than 100KB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }

        $id = $request->id;
        $emp_id = $request->emp_id;
        $emp_etfno = $request->emp_etfno;
        $emp_name_with_initial = $request->emp_name_with_initial;
        $calling_name = $request->calling_name;
        $firstname = $request->firstname;
        $middlename = $request->middlename;
        $lastname = $request->lastname;
        $fullname = $request->fullname;
        $nicnumber = $request->nicnumber;
        $licensenumber = $request->licensenumber;
        $licenseexpiredate = $request->licenseexpiredate;
        $address1 = $request->address1;
        $address2 = $request->address2;
        $gender = $request->gender;
        $marital_status = $request->marital_status;
        $nationality = $request->nationality;
        $birthday = $request->birthday;
        $joindate = $request->joindate;
        $jobtitle = $request->jobtitle;
        $jobstatus = $request->jobstatus;
        $dateassign = $request->dateassign;
        $location = $request->location;
        $shift = $request->shift;
        $employeecompany = $request->employeecompany;
        $department = $request->department;
        $job_category_id = $request->job_category_id;
        $work_category_id = $request->work_category_id;
        $leave_approve_person = $request->leave_approve_person;
        $emergency_contact_person = $request->emergency_contact_person;
        $emergency_contact_tp = $request->emergency_contact_tp;
        $dsdivision = $request->dsdivision;
        $gsndivision = $request->gsndivision;
        $gsnname = $request->gsnname;
        $gsncontactno = $request->gsncontactno;
        $policestation = $request->policestation;
        $policecontat = $request->policecontat;
        $employee = Employee::find($id);

        if($jobstatus != 2){
            $errors = array();
            if($request->no_of_casual_leaves > 0){
                $errors[] = 'No of casual leaves allows only for permanent employees';
                Session::flash('error', $errors);
                return redirect('viewEmployee/' . $id);
            }
            if($request->no_of_annual_leaves > 0){
                $errors[] = 'No of annual leaves allows only for permanent employees';
                Session::flash('error', $errors);
                return redirect('viewEmployee/' . $id);
            }
        }

        $employee->emp_id = $emp_id;
        $employee->emp_etfno = $emp_etfno;
        $employee->emp_name_with_initial = $emp_name_with_initial;
        $employee->calling_name = $calling_name;
        $employee->emp_first_name = $firstname;
        $employee->emp_med_name = $middlename;
        $employee->emp_last_name = $lastname;
        $employee->emp_fullname = $fullname;
        $employee->emp_national_id = $nicnumber;
        $employee->emp_drive_license = $licensenumber;
        $employee->emp_license_expire_date = $licenseexpiredate;
        $employee->emp_address = $address1;
        $employee->emp_address_2 = $address2;
        $employee->emp_gender = $gender;
        $employee->emp_marital_status = $marital_status;
        $employee->emp_nationality = $nationality;
        $employee->emp_birthday = $birthday;
        $employee->emp_join_date = $joindate;
        $employee->emp_job_code = $jobtitle;
        $employee->emp_status = $jobstatus;
        $employee->emp_location = $location;
        $employee->emp_shift = $shift;
        $employee->emp_company = $employeecompany;
        $employee->emp_department = $department;
        $employee->job_category_id = $job_category_id;
        $employee->work_category_id = $work_category_id;
        $employee->ds_divition = $dsdivision;
        $employee->gsn_divition = $gsndivision;
        $employee->gsn_name = $gsnname;
        $employee->gsn_contactno = $gsncontactno;
        $employee->police_station = $policestation;
        $employee->police_contactno = $policecontat;
        $employee->leave_approve_person = $leave_approve_person;
        $employee->emp_work_telephone = $request->input('emp_work_telephone');
        $employee->tp1 = $request->input('telephone');
        $employee->emp_mobile = $request->input('emp_mobile');
        $employee->emp_etfno_a = $emp_etfno;
        $employee->emp_email = $request->input('employee_mail');
        $employee->emp_other_email = $request->input('employee_other_mail');
        
        if($request->input('is_resigned') !== null){
            $employee->is_resigned = $request->input('is_resigned');
        }else{
            $employee->is_resigned = 0;
        }
        $employee->emp_addressT1 = $request->addressT1;
        $employee->emp_address_T2 = $request->addressT2;
        if ($jobstatus == 2) {
            $employee->emp_permanent_date = $dateassign;
        }
        $employee->emp_assign_date = $dateassign;
        $employee->save();

        $jobstatus = new JobStatus;
        $jobstatus->emp_id = $request->input('id');
        $jobstatus->emp_job_status = $request->input('jobstatus');
        $jobstatus->emp_assign_date = $request->input('dateassign');

        $jobstatus->save();

        if ($request->hasFile('photograph')) {
            $image = $request->file('photograph');
            
            if ($image->isValid()) {
                $name = time() . '_' . $id . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('/images');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                if ($image->move($destinationPath, $name)) {
                    $employeePic = EmployeePicture::where('emp_id', $id)->first();
                    
                    if ($employeePic) {
                        if ($employeePic->emp_pic_filename) {
                            $oldImagePath = public_path('/images/' . $employeePic->emp_pic_filename);
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                        
                        DB::table('employee_pictures')
                            ->where('emp_id', $id)
                            ->update([
                                'emp_pic_filename' => $name,
                                'update_user' => Auth::id(),
                                'update_date' => Carbon::now()->toDateTimeString(),
                                'updated_at' => Carbon::now()->toDateTimeString()
                            ]);
                    } else {
                        DB::table('employee_pictures')->insert([
                            'emp_id' => $id,
                            'emp_pic_filename' => $name,
                            'insert_user' => Auth::id(),
                            'created_at' => Carbon::now()->toDateTimeString(),
                            'updated_at' => Carbon::now()->toDateTimeString()
                        ]);
                    }
                } else {
                    Session::flash('error', 'Failed to upload image. Please try again.');
                    return redirect('viewEmployee/' . $id);
                }
            } else {
                Session::flash('error', 'Invalid image file. Please select a valid image.');
                return redirect('viewEmployee/' . $id);
            }
        }
        Session::flash('success', 'The Employee Details Successfully Updated');
        return redirect('viewEmployee/' . $id);

    }

    public function destroy($id)
    {
        $permission = Auth::user()->can('employee-delete');
        if ($permission == false) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        DB::table('employees')
            ->where('id', $id)
            ->update(['deleted' => 1]);

        Session::flash('success', 'The Employee Details Successfuly Updated');
    }
            
    public function employeeresignation(Request $request){
             
        $permission = Auth::user()->can('employee-edit');
        if ($permission == false) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        } 
            $emp_id = $request->input('recordID');
            $resignationdate = $request->input('resignationdate');
            $resignationremark = $request->input('resignationremark');

        $current_date_time = Carbon::now()->toDateTimeString();
        Employee::where('emp_id', $emp_id)
            ->update([
                'resignation_date' =>  $resignationdate,
                'resignation_remark' =>  $resignationremark,
                'is_resigned' =>  '1',
                'updated_at' => $current_date_time,
            ]);
            
        return response()->json(['success' => 'Employee successfully Resigned']);
    
    }
    public function getEmployeeJoinDate(Request $request)
    {
        $emp_id = $request->input('id');
        $employee = Employee::select('emp_join_date')->where('emp_id', $emp_id)->first();

        if ($employee) {
            return response()->json(['join_date' => $employee->emp_join_date]);
        }

        return response()->json(['error' => 'Employee not found'], 404);
    }

    public function usercreate(Request $request)
    {
        $permission = Auth::user()->can('employee-create');
        if (!$permission) {
            return response()->json(['errors' => ['Unauthorized access']], 403);
        }

        $rules = array(
            'userid' => 'required',
            'name' => 'required|string|max:255',
            'company_id' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $existingUser = User::where('emp_id', $request->input('userid'))->first();
        if ($existingUser) {
            return response()->json(['errors' => ['User login already exists for this employee']]);
        }

        try {
            $user = new User;
            $user->emp_id = $request->input('userid');
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->company_id = $request->input('company_id');
            $user->password = bcrypt($request->input('password'));
            $user->save();
            
            $user->assignRole('Employee');

            return response()->json(['success' => 'User Login is successfully Created']);
            
        } catch (\Exception $e) {
            return response()->json(['errors' => ['Failed to create user login: ' . $e->getMessage()]]);
        }
    }

     
}
