<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

 use Illuminate\Support\Facades\Session;
 use Auth;
 use App\User;
use Illuminate\Support\Facades\DB;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;
   

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    //protected $redirectTo = '/home';

    protected function redirectTo()
    {
        if (Auth::check()) {
            $employeeData = DB::table('users')
                ->join('employees', 'users.emp_id', '=', 'employees.emp_id')
                ->where('users.id', Auth::id())
                ->select('users.id','employees.emp_id','employees.emp_etfno','employees.emp_name_with_initial', 'employees.emp_location', 'employees.calling_name','employees.emp_department','employees.emp_company')
                ->first();
                
        } 
        else{
            return '/login';
        }         

        // Share with all views
        if($employeeData){                
            Session::put('users_id', $employeeData->id);
            Session::put('emp_id', $employeeData->emp_id);
            Session::put('emp_etfno', $employeeData->emp_etfno);
            Session::put('emp_name_with_initial', $employeeData->emp_name_with_initial);
            Session::put('emp_location', $employeeData->emp_location);
            Session::put('emp_department', $employeeData->emp_department);
            Session::put('emp_company', $employeeData->emp_company);
        }
   
   
        if (Auth::user()->hasRole('Employee')) {
            return '/useraccountsummery';
        }
        return '/home';
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
