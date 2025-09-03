<?php

namespace App\Http\Controllers;

use App\OccupationGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Console\Input\Input;
use Yajra\Datatables\Datatables;

class OccupationGroupController extends Controller
{
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $occupation_groups = OccupationGroup::all();
        return view('occupation_groups.index', compact('occupation_groups'));
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


    public function store(Request $request)
    {
        //validate
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $name = $request->input('name');

        $occupation_group = new OccupationGroup;
        $occupation_group->name = $name;
        $occupation_group->save();

        return json_encode([
            'success' => true,
            'message' => 'Occupation Group successfully added.',
            'occupation_group' => $occupation_group
        ]);
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

    public function fetch_single(Request $request)
    {
        $occupation_group = OccupationGroup::find($request->id);
        return json_encode([
            'success' => true,
            'occupation_group' => $occupation_group
        ]);
    }

    public function update_manual(Request $request)
    {
        $occupation_group = OccupationGroup::findOrFail($request->hidden_id);
        $occupation_group->name = $request->name;
        $occupation_group->save();

        return json_encode([
            'success' => true,
            'message' => 'Occupation Group successfully updated.',
            'occupation_group' => $occupation_group
        ]);
    }

    public function destroy($id)
    {
        //
    }

    public function occupation_group_list_dt(Request $request)
    {
        $occupation_groups = OccupationGroup::select('id', 'name')->get();

        return Datatables::of($occupation_groups)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $btn = '';

                $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';

                $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
