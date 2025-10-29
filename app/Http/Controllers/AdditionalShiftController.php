<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\Datatables\Facades\Datatables;

class AdditionalShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('additional-shift-list');
        if(!$permission) {
            abort(403);
        }

        return view('Shift.additional_shift');
    }

    public function additional_shift_list_dt(Request $request)
    {

        $branches = DB::table('additional_shifts')
            ->select('additional_shifts.*')
            ->get();

        return Datatables::of($branches)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {

                $btn = '';

                $user = Auth::user();
                $permission = $user->can('additional-shift-edit');
                if($permission){
                    $btn .= ' <button name="edit" id="'.$row->id.'" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';
                }

                $permission = $user->can('additional-shift-delete');
                if($permission){
                    $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

   
}
