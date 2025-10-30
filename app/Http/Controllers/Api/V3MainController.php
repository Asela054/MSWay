<?php

namespace App\Http\Controllers\Api;

use App\BusLogin;
use App\Emoji;
use App\BusEmployee;
use App\BusEmployeeAvailability;
use App\EmojiSave;
use Config;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class V3MainController extends Controller
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

    public function SaveEmoji(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'emoji_id' => 'required',
            'date' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $obj = new EmojiSave();
        $obj->emoji_id = $request->emoji_id;
        $obj->date = $request->date;
        $obj->save();

        return (new BaseController)->sendResponse(array(), 'Record Inserted');

    }

    public function GetEmojies(Request $request){
        $emojies = Emoji::get();

        if(EMPTY($emojies)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        $data = array();
        foreach($emojies as $emoji){
            $sub = array(
                'id' => $emoji->id,
                'name' => $emoji->name,
                'image' => url('public/'.$emoji->emoji)
            );
            array_push($data, $sub);
        }

        return (new BaseController)->sendResponse($data, 'Emojis List');
    }

    public function BusLogin(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'bus_no' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        //check if record find
        $record = BusLogin::where('bus_no', $request->bus_no)->where('password', md5($request->password))->first();

        if (EMPTY($record)){
            return (new BaseController)->sendError('Login Failed!', ['error' => 'Login Failed!'], '400');
        } 

        $api_key = Config::get('constants.api_secret_key');

        $res = array(
            'api_key' => $api_key
        ); 
        
        return (new BaseController)->sendResponse($res, 'Login Success'); 
    } 

    public function GetBusEmployees(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'api_key' => 'required',
            'bus_no' => 'required'
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        if($request->api_key != Config::get('constants.api_secret_key')){ 
            return (new BaseController())->sendError('Authentication Error.', [], 400);
        }

        //get bus_id
        $buss = BusLogin::where('bus_no', $request->bus_no)->first()->toArray();  
        $bus_id = $buss['id'];

        //check if record find
        $records = BusEmployee::where('bus_id', $bus_id)
        ->with('employee')
        ->get(); 

        return (new BaseController)->sendResponse($records, 'Bus Employees');

    }

    
    public function EmpAvailabilitySave(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'emp_id' => 'required',
            'bus_no' => 'required',
            'date' => 'required',
            'api_key' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        if($request->api_key != Config::get('constants.api_secret_key')){ 
            return (new BaseController())->sendError('Authentication Error.', [], 400);
        }

        $buss = BusLogin::where('bus_no', $request->bus_no)->first()->toArray();  
        $bus_id = $buss['id'];  

        $obj_bus = BusEmployeeAvailability::where('emp_id', $request->emp_id)->where('bus_id', $bus_id)->where('date', $request->date)->first(); 

        if ($obj_bus === null) {
            $obj_bus = new BusEmployeeAvailability(['emp_id', $request->emp_id, 'bus_id', $bus_id, 'date', $request->date, 'availability', '1' ]);
        }
         
        $obj_bus->emp_id = $request->emp_id;
        $obj_bus->bus_id = $bus_id;
        $obj_bus->date = $request->date;
        $obj_bus->availability = 1;
 
        $obj_bus->save();

        return (new BaseController)->sendResponse($obj_bus, 'Success');

    }

    public function GetSavedEmojiCount(Request $request){

        $validator = \Validator::make($request->all(), [
            'date' => 'required',
        ]);

        if($validator->fails()){
            return (new BaseController())->sendError('Validation Error.', $validator->errors(), '400');
        }

        $sql = "
            SELECT 
            COUNT(emoji_id) as count,
            date,
           emoji_id,
           name 
            FROM emoji_save 
            LEFT JOIN emojies ON emoji_save.emoji_id = emojies.id 
            GROUP BY emoji_id   
        ";

        $emojies = DB::select($sql);

        if(EMPTY($emojies)){
            return (new BaseController)->sendError('No Records Found', ['error' => 'No Records Found']);
        }

        return (new BaseController)->sendResponse($emojies, 'Emojis Count List');
    }


}
