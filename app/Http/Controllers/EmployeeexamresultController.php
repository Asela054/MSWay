<?php

namespace App\Http\Controllers;

use App\Commen;
use App\Employeeexamresult;
use App\Examsubject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use Illuminate\Support\Facades\DB;

class EmployeeexamresultController extends Controller
{
    public function show($id)
    {
        $examsubject = Examsubject::orderBy('id', 'asc')
        ->whereIn('exam_subjects.status', [1, 2])
        ->get();
        return view('Employee.viewExamResult', compact( 'id','examsubject'));
    }

    public function insert(Request $request){
        $tableData = $request->input('tableData');
        $employeeid = $request->input('empid');
        $examtype = $request->input('examtype');
        $medium = $request->input('medium');
        $year = $request->input('year');
        $school = $request->input('school');
        $center_no = $request->input('center_no');
        $index_no = $request->input('index_no');
        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($tableData as $rowtabledata) {
            $subjecttxt = $rowtabledata['col_1'];
            $grade = $rowtabledata['col_2'];
            $school_from_table = $rowtabledata['col_3'];
            $medium_text = $rowtabledata['col_4'];
            $year_from_table = $rowtabledata['col_5'];
            $center_no_from_table = $rowtabledata['col_6'];
            $index_no_from_table = $rowtabledata['col_7'];
            $subjectid = $rowtabledata['col_8'];
            $medium_id = $rowtabledata['col_9'];

            $employeresult = new Employeeexamresult();
            $employeresult->emp_id = $employeeid;
            $employeresult->exam_type = $examtype;
            $employeresult->subject_id = $subjectid;
            $employeresult->grade = $grade;
            $employeresult->school = $school;
            $employeresult->medium = $medium_id;
            $employeresult->year = $year;
            $employeresult->center_no = $center_no ? $center_no : 0;
            $employeresult->index_no = $index_no ? $index_no : 0;
            $employeresult->status = '1';
            $employeresult->created_at = $current_date_time;
            $employeresult->save();
        }

        return response()->json(['status' => 1, 'message' => 'Employee Result is Successfully Created']);
    }

    public function resultlist(Request $request){
        $empid = $request->input('empid');

        $requests = DB::table('employee_exam_results')
        ->leftjoin('exam_subjects', 'employee_exam_results.subject_id', '=', 'exam_subjects.id')
        ->select('employee_exam_results.*','employee_exam_results.id AS resultid', 'exam_subjects.subject AS subjectname')
        ->where('employee_exam_results.emp_id', $empid)
        ->whereIn('employee_exam_results.status', [1, 2])
        ->get();

        return Datatables::of($requests)
        ->addIndexColumn()
        ->addColumn('medium_text', function ($row) {
            switch($row->medium) {
                case 1:
                    return 'Sinhala';
                case 2:
                    return 'English';
                case 3:
                    return 'Tamil';
                default:
                    return 'Unknown';
            }
        })
        ->addColumn('action', function ($row) {
            $btn='';
            $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" ><i class="fas fa-pencil-alt"></i></button>';
            $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';  
            return $btn;
        })
        ->rawColumns(['action'])
        ->make(true);
    }

    public function edit(Request $request){
        $id = Request('id');
        if (request()->ajax()){
            $data = DB::table('employee_exam_results')
            ->select('employee_exam_results.*')
            ->where('employee_exam_results.id', $id)
            ->get(); 
            return response() ->json(['result'=> $data[0]]);
        }
    }

    public function update(Request $request){
        $current_date_time = Carbon::now()->toDateTimeString();
        
        $id =  $request->hidden_id;
        $form_data = array(
            'exam_type' => $request->eeditxamtype,
            'subject_id' => $request->editsubject,
            'grade' => $request->editgrade,
            'school' => $request->editschool,
            'medium' => $request->editmedium,
            'year' => $request->edityear,
            'center_no' => $request->editcenter_no ? $request->editcenter_no : 0,
            'index_no' => $request->editindex_no ? $request->editindex_no : 0,
            'updated_at' => $current_date_time,
        );

        Employeeexamresult::findOrFail($id)->update($form_data);
        
        return response()->json(['success' => 'Employee Result is Successfully Updated']);
    }

    public function delete(Request $request){
        $id = Request('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' =>  '3',
            'updated_at' => $current_date_time,
        );
        Employeeexamresult::findOrFail($id)->update($form_data);

        return response()->json(['success' => 'Employee Result is successfully Deleted']);
    }

    public function getExamSubjects(Request $request)
    {
        try {
            $examType = $request->input('exam_type');
            
            if (empty($examType)) {
                return response()->json(['error' => 'Exam type is required'], 400);
            }
            
            $subjects = Examsubject::where('exam_type', $examType)
                ->whereIn('status', [1, 2])
                ->orderBy('subject', 'asc')
                ->get();
            
            return response()->json(['subjects' => $subjects]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error loading subjects: ' . $e->getMessage()], 500);
        }
    }
}