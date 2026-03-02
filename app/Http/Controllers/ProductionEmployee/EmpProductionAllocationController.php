<?php

namespace App\Http\Controllers\ProductionEmployee;

use App\ProductionEmployee\EmpProductionAllocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use DB;

class EmpProductionAllocationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('production-detail-list');

        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $departments = DB::table('departments')
            ->orderBy('name')
            ->get();

        return view('ProductionEmployee.empAllocation', compact('departments'));
    }

    public function insert(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-detail-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, [
            'tableData'          => 'required|array|min:1',
            'tableData.*.col_1'  => 'required|string',
            'tableData.*.col_3'  => 'required|date',
            'tableData.*.col_4'  => 'required|integer|min:1',  
        ]);

        try {
            DB::beginTransaction();
            
            foreach ($request->tableData as $row) {
                $emp_id = $row['col_1'];
                $date = $row['col_3'];
                $department_id = $row['col_4'];

                $employee = DB::table('employees')
                    ->where('emp_id', $emp_id)
                    ->where('deleted', 0)
                    ->first();

                if (!$employee) {
                    throw new \Exception("Employee ID {$emp_id} not found or inactive");
                }

                $existing = EmpProductionAllocation::where('emp_id', $emp_id)
                    ->where('date', $date)
                    ->where('department_id', $department_id)
                    ->where('status', '!=', '3')
                    ->first();

                if ($existing) {
                    throw new \Exception("Reading already exists for Employee {$emp_id} on {$date} for Department ID {$department_id}");
                }

                EmpProductionAllocation::create([
                    'date' => $date,
                    'department_id' => $department_id,
                    'emp_id' => $emp_id,
                    'status' => '0',
                    'created_by' => Auth::id(),
                    'updated_by' => 0,
                ]);
            }

            DB::commit();
            return response()->json(['success' => 'Employee Production entries successfully created']);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }

    public function edit(Request $request)
    {
        $id = $request->id;
        
        try {
            $data = EmpProductionAllocation::with('employee:emp_id,emp_name_with_initial')
                ->where('id', $id)
                ->where('status', '!=', '3')
                ->first();

            if (!$data) {
                return response()->json(['error' => 'Record not found'], 404);
            }

            $requestdata = '<tr>
                <td>' . $data->emp_id . '</td>
                <td>' . ($data->employee ? $data->employee->emp_name_with_initial : 'N/A') . '</td>
                <td>' . $data->date . '</td>
                <td>' . $data->department_id . '</td>
                <td class="text-right">
                    <button type="button" onclick="productDelete(this);" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>';

            $responseData = [
                'mainData' => $data,
                'requestdata' => $requestdata,
            ];

            return response()->json(['result' => $responseData]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading record: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-detail-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->validate($request, [
            'hidden_id'          => 'required|exists:emp_production_allocation,id', 
            'tableData'          => 'required|array|min:1',
            'tableData.*.col_1'  => 'required|string',
            'tableData.*.col_3'  => 'required|date',
            'tableData.*.col_4'  => 'required|integer|min:1',
        ]);

        try {
            $id = $request->hidden_id;
            $row = $request->tableData[0]; 
            
            $emp_id = $row['col_1'];
            $date = $row['col_3'];
            $department_id = $row['col_4'];

            $employee = DB::table('employees')
                ->where('emp_id', $emp_id)
                ->where('deleted', 0)
                ->first();

            if (!$employee) {
                throw new \Exception("Employee ID {$emp_id} not found or inactive");
            }

            $existing = EmpProductionAllocation::where('emp_id', $emp_id)
                ->where('date', $date)
                ->where('department_id', $department_id)
                ->where('status', '!=', '3')
                ->where('id', '!=', $id)
                ->first();

            if ($existing) {
                throw new \Exception("Reading already exists for Employee {$emp_id} on {$date} for Department ID {$department_id}");
            }

            EmpProductionAllocation::findOrFail($id)->update([
                'date' => $date,
                'emp_id' => $emp_id,
                'department_id' => $department_id,
                'updated_by' => Auth::id(),
                'updated_at' => Carbon::now(),
            ]);

            return response()->json(['success' => 'Employee Production entry successfully updated']);
            
        } catch (\Exception $e) {
            return response()->json(['errors' => [$e->getMessage()]], 422);
        }
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('production-detail-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $id = $request->id;
        
        try {
            EmpProductionAllocation::findOrFail($id)->delete();
            
            return response()->json(['success' => 'Employee Production entry successfully deleted']);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Record not found'], 404);
        }
    }

    // public function meter_reading_upload_csv(Request $request)
    // {
    //     $user = Auth::user();
    //     $permission = $user->can('production-detail-create');
    //     if (!$permission) {
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     }

    //     $this->validate($request, [
    //         'date' => 'required|date',
    //         'csv_file_u' => 'required|file|mimes:csv,txt|max:2048'
    //     ]);

    //     $date = $request->input('date');
    //     $file = $request->file('csv_file_u');

    //     try {
    //         $fileContents = file($file->getPathname());
    //         array_shift($fileContents); 
            
    //         $errors = [];
    //         $successCount = 0;
    //         $lineNumber = 2;

    //         DB::beginTransaction();

    //         foreach ($fileContents as $line) {
    //             $line = trim($line);
    //             if (empty($line)) {
    //                 $lineNumber++;
    //                 continue;
    //             }

    //             $data = str_getcsv($line);
                
    //             if (count($data) < 2) {
    //                 $errors[] = "Line {$lineNumber}: Invalid format - expected emp_id,count";
    //                 $lineNumber++;
    //                 continue;
    //             }

    //             $emp_id = trim($data[0]);
    //             $count = trim($data[1]);

    //             if (empty($emp_id)) {
    //                 $errors[] = "Line {$lineNumber}: Missing employee ID";
    //                 $lineNumber++;
    //                 continue;
    //             }

    //             if (!is_numeric($count)) {
    //                 $errors[] = "Line {$lineNumber}: Count must be numeric";
    //                 $lineNumber++;
    //                 continue;
    //             }

    //             $emp = DB::table('employees')
    //                 ->where('emp_id', $emp_id)
    //                 ->where('deleted', 0)
    //                 ->first();

    //             if (!$emp) {
    //                 $errors[] = "Line {$lineNumber}: Employee ID {$emp_id} not found or inactive";
    //                 $lineNumber++;
    //                 continue;
    //             }

    //             $existing = EmpProductionAllocation::where('date', $date)
    //                 ->where('emp_id', $emp_id)
    //                 ->where('status', '!=', '3')
    //                 ->first();

    //             if ($existing) {
    //                 $errors[] = "Line {$lineNumber}: Reading already exists for Employee {$emp_id} on {$date}";
    //                 $lineNumber++;
    //                 continue;
    //             }

    //             try {
    //                 EmpProductionAllocation::create([
    //                     'date' => $date,
    //                     'emp_id' => $emp_id,
    //                     'count' => $count,
    //                     'status' => '1',
    //                     'created_by' => Auth::id(),
    //                     'updated_by' => 0,
    //                 ]);

    //                 $successCount++;
    //             } catch (\Exception $e) {
    //                 $errors[] = "Line {$lineNumber}: Processing error - " . $e->getMessage();
    //             }
                
    //             $lineNumber++;
    //         }

    //         DB::commit();

    //         $response = [
    //             'status' => $successCount > 0,
    //             'msg' => "Successfully processed {$successCount} employees."
    //         ];

    //         if (!empty($errors)) {
    //             $response['errors'] = $errors;
    //             if ($successCount === 0) {
    //                 $response['status'] = false;
    //                 $response['msg'] = 'No records were processed due to errors.';
    //             }
    //         }

    //         return response()->json($response);
            
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return response()->json([
    //             'status' => false,
    //             'msg' => 'File processing failed: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }
}