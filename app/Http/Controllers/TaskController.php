<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Validator;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('task-list');
        if (!$permission) {
            abort(403);
        }

        $task= Task::orderBy('id', 'asc')->get();
        return view('Daily_Task.Task',compact('task'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('task-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'taskname'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);
        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'taskname'   =>  $request->taskname,
            'description'   =>  $request->description,
            'hourly_rate'    =>  $request->hourly_rate,
            'daily_rate'    =>  $request->daily_rate
        );

        $task=new Task;
        $task->taskname=$request->input('taskname');
        $task->description=$request->input('description'); 
        $task->hourly_rate=$request->input('hourly_rate');
        $task->daily_rate=$request->input('daily_rate');
        $task->status=1;      
        $task->save();

        return response()->json(['success' => 'Task Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('task-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = Task::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, Task $task)
    {
        $user = auth()->user();
        $permission = $user->can('task-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        $rules = array(
            'taskname'    =>  'required'
        );
        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'taskname'    =>  $request->taskname,
            'description' =>  $request->description,
            'hourly_rate'    =>  $request->hourly_rate,
            'daily_rate'    =>  $request->daily_rate
        );

        Task::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('task-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Task::findOrFail($id);
        $data->delete();
    }
}
