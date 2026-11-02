<?php

namespace App\Http\Controllers\ActivityManagement;

use Illuminate\Http\Request;
use Validator;
use App\Http\Controllers\Controller;
use App\Services\ActivityManagement\VisitPlaceService;

class VisitPlaceController extends Controller
{
    protected $visitPlaceService;

    public function __construct(VisitPlaceService $visitPlaceService)
    {
        $this->middleware('auth');
        $this->visitPlaceService = $visitPlaceService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('visit-place-list');
        if (!$permission) {
            abort(403);
        }

        $visitPlace = $this->visitPlaceService->getAllPlaces();
        return view('ActivityManagement.visitPlace', compact('visitPlace'));
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
        $permission = $user->can('visit-place-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'employee' => 'required',
            'latitude'    =>  'nullable|numeric|between:-90,90',
            'longitude'    =>  'nullable|numeric|between:-180,180',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $this->visitPlaceService->store($request->only(['employee', 'place', 'date', 'time', 'latitude', 'longitude', 'remark']));

        return response()->json(['success' => 'Visit Place Added Successfully.']);
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
        $permission = $user->can('visit-place-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = $this->visitPlaceService->find($id);
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
        $permission = $user->can('visit-place-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'employee' => 'required',
            'latitude'    =>  'nullable|numeric|between:-90,90',
            'longitude'    =>  'nullable|numeric|between:-180,180',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $this->visitPlaceService->update(
            $request->hidden_id,
            $request->only(['employee', 'place', 'date', 'time', 'latitude', 'longitude', 'remark'])
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
        $permission = $user->can('visit-place-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $this->visitPlaceService->destroy($id);
    }
}
