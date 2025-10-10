<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use App\Helpers\EmployeeHelper;
use Session;

class RptLeaveController extends Controller
{
    public function leavereport(Request $request)
    {
        $permission = Auth::user()->can('leave-report');
        if (!$permission) {
            abort(403);
        }

        if (!Session::has('company_name')) {
            $company_name = DB::table('companies')->value('name');
            Session::put('company_name', $company_name);
        } else {
            $company_name = Session::get('company_name');
        }

        return view('Report.leavereport' ,compact('company_name'));
    }


    public function leave_report_list(Request $request)
    {
        $permission = Auth::user()->can('leave-report');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        // Base query
        $query = DB::table('leaves')
            ->select([
                'leaves.*',
                'employees.emp_id',
                'employees.emp_name_with_initial',
                'employees.calling_name',
                'ec.emp_name_with_initial as emp_covering_name',
                'leave_types.leave_type as leave_type_name',
                'departments.name as dept_name'
            ])
            ->join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
            ->leftJoin('employees as ec', 'leaves.emp_covering', '=', 'ec.emp_id')
            ->leftJoin('leave_types', 'leave_types.id', '=', 'leaves.leave_type')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->where('employees.deleted', 0)
            ->where('employees.is_resigned', 0)
            ->where('leaves.status', 'Approved');

        // Apply filters
        if ($request->has('department') && $request->department != '') {
            $query->where('departments.id', $request->department);
        }

        if ($request->has('employee') && $request->employee != '') {
            $query->where('employees.emp_id', $request->employee);
        }

        if ($request->has('from_date') && $request->from_date != '') {
            $query->where('leaves.leave_from', '>=', $request->from_date);
        }

        if ($request->has('to_date') && $request->to_date != '') {
            $query->where('leaves.leave_to', '<=', $request->to_date);
        }

        // Handle search value
        if ($request->has('search') && $request->search['value'] != '') {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('employees.emp_id', 'like', "{$searchValue}%")
                ->orWhere('employees.emp_name_with_initial', 'like', "%{$searchValue}%")
                ->orWhere('employees.calling_name', 'like', "%{$searchValue}%")
                ->orWhere('leaves.leave_from', 'like', "%{$searchValue}%")
                ->orWhere('leaves.leave_to', 'like', "%{$searchValue}%")
                ->orWhere('leaves.comment', 'like', "%{$searchValue}%")
                ->orWhere('leaves.reson', 'like', "%{$searchValue}%")
                ->orWhere('ec.emp_name_with_initial', 'like', "%{$searchValue}%")
                ->orWhere('departments.name', 'like', "%{$searchValue}%");
            });
        }

        // Get total records count
        $totalRecords = DB::table('leaves')
            ->join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->where('employees.deleted', 0)
            ->where('employees.is_resigned', 0)
            ->where('leaves.status', 'Approved')
            ->distinct('employees.emp_id', 'leaves.leave_from')
            ->count();

        // Clone the main query for filtered count
        $filteredQuery = clone $query;
        
        // Get filtered count using a simpler approach
        $filteredResults = $filteredQuery->distinct('employees.emp_id', 'leaves.leave_from')->get();
        $totalRecordswithFilter = $filteredResults->count();

        // Apply grouping for the main query
        $query->groupBy('employees.emp_id', 'leaves.leave_from');

        // Apply ordering
        $orderColumnIndex = $request->input('order.0.column');
        $orderColumn = $request->input("columns.{$orderColumnIndex}.data");
        $orderDirection = $request->input('order.0.dir');

        // Map column names to actual database columns
        $columnMapping = [
            'employee_display' => 'employees.emp_name_with_initial',
            'emp_name_with_initial' => 'employees.emp_name_with_initial',
            'leave_from' => 'leaves.leave_from',
            'leave_to' => 'leaves.leave_to',
            'leave_type_name' => 'leave_types.leave_type',
            'dept_name' => 'departments.name',
            'emp_covering_name' => 'ec.emp_name_with_initial',
            'reson' => 'leaves.reson'
        ];

        $orderColumn = $columnMapping[$orderColumn] ?? $orderColumn;
        
        // Only apply ordering if the column exists in the mapping
        if (isset($columnMapping[$request->input("columns.{$orderColumnIndex}.data")])) {
            $query->orderBy($orderColumn, $orderDirection);
        } else {
            $query->orderBy('leaves.leave_from', 'desc');
        }

        // Apply pagination
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $query->offset($start)->limit($length);

        // Get filtered data
        $records = $query->get();

        // Format data
        $data_arr = [];
        foreach ($records as $record) {
            $data_arr[] = [
                "id" => $record->id,
                "employee_display" => EmployeeHelper::getDisplayName((object)[
                    'emp_id' => $record->emp_id,
                    'emp_name_with_initial' => $record->emp_name_with_initial,
                    'calling_name' => $record->calling_name
                ]),
                "emp_name_with_initial" => $record->emp_name_with_initial,
                "leave_from" => $record->leave_from,
                "leave_to" => $record->leave_to,
                "leave_type_name" => $record->leave_type_name,
                "status" => $record->status,
                "dept_name" => $record->dept_name,
                "emp_covering_name" => $record->emp_covering_name,
                "reson" => $record->reson
            ];
        }

        $response = [
            "draw" => intval($request->input('draw')),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr
        ];

        return response()->json($response);
    }


    public function employee_list_from_leaves_sel2(Request $request)
    {
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = DB::query()
                ->where('employees.emp_name_with_initial', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('employees.deleted', 0)
                ->where('employees.is_resigned', 0)
                ->from('leaves')
                ->leftjoin('employees', 'employees.emp_id', '=', 'leaves.emp_id')
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('DISTINCT employees.emp_id as id'),DB::raw('CONCAT(employees.emp_name_with_initial, " - " ,employees.calling_name) as text')]);

            $count = DB::query()
                ->where('employees.emp_name_with_initial', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('employees.deleted', 0)
                ->where('employees.is_resigned', 0)
                ->from('leaves')
                ->leftjoin('employees', 'employees.emp_id', '=', 'leaves.emp_id')
                ->orderBy('employees.emp_name_with_initial')
                ->skip($offset)
                ->take($resultCount)
                ->select([DB::raw('DISTINCT employees.emp_id as id'),DB::raw('employees.emp_name_with_initial as text')])
                ->count();
            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = array(
                "results" => $breeds,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }

    function fetch_leave_data(Request $request)
    {


        if ($request->ajax()) {
            if ($request->employee != '') {
                $data = DB::query()
                    ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                    ->from('leaves')
                    ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                    ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                    ->where('employees.emp_id', $request->employee)
                    ->groupBy('employees.emp_id', 'leaves.leave_from')
                    ->get();
            }
            if ($request->employee != '' && $request->to_date != '' && $request->from_date != '') {
                $data = DB::query()
                    ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                    ->from('leaves')
                    ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                    ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                    ->where('employees.emp_id', $request->employee)
                    ->whereBetween('leaves.leave_from', [$request->from_date, $request->to_date])
                    ->groupBy('employees.emp_id', 'leaves.leave_from')
                    ->get();
            }
            if ($request->to_date != '' && $request->from_date != '') {
                $data = DB::query()
                    ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                    ->from('leaves')
                    ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                    ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                    ->whereBetween('leaves.leave_from', [$request->from_date, $request->to_date])
                    ->groupBy('employees.emp_id', 'leaves.leave_from')
                    ->get();
            }

            echo json_encode($data);

        }

    }

    function leavedatafilter(Request $request)
    {


        if ($request->employee != '') {
            $leave_data = DB::query()
                ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                ->from('leaves')
                ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                ->where('employees.emp_id', $request->employee)
                ->groupBy('employees.emp_id', 'leaves.leave_from')
                ->get();
        }
        if ($request->employee != '' && $request->to_date != '' && $request->from_date != '') {
            $leave_data = DB::query()
                ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                ->from('leaves')
                ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                ->where('employees.emp_id', $request->employee)
                ->whereBetween('leaves.leave_from', [$request->from_date, $request->to_date])
                ->groupBy('employees.emp_id', 'leaves.leave_from')
                ->get();
        }
        if ($request->to_date != '' && $request->from_date != '') {
            $leave_data = DB::query()
                ->select('leaves.*', 'employees.emp_id', 'employees.emp_name_with_initial', 'leave_types.leave_type')
                ->from('leaves')
                ->Join('employees', 'leaves.emp_id', '=', 'employees.emp_id')
                ->leftJoin('leave_types', 'leaves.leave_type', '=', 'leave_types.id')
                ->whereBetween('leaves.leave_from', [$request->from_date, $request->to_date])
                ->groupBy('employees.emp_id', 'leaves.leave_from')
                ->get();
        }


        $leave_array[] = array('Employee Id', 'Name With Initial', 'Leave From', 'Leave To', 'Leave Type', 'Status');
        foreach ($leave_data as $leaves) {


            $leave_array[] = array(
                'Employee Id' => $leaves->emp_id,
                'Name With Initial' => $leaves->emp_name_with_initial,
                'Leave From' => $leaves->leave_from,
                'Leave To' => $leaves->leave_to,
                'Leave Type' => $leaves->leave_type,
                'Status' => $leaves->status


            );
        }
        Excel::create('Employee Leave  Data', function ($excel) use ($leave_array) {
            $excel->setTitle('Employee Leave Data');
            $excel->sheet('Employee Leave Data', function ($sheet) use ($leave_array) {
                $sheet->fromArray($leave_array, null, 'A1', false, false);
            });
        })->download('xlsx');

    }


        // public function leave_report_list(Request $request)
    // {
    //     $permission = Auth::user()->can('leave-report');
    //     if (!$permission) {
    //         return response()->json(['error' => 'UnAuthorized'], 401);
    //     }
    //     ## Read value
    //     $department = $request->get('department');
    //     $employee = $request->get('employee');
    //     $from_date = $request->get('from_date');
    //     $to_date = $request->get('to_date');

    //     $draw = $request->get('draw');
    //     $start = $request->get("start");
    //     $rowperpage = $request->get("length"); // Rows display per page

    //     $columnIndex_arr = $request->get('order');
    //     $columnName_arr = $request->get('columns');
    //     $order_arr = $request->get('order');
    //     $search_arr = $request->get('search');

    //     $columnIndex = $columnIndex_arr[0]['column']; // Column index
    //     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
    //     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    //     $searchValue = $search_arr['value']; // Search value

    //     // Total records
    //     $totalRecords_array = DB::select('
    //         SELECT COUNT(*) as acount
    //             FROM
    //             (
    //                 SELECT COUNT(*)
    //                 from leaves 
    //                 inner join `employees` on `employees`.`emp_id` = `leaves`.`emp_id` 
    //                 left join `leave_types` on `leave_types`.`id` = `leaves`.`leave_type` 
    //                 left join `departments` on `departments`.`id` = `employees`.`emp_department` 
    //                 where 1 = 1
    //                 group by employees.emp_id, leaves.leave_from   
    //             )t
    //         ');

    //     $totalRecords = $totalRecords_array[0]->acount;

    //     $query1 = 'SELECT COUNT(*) as acount ';
    //     $query1.= 'FROM ( ';
    //     $query1.= 'SELECT COUNT(*) ';
    //     $query2= 'FROM leaves ';
    //     $query2.= 'inner join `employees` on `employees`.`emp_id` = `leaves`.`emp_id` ';
    //     $query2.= 'left join employees as ec on ec.emp_id = leaves.emp_covering ';
    //     $query2.= 'left join `leave_types` on `leave_types`.`id` = `leaves`.`leave_type` ';
    //     $query2.= 'left join `departments` on `departments`.`id` = `employees`.`emp_department` ';
    //     $query2.= 'WHERE 1 = 1 ';
    //     $query2.= 'AND employees.deleted = 0 ';
    //     $query2.= 'AND employees.is_resigned = 0 ';
    //     $query2.= 'AND leaves.status = "Approved" ';
    //     //$searchValue = 'Breeder Farm';
    //     if($searchValue != ''){
    //         $query2.= 'AND ';
    //         $query2.= '( ';
    //         $query2.= 'employees.emp_id like "'.$searchValue.'%" ';
    //         $query2.= 'OR employees.emp_name_with_initial like "'.$searchValue.'%" ';
    //         $query2.= 'OR leaves.leave_from like "'.$searchValue.'%" ';
    //         $query2.= 'OR leaves.leave_to like "'.$searchValue.'%" ';
    //         $query2.= 'OR leaves.comment like "'.$searchValue.'%" ';
    //         $query2.= 'OR leaves.reson like "'.$searchValue.'%" ';
    //         $query2.= 'OR ec.emp_name_with_initial like "'.$searchValue.'%" ';
    //         $query2.= 'OR departments.name like "'.$searchValue.'%" ';
    //         $query2.= ') ';
    //     }

    //     if($department != ''){
    //         $query2.= 'AND departments.id = "'.$department.'" ';
    //     }

    //     if($employee != ''){
    //         $query2.= 'AND employees.emp_id = "'.$employee.'" ';
    //     }

    //     if($from_date != ''){
    //         $query2.= 'AND leaves.leave_from >= "'.$from_date.'" ';
    //     }

    //     if($to_date != ''){
    //         $query2.= 'AND leaves.leave_to <= "'.$to_date.'" ';
    //     }

    //     $query6 = 'group by employees.emp_id, leaves.leave_from ';
    //     $query6.= ' ';
    //     $query4 = ') t ';
    //     $query5 = 'LIMIT ' . (string)$start . ' , ' . $rowperpage . ' ';
    //     $query7 = 'ORDER BY '.$columnName.' '.$columnSortOrder.' ';

    //     $totalRecordswithFilter_arr = DB::select($query1.$query2.$query6.$query4);
    //     $totalRecordswithFilter = $totalRecordswithFilter_arr[0]->acount;

    //     // Fetch records
    //     $query3 = 'select  
    //         leaves.*,
    //         employees.emp_id ,
    //         employees.emp_name_with_initial ,
    //         ec.emp_name_with_initial as emp_covering_name,
    //         leave_types.leave_type as leave_type_name,
    //         departments.name as dept_name  
    //           ';

    //     $records = DB::select($query3.$query2.$query6.$query7.$query5);

    //     $data_arr = array();

    //     foreach ($records as $record) {

    //         $data_arr[] = array(
    //             "id" => $record->id,
    //             "emp_name_with_initial" => $record->emp_name_with_initial,
    //             "leave_from" => $record->leave_from,
    //             "leave_to" => $record->leave_to,
    //             "leave_type_name" => $record->leave_type_name,
    //             "status" => $record->status,
    //             "dept_name" => $record->dept_name,
    //             "emp_covering_name" => $record->emp_covering_name,
    //             "reson" => $record->reson
    //         );
    //     }

    //     $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecordswithFilter,
    //         "aaData" => $data_arr
    //     );

    //     echo json_encode($response);
    //     exit;
    // }
}
