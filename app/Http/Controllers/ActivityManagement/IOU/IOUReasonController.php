<?php

namespace App\Http\Controllers\ActivityManagement\IOU;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use App\Services\ActivityManagement\IOU\IOUReasonService;

class IOUReasonController extends Controller
{
    protected $iouReasonService;

    public function __construct(IOUReasonService $iouReasonService)
    {
        $this->middleware('auth');
        $this->iouReasonService = $iouReasonService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-list');
        if (!$permission) {
            abort(403);
        }

        $iouReason = $this->iouReasonService->getAllReasons();
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
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'reason' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $this->iouReasonService->store($request->only(['reason', 'estimate_amount', 'remark']));

        return response()->json(['success' => 'IOU Reason Added Successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = $this->iouReasonService->find($id);
            return response()->json(['result' => $data]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'reason' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $this->iouReasonService->update(
            $request->hidden_id,
            $request->only(['reason', 'estimate_amount', 'remark'])
        );

        return response()->json(['success' => 'Data is successfully updated']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('iou-reason-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->iouReasonService->destroy($id);
    }
}
