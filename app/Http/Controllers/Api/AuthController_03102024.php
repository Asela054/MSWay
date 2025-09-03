<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct()
    {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, X-Auth-Token');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day   // cache for 1 day
            header('content-type: application/json; charset=utf-8');
        }

        if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
            $_POST = array_merge($_POST, (array) json_decode(trim(file_get_contents('php://input')), true));
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        
               {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }

    public function AuthenticateUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $login =[
            'email' => $request->email,
            'password' => $request->password
        ];

        if(!Auth::attempt($login)){
            return (new BaseController)->sendError('Unauthorised', ['error' => 'Invalid Login']);
        }

        $accessToken = Auth::user()->createToken('authToken')->accessToken;

        $data = array(['user' => Auth::user(), 'api_key' => $accessToken]);

        return (new BaseController)->sendResponse($data, 'Login Success');

    }

    public function UpdatePasswordFromForgotPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'user_id' => 'required',
            'password' => 'required',
            'otp' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj_user = User::where('id', $request->user_id)->where('otp', $request->otp)->first();

        if(EMPTY($obj_user)){
            return (new BaseController)->sendError('OTP is Invalid', ['error' => 'OTP is Invalid']);
        }

        $obj_user = User::find($request->user_id);
        $obj_user->password = Hash::make($request->password);
        $obj_user->save();

        return (new BaseController)->sendResponse($obj_user, 'Password Changed');

    }

    public function resetPasswordRequestOTP(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $number = rand(1000,9000);
        $user = User::where('email', $request->email)->first();
        if($user){
            $update_data = array(
                'otp' => $number
            );
            $user->update($update_data);
        }

        //send email
        $mail_title = ' One Time Password ';

        $mail_body = '';
        $mail_body .= '<table>';
        $mail_body .= '<tr>';
        $mail_body .= '<td> <strong> Use Following OTP to change the password. </strong> </td>';
        $mail_body .= '</tr>';
        $mail_body .= '<tr>';
        $mail_body .= '<td>';

        $mail_body .= '<table>
                    <tr> <td> <strong> '.$number.' </strong>   </td> </tr> 
                </table>';

        $mail_body .= '</td>';
        $mail_body .= '</tr>';

        $mail_body .= '<tr>';
        $mail_body .= '<td>   This is a system generated email, do not reply to this email   </td>';
        $mail_body .= '</tr>';

        $mail_body .= '<tr>';
        $mail_body .= '<td>   </td>';
        $mail_body .= '</tr>';

        $mail_body .= '</table>';

        $email_msg = array(
            'title' => $mail_title,
            'body' => $mail_body,
            'receptions' => 'tharakadoo@gmail.com',
        );

        $status = false;
        $errors = [];
        $msg = [];

//        try {
//            echo json_encode(array(
//                'status' => $status,
//                'msg' => $msg,
//                'errors' => $errors,
//                'email_msg' => $email_msg,
//            ));
//        } catch (Exception $e) {
//            echo 'Message: ' .$e->getMessage();
//        }

        $data = array(
            'otp' => $number,
            'user' => $user
        );

        return (new BaseController)->sendResponse($data, 'OTP Send');

    }

    public function UpdatePassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'employee_id' => 'required',
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj_user = User::where('emp_id', $request->employee_id)->first();

        if(EMPTY($obj_user)){
            return (new BaseController)->sendError('Invalid User', ['error' => 'Invalid User']);
        }

        $login =[
            'email' => $obj_user->email,
            'password' => $request->old_password
        ];

        if(!Auth::attempt($login)){
            return (new BaseController)->sendError('Invalid Current Password', ['error' => 'Invalid Current Password']);
        }

        $obj_user->password = Hash::make($request->password);
        $obj_user->save();

        return (new BaseController)->sendResponse($obj_user, 'Password Changed');

    }

}
