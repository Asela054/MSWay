<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User; // or App\Models\User depending on structure
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;



class UserPasswordResetController extends Controller
{
     public function showForm()
    {
        return view('auth.reset');
    }

    public function reset(Request $request)
    {
       
       $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        

         $user = User::where('email', $request->email)->first();

    if ($user) {
        $user->password = Hash::make($request->password);

        // Optional: Update remember_token to log out other sessions
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Logout all current sessions
        Auth::logout();
        Session::flush();

        return redirect()->route('login')->with('status', 'Password reset successfully. Please log in with your new password.');
    }

    return redirect()->back()->withErrors(['email' => 'User not found.']);
    }
}
