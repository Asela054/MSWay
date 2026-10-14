<?php

namespace App\Http\Controllers\ActivityManagement\IOU;

use App\ActivityManagement\IOU\IOUReason;
use Illuminate\Http\Request;
use Validator;

class IOUReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-list');
        if (!$permission) {
            abort(403);
        }

        $iouReason = IOUReason::orderBy('id', 'asc')->get();
        return view('ActivityManagement.IOU.iouReason', compact('iouReason'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'reason'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'reason'        =>  $request->reason            
        );

        $iouReason=new IOUReason;
        $iouReason->reason=$request->input('reason');       
        $iouReason->estimate_amount=$request->input('estimate_amount');  
        $iouReason->remark=$request->input('remark');     
        $iouReason->status=1;     
        $iouReason->save();

       

        return response()->json(['success' => 'IOU Reason Added Successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\IOUReason  $iouReason
     * @return \Illuminate\Http\Response
     */
    public function show(IOUReason $iouReason)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\IOUReason  $iouReason
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if(request()->ajax())
        {
            $data = IOUReason::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\IOUReason  $iouReason
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, IOUReason $iouReason   )
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'reason'    =>  'required'
        );

        $error = Validator::make($request->all(), $rules);

        if($error->fails())
        {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'reason'    =>  $request->reason,
            'estimate_amount'    =>  $request->estimate_amount,
            'remark'    =>  $request->remark
            
        );

        IOUReason::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\IOUReason  $iouReason
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = IOUReason::findOrFail($id);
        $data->status = 3;
        $data->save();
    }
}
