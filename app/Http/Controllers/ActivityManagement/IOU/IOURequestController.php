<?php

namespace App\Http\Controllers\ActivityManagement\IOU;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use App\Services\ActivityManagement\IOU\IOURequestService;
use App\Services\ActivityManagement\IOU\IOUReasonService;

class IOURequestController extends Controller
{
    protected $iouRequestService;
    protected $iouReasonService;

    public function __construct(IOURequestService $iouRequestService, IOUReasonService $iouReasonService)
    {
        $this->middleware('auth');
        $this->iouRequestService = $iouRequestService;
        $this->iouReasonService  = $iouReasonService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('iou-request-list');
        if (!$permission) {
            abort(403);
        }

        $iouRequest = $this->iouRequestService->getAllRequests();
        $iouReason  = $this->iouReasonService->getAllReasons();
        return view('ActivityManagement.IOU.iouRequest', compact('iouRequest', 'iouReason'));
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
        $permission = $user->can('iou-request-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'reason_id' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $this->iouRequestService->store($request->only(['reason_id', 'employee', 'amount', 'remark']));

        return response()->json(['success' => 'IOU Request Added Successfully.']);
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
        $permission = $user->can('iou-request-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = $this->iouRequestService->find($id);
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
        $permission = $user->can('iou-request-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'reason_id' => 'required'
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $this->iouRequestService->update(
            $request->hidden_id,
            $request->only(['reason_id', 'employee', 'amount', 'remark'])
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
        $permission = $user->can('iou-request-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->iouRequestService->destroy($id);
    }
}
