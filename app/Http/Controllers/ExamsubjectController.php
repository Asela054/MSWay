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
                
                if($row->status == 1){
                    $btn .= ' <a href="'.route('examsubjectstatus', ['id' => $row->id, 'stasus' => 2]) .'" onclick="return deactive_confirm()" target="_self" class="btn btn-success btn-sm mr-1 "><i class="fas fa-check"></i></a>';
                }else{
                    $btn .= '&nbsp;<a href="'.route('examsubjectstatus', ['id' => $row->id, 'stasus' => 1]) .'" onclick="return active_confirm()" target="_self" class="btn btn-warning btn-sm mr-1 "><i class="fas fa-times"></i></a>';
                }
                
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
        $id = Request('id');
        $current_date_time = Carbon::now()->toDateTimeString();
        $form_data = array(
            'status' =>  '3',
            'updated_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );
        Examsubject::findOrFail($id)->update($form_data);

        return response()->json(['success' => 'Exam Subject is successfully Deleted']);
    }

    public function status($id,$statusid){
        if($statusid == 1){
            $form_data = array(
                'status' =>  '1',
                'updated_by' => Auth::id(),
            );
            Examsubject::findOrFail($id)->update($form_data);
            return redirect()->route('examsubjects');
        } else{
            $form_data = array(
                'status' =>  '2',
                'updated_by' => Auth::id(),
            );
            Examsubject::findOrFail($id)->update($form_data);
            return redirect()->route('examsubjects');
        }
    }
}