<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use App\ERP_KT\Customer;
use Illuminate\Http\Request;
use Validator;
use DB;

class ERPCustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-list');
        if (!$permission) {
            abort(403);
        }

        $customers = Customer::orderBy('id', 'asc')->get();
        return view('ERP_KT.customer', compact('customers'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'name'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name'=>$request->name,
            'contact_number'=>$request->contact_number,
            'email'=>$request->email,
            'remarks'=>$request->remarks,
        );

        $customer=new Customer;
        $customer->name=$request->input('name');
        $customer->contact_number=$request->input('contact_number');
        $customer->email=$request->input('email');
        $customer->remarks=$request->input('remarks');       
        $customer->save();

        return response()->json(['success' => 'Customer Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = Customer::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)   
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
          'name'    =>  'required',
        );

        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name'=>$request->name,
            'contact_number'=>$request->contact_number,
            'email'=>$request->email,
            'remarks'=>$request->remarks,
        );

        Customer::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Customer Data Updated Successfully ']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Customer::findOrFail($id);
        $data->delete();

        return response()->json(['success' => 'Customer Deleted Successfully.']);
    }

    public function upload_csv(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-customer-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, [
            'csv_file_u' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('csv_file_u');

        try {
            $fileContents = file($file->getPathname());
            array_shift($fileContents);

            $errors = [];
            $successCount = 0;
            $lineNumber = 2;

            DB::beginTransaction();

            foreach ($fileContents as $line) {
                $line = trim($line);
                if (empty($line)) {
                    $lineNumber++;
                    continue;
                }

                $data = str_getcsv($line);

                if (count($data) < 1 || empty(trim($data[0]))) {
                    $errors[] = "Line {$lineNumber}: Missing customer name";
                    $lineNumber++;
                    continue;
                }

                $name           = trim($data[0]);
                $contact_number = isset($data[1]) ? trim($data[1]) : null;
                $email          = isset($data[2]) ? trim($data[2]) : null;
                $remarks        = isset($data[3]) ? trim($data[3]) : null;

                $existing = Customer::where('name', $name)->first();
                if ($existing) {
                    $errors[] = "Line {$lineNumber}: Customer already exists with name '{$name}'";
                    $lineNumber++;
                    continue;
                }

                try {
                    Customer::create([
                        'name'           => $name,
                        'contact_number' => $contact_number,
                        'email'          => $email,
                        'remarks'        => $remarks,
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $errors[] = "Line {$lineNumber}: Processing error - " . $e->getMessage();
                }

                $lineNumber++;
            }

            DB::commit();

            $response = [
                'status' => $successCount > 0,
                'msg'    => "Successfully processed {$successCount} customer(s)."
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
                if ($successCount === 0) {
                    $response['status'] = false;
                    $response['msg']    = 'No records were processed due to errors.';
                }
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'msg'    => 'File processing failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
