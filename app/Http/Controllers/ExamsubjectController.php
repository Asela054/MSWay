<?php

namespace App\Http\Controllers;

use App\Commen;
use App\Examsubject;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Carbon\Carbon;
use Datatables;
use DB;

class ExamsubjectController extends Controller
{
     public function index()
     {
        return view('Qulification.examsubject');
     }
     public function insert(Request $request){
        $user = Auth::user();
        $subjects = new Examsubject();
        $subjects->exam_type = $request->input('examtype');
        $subjects->subject = $request->input('subject');
        $subjects->status = '1';
        $subjects->created_by = Auth::id();
        $subjects->updated_by = '0';
        $subjects->save();
        return response()->json(['success' => 'Exam Subject is successfully Inserted']);
     }

     public function subjectlist(Request $request){
        $query = DB::table('exam_subjects')
            ->select('exam_subjects.*')
            ->whereIn('exam_subjects.status', [1, 2]);
            
        // Filter by exam type if provided
        if ($request->has('exam_type') && !empty($request->exam_type)) {
            $query->where('exam_subjects.exam_type', $request->exam_type);
        }
        
        $types = $query->get();
        
        return Datatables::of($types)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $btn = '';
                $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';
                
                $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
     }

     public function edit(Request $request)
     {
        $id = Request('id');
        if (request()->ajax()){
            $data = DB::table('exam_subjects')
            ->select('exam_subjects.*')
            ->where('exam_subjects.id', $id)
            ->get(); 
            return response() ->json(['result'=> $data[0]]);
        }
    }

    public function update(Request $request){
        $current_date_time = Carbon::now()->toDateTimeString();
        $id =  $request->hidden_id ;
        $form_data = array(
                'exam_type' => $request->examtype,
                'subject' => $request->subject,
                'updated_by' => Auth::id(),
                'updated_at' => $current_date_time,
            );

        Examsubject::findOrFail($id)->update($form_data);
        
        return response()->json(['success' => 'Exam Subject is Successfully Updated']);
    }

    public function delete(Request $request){
        $id = $request->input('id');
        
        if (!$id) {
            return response()->json(['error' => 'ID not provided'], 400);
        }
        
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' => '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        
        $updated = Examsubject::findOrFail($id)->update($form_data);
        
        if ($updated) {
            return response()->json(['success' => 'Exam Subject is successfully Deleted']);
        } else {
            return response()->json(['error' => 'Failed to delete record'], 500);
        }
    }
}