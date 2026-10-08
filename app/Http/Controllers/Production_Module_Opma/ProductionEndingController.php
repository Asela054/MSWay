<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\ProductionModule_Opma\EmployeeProduction;
use App\ProductionModule_Opma\EmpProductAllocation;
use App\ProductionModule_Opma\EmpProductAllocationDetail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\Productionempattendace;
use App\ProductionModule_Opma\Productionemptransfers;
use App\ProductionModule_Opma\Productionstatusrecords;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;
use Illuminate\Support\Facades\Input;

class ProductionEndingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        return view('Opma_Production.Daily_Production.daily_ending');
    }
    
     public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

         $current_date_time = Carbon::now()->toDateTimeString();

          $quntity = $request->input('quantity');
          $desription = $request->input('desription');
          $hidden_id = $request->input('hidden_id');
          $completetime = $request->input('completetime');
          $complete_status = $request->input('complete_status');
          $damage_percentage = $request->input('damage_percentage');
          $damage_qty = $request->input('damage_qty');

        $completdate = Carbon::parse($completetime)->format('Y-m-d');

        $maindata = DB::table('opma_emp_product_allocation')
            ->select('opma_emp_product_allocation.*')
            ->where('opma_emp_product_allocation.id', $hidden_id)
            ->first(); 

          $produtiondate = $maindata->date;
          $machine_id = $maindata->machine_id;
          $product_id = $maindata->product_id;
          $target = $maindata->target;

          $style = DB::table('opma_styles')->where('id', $product_id)->first();
            if (!$style) {
                return response()->json(['error' => 'Style not found']);
            }
            $requested_qty = !empty($style->request_qty) ? $style->request_qty : 0;

            $already_produced = DB::table('opma_emp_product_allocation')
                ->where('product_id', $product_id)
                ->where('date', '<=', $produtiondate)
                ->where('id', '!=', $hidden_id)
                ->where('production_status', '=', 4)
                ->sum('full_amount');

            $total_produced = $already_produced + $quntity;

            if($style->over_qty_approve_status == 2 && $requested_qty > 0){
                  if ($total_produced > $requested_qty) {
                     try {
                            $this->sendQtyExceedSms($style, $produtiondate, $total_produced, $requested_qty);
                        } catch (\Exception $e) {
                            \Log::error('Qty exceed SMS failed: ' . $e->getMessage());
                        }
                return response()->json([
                    'error' => 'Total produced quantity (' . $total_produced . ') exceeds the requested quantity for this style.'
                ]);
              }
            }
            
          
         $productioncomplete =0;

          $production_differnce = $quntity - $target; 
          $produced_percentage = ($target > 0) ? round(($quntity / $target) * 100, 2) : 0;


          $performance = ($target > 0) ? round((($quntity - $damage_qty) / $target) * 100) : 0;

    
          // get employee count
        $employeeAllocations = DB::table('opma_emp_product_allocation_details')
                        ->where('allocation_id', $hidden_id)
                            ->where('status', 1)
                            ->select('id', 'emp_id')
                        ->get();

          $employeeCount = $employeeAllocations->count();
          $employeeIds = $employeeAllocations->pluck('emp_id')->toArray();
          
          

        if ($employeeCount > 0) {

            foreach ($employeeAllocations as $allocation) {


                if($produced_percentage > 90){
                      $employeedetails = DB::table('employees')
                        ->where('emp_id', $allocation->emp_id)
                        ->select('emp_department','emp_job_code')
                        ->first();
                    
                    $empdepartment = $employeedetails->emp_department;
                    $emp_jobtitle = $employeedetails->emp_job_code;

                    $amountData = DB::table('opma_production_amount')
                            ->where('department_id',$empdepartment )
                            ->where('jobtitle',$emp_jobtitle )
                            ->first();

                    $employee_amount = $amountData ? $amountData->amount : 0;

                }else{
                    $employee_amount = 0;
                }
                $existingRecord = EmployeeProduction::where('allocation_id', $hidden_id)
                                            ->where('emp_id', $allocation->emp_id)
                                            ->first();

                 if ($existingRecord) {
                    // Backup existing record to backup table
                    DB::table('opma_employee_production_backup')->insert([
                        'allocation_id' => $existingRecord->allocation_id,
                        'emp_id' => $existingRecord->emp_id,
                        'date' => $existingRecord->date,
                        'machine_id' => $existingRecord->machine_id,
                        'product_id' => $existingRecord->product_id,
                        'target' => $existingRecord->target,
                        'Produce_qty' => $existingRecord->Produce_qty,
                        'difference' => $existingRecord->difference,
                        'precentage' => $existingRecord->precentage,
                        'amount' => $existingRecord->amount,
                        'description' => $existingRecord->description,
                        'damage_precentage' => $existingRecord->damage_precentage,
                        'damage_qty' => $existingRecord->damage_qty,
                        'perfomance' => $existingRecord->perfomance,
                        'status' => $existingRecord->status,
                        'created_by' => Auth::id(),
                        'updated_by' => Auth::id(),
                        'created_at' => $current_date_time,
                        'updated_at' => $current_date_time 
                    ]);
                }

                $data = [
                'allocation_id' => $hidden_id,
                'emp_id' => $allocation->emp_id,
                'date' => $produtiondate,
                'machine_id' => $machine_id,
                'product_id' => $product_id,
                'target' => $target,
                'Produce_qty' => $quntity,
                'difference' => $production_differnce,
                'precentage' => $produced_percentage,
                'amount' => $employee_amount,
                'description' => $desription,
                'damage_precentage' => $damage_percentage,
                'damage_qty' => $damage_qty,
                'perfomance' => $performance,
                'status' => 1,
                'created_by' => Auth::id(),
                'updated_at' => $current_date_time
                 ];

                 

                    if ($existingRecord) {
                        $existingRecord->update($data);
                    } else {
                        $data['updated_by'] = Auth::id();
                        $data['created_at'] = $current_date_time;
                        EmployeeProduction::create($data);
                    }
            }


             
        // Create record in production_status_records table
        Productionstatusrecords::create([
            'production_id' => $hidden_id,
            'date' => $completdate, 
            'employee_count' => $employeeCount,
            'timestamp' => $completetime,
            'produced_quntity' => $quntity, 
            'production_status' => 4, 
            'created_by' => Auth::id()
        ]);


        foreach ($employeeIds as $emp_id) {
            Productionempattendace::where('emp_id', $emp_id)
                ->where('production_id', $hidden_id)
                ->where('date', $completdate)
                ->update([
                    'finish_timestamp' => $completetime,
                    'status' => 1,
                    'updated_by' => Auth::id(),
                    'updated_at' => Carbon::now()->toDateTimeString()
                ]);
        }

        $form_data = array(
                    'full_amount' => $quntity,
                    'production_status' => '4',
                    'complete_status' =>  $productioncomplete,
                    'updated_by' => Auth::id(),
                    'updated_at' => $current_date_time,);
        
        EmpProductAllocation::findOrFail($hidden_id)->update($form_data);

        }
        
         return response()->json(['success' => 'Production Successfully Finished']);
    }

    public function cancelproduction(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-cancel');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

          $cancel_desription = $request->input('cancel_desription');
          $cancel_id = $request->input('cancel_id');


        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'cancel_description' => $cancel_desription,
            'production_status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocation::findOrFail($cancel_id)->update($form_data);

        return response()->json(['success' => 'Production Successfully Canceled']);

    }

    public function startproduction(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-finish');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $current_date_time = Carbon::now()->toDateTimeString();

          $starttime = $request->input('starttime');
          $start_id = $request->input('start_id');

          $startdate = Carbon::parse($starttime)->format('Y-m-d');

          $employeeDetails = DB::table('opma_emp_product_allocation_details')
            ->where('allocation_id', $start_id)
            ->where('status', 1)
            ->select('id', 'emp_id')
            ->get();

        // Get employee count
        $employeeCount = $employeeDetails->count();
        
        // Get employee IDs as an array
        $employeeIds = $employeeDetails->pluck('emp_id')->toArray();

        // Create record in production_status_records table
        Productionstatusrecords::create([
            'production_id' => $start_id,
            'date' => $startdate, 
            'employee_count' => $employeeCount,
            'timestamp' => $starttime,
            'produced_quntity' => 0, 
            'production_status' => 1, 
            'created_by' => Auth::id()
        ]);


         foreach ($employeeIds as $emp_id) {
            Productionempattendace::create([
                'emp_id' => $emp_id,
                'production_id' => $start_id,
                'date' => $startdate,
                'start_timestmp' => $starttime,
                'finish_timestamp' => null, 
                'status' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id()
            ]);
        }

        
        $form_data = array(
            'production_status' => '1',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        EmpProductAllocation::findOrFail($start_id)->update($form_data);

        return response()->json(['success' => 'Production Start Successfully']);
    }

     public function employeeproduction()
    {
        $user = Auth::user();
        $permission = $user->can('production-ending-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $machines = DB::table('opma_machines')
            ->select('id', 'machine')
            ->get();

        $products = DB::table('opma_styles')
            ->select('id', 'title','code')
            ->where('status', 1)
            ->get();

        return view('Opma_Production.Daily_Production.employee_production', compact('machines', 'products'));
    }


    private function qtyApproveToken($styleId, $expires)
    {
        return substr(hash_hmac('sha256', $styleId . '|' . $expires, config('app.key')), 0, 20);
    }

    private function sendQtyExceedSms($style, $produtiondate, $total_produced, $requested_qty)
    {
        $mobile = '0777474169';
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        if (strlen($mobile) == 10 && $mobile[0] == '0') {
            $mobile = substr($mobile, 1);
        } elseif (strlen($mobile) == 11 && substr($mobile, 0, 2) == '94') {
            $mobile = substr($mobile, 2);
        }

        try {
            $formattedDate = \Carbon\Carbon::parse($produtiondate)->format('d-m-Y');
        } catch (\Exception $e) {
            $formattedDate = $produtiondate;
        }

        // link valid for 3 days
        $expires = time() + (3 * 24 * 60 * 60);
        $token   = $this->qtyApproveToken($style->id, $expires);
        $link    = url('qty-approve/' . $style->id . '/' . $expires . '/' . $token);

        $message = "Production Alert: Style " . $style->title
            . " total produced qty (" . $total_produced . ") exceeds requested qty ("
            . $requested_qty . ") as of " . $formattedDate . ". Approve: " . $link;

        try {
            $smsService = new \App\Services\Opma_Sms_policyService();
            $result = $smsService->sendSms($mobile, $message);
        } catch (\Exception $e) {
            \Log::error('Qty exceed SMS failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }

        \Log::info('eSMS qty exceed result', [
            'style_id' => $style->id,
            'mobile'   => $mobile,
            'result'   => $result,
        ]);

        return $result;
    }

    private function qtyApproveValid($id, $expires, $token)
    {
        if (!ctype_digit((string) $expires) || (int) $expires < time()) {
            return false;
        }
        return hash_equals($this->qtyApproveToken($id, $expires), (string) $token);
    }

    private function qtyApprovePage($title, $message, $button = null)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . e($title) . '</title></head>'
            . '<body style="font-family:Arial,sans-serif;text-align:center;padding:40px 16px;">'
            . '<h3>' . e($title) . '</h3><p>' . e($message) . '</p>';
        if ($button) {
            $html .= '<form method="post" action="">' . csrf_field()
                . '<button type="submit" style="padding:12px 28px;font-size:16px;background:#28a745;color:#fff;border:0;border-radius:4px;">'
                . e($button) . '</button></form>';
        }
        return response($html . '</body></html>');
    }

    public function qtyApproveShow($id, $expires, $token)
    {
        if (!$this->qtyApproveValid($id, $expires, $token)) {
            return $this->qtyApprovePage('Link Invalid', 'This approval link is invalid or has expired.');
        }

        $style = DB::table('opma_styles')->where('id', $id)->first();
        if (!$style) {
            return $this->qtyApprovePage('Not Found', 'Style not found.');
        }
        if ($style->over_qty_approve_status == 1) {
            return $this->qtyApprovePage('Already Approved', 'Over quantity for style ' . $style->title . ' is already approved.');
        }

        return $this->qtyApprovePage(
            'Approve Over Quantity',
            'Style: ' . $style->title . ' | Requested Qty: ' . $style->request_qty,
            'Approve'
        );
    }

    public function qtyApproveConfirm($id, $expires, $token)
    {
        if (!$this->qtyApproveValid($id, $expires, $token)) {
            return $this->qtyApprovePage('Link Invalid', 'This approval link is invalid or has expired.');
        }

        $updated = DB::table('opma_styles')
            ->where('id', $id)
            ->where('over_qty_approve_status', 2)
            ->update(array('over_qty_approve_status' => 1));

        \Log::info('Over qty approved via SMS link', array('style_id' => $id, 'ip' => request()->ip()));

        if ($updated) {
            return $this->qtyApprovePage('Approved', 'Over quantity approved. You can continue the production now.');
        }
        return $this->qtyApprovePage('No Change', 'This style is already approved or not found.');
    }
}
