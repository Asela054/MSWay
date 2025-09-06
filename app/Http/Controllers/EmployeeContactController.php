<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Employee;
use Session;
use Validator;

class EmployeeContactController extends Controller
{
    public function showcontact($id)
    {
        $permission = Auth::user()->can('employee-list');
        if (!$permission) {
            abort(403);
        }

        $employee = DB::table('employees')
            ->leftjoin('employee_pictures', 'employees.id', '=', 'employee_pictures.emp_id')
            ->select('employees.*', 'employee_pictures.emp_pic_filename')
            ->where('id', $id)->first();

        return view('Employee.contactDetails', compact('employee', 'id'));
    }

    public function editcontact(REQUEST $request)
    {
        $permission = Auth::user()->can('employee-edit');
        if ($permission == false) {
            abort(403);
        }

        $id = $request->id;
        $address1 = $request->address1;
        $address2 = $request->address2;
        $city = $request->city;
        $province = $request->province;
        $postal_code = $request->postal_code;
        $home_no = $request->home_no;
        $mobile = $request->mobile;
        $birthday = $request->birthday;
        $work_telephone = $request->work_telephone;
        $work_email = $request->work_email;
        $other_email = $request->other_email;

        $employee = Employee::find($id);

        $employee->emp_address = $address1;
        $employee->emp_address_2 = $address2;
        $employee->emp_city = $city;
        $employee->emp_province = $province;
        $employee->emp_postal_code = $postal_code;
        $employee->emp_home_no = $home_no;
        $employee->emp_mobile = $mobile;
        $employee->emp_birthday = $birthday;
        $employee->emp_work_phone_no = $work_telephone;
        $employee->emp_email = $work_email;
        $employee->emp_other_email = $other_email;

        $employee->save();
        Session::flash('success', 'The Employee Contact Details Successfuly Updated');
        return redirect('contactDetails/' . $id);
    }
}
