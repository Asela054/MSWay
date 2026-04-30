<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected function authenticated(Request $request, $user)
    {
        $employeeData = DB::table('users')
            ->join('employees', 'users.emp_id', '=', 'employees.emp_id')
            ->leftjoin('companies', 'employees.emp_company', '=', 'companies.id')
            ->where('users.id', $user->id)
            ->select(
                'users.id', 'employees.emp_id', 'employees.emp_etfno',
                'employees.emp_name_with_initial', 'employees.emp_location',
                'employees.calling_name', 'employees.emp_department',
                'employees.emp_company', 'companies.name as company_name',
                'companies.address as company_address'
            )
            ->first();

        if ($employeeData) {
            // Keep Laravel session for the main app
            $request->session()->put([
                'users_id'              => $employeeData->id,
                'emp_id'                => $employeeData->emp_id,
                'emp_etfno'             => $employeeData->emp_etfno,
                'emp_name_with_initial' => $employeeData->emp_name_with_initial,
                'emp_location'          => $employeeData->emp_location,
                'emp_department'        => $employeeData->emp_department,
                'emp_company'           => $employeeData->emp_company,
                'company_name'          => $employeeData->company_name,
                'company_address'       => $employeeData->company_address,
            ]);

            
        }
        // Create a bridge token for standalone PHP scripts
        $this->createBridgeToken($request, $employeeData->id);

        return $user->hasRole('Employee')
            ? redirect('/useraccountsummery')
            : redirect('/home');
    }

    /**
     * Write a signed token into user_session_tokens and set a plain cookie
     * that standalone scripts can read without needing Laravel's Encrypter.
     */
    protected function createBridgeToken(Request $request, int $userId): void
    {
        try {
            DB::table('user_session_tokens')
                ->where('user_id', $userId)
                ->where('expires_at', '<', Carbon::now())
                ->delete();

            $token     = bin2hex(random_bytes(32));
            $expiresAt = Carbon::now()->addHours(12);

            \Log::info('Bridge token insert attempt', [
                'user_id'    => $userId,
                'token'      => $token,
                'expires_at' => $expiresAt->toDateTimeString(),
            ]);

            $inserted = DB::table('user_session_tokens')->insert([
                'token'      => $token,
                'user_id'    => $userId,
                'created_at' => Carbon::now()->toDateTimeString(),
                'expires_at' => $expiresAt->toDateTimeString(),
            ]);

            \Log::info('Bridge token insert result', ['inserted' => $inserted]);

            setcookie(
                'usr_bridge',
                $token,
                $expiresAt->timestamp,
                '/',
                '',
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                true
            );

            \Log::info('Bridge cookie set', ['token' => $token]);

        } catch (\Exception $e) {
            \Log::error('createBridgeToken failed: ' . $e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        if ($userId = $request->session()->get('users_id')) {
            DB::table('user_session_tokens')
                ->where('user_id', $userId)
                ->delete();
        }

        setcookie('usr_bridge', '', time() - 3600, '/');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    protected function redirectTo()
    {
        return Auth::user()->hasRole('Employee') ? '/useraccountsummery' : '/home';
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}