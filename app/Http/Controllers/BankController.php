<?php

namespace App\Http\Controllers;

use App\Bank;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Validator;
use Yajra\Datatables\Datatables;


class BankController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('bank-list');
        if(!$permission) {
            abort(403);
        }

        return view('Organization.bank' );
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('bank-create');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'name' => 'required',
            'code' => 'required|unique:banks,code',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $bank = new Bank();
        $bank->bank = $request->input('name');
        $bank->code = $request->input('code');
        $bank->status = '1';
        $bank->create_by = Auth::id();
        $bank->save();

        return response()->json(['success' => 'Bank Added successfully.']);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('bank-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = Bank::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request, Bank $bank)
    {
        $user = Auth::user();
        $permission = $user->can('bank-edit');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'name' => 'required',
            'code' => 'required|unique:banks,code,'.$request->hidden_id
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $current_date_time = Carbon::now()->toDateTimeString();

        $form_data = array(
            'bank' => $request->name,
            'code' => $request->code,
            'update_by' => Auth::id(),
            'updated_at' => $current_date_time,
        );

        Bank::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Bank is successfully updated']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('bank-delete');
        if(!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = Bank::findOrFail($id);
        $data->delete();
    }

    public function bank_list(Request $request){
        if ($request->ajax())
        {
            $page = Input::get('page');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $breeds = Bank::where('bank', 'LIKE',  '%' . Input::get("term"). '%')
                ->where('status','1')
                ->orderBy('code')
                ->skip($offset)
                ->take($resultCount)
                ->get([DB::raw('code as id'),DB::raw('bank as text')]);

            $count = Bank::where('status', '1')->count();
            $endCount = $offset + $resultCount;
            $morePages = $endCount < $count;

            $results = array(
                "results" => $breeds,
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return response()->json($results);
        }
    }

    public function banks_list_dt(Request $request)
    {
        $banks = Bank::where('status', '1')->orderBy('code')->get();

        return Datatables::of($banks)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $user = Auth::user();

                $btn = ' <a title="View Branches" href=" '.route('bank_branch_show',$row->code) .' " class="branches btn btn-outline-info btn-sm" > <i class="fas fa-building"></i> </a> ';

                $permission = $user->can('bank-edit');
                if($permission) {
                    $btn .= ' <button name="edit" id="' . $row->id . '" class="edit btn btn-outline-primary btn-sm" type="submit"><i class="fas fa-pencil-alt"></i></button>';
                }

                $permission = $user->can('bank-delete');
                if($permission) {
                    $btn .= ' <button name="delete" id="'.$row->id.'" class="delete btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></button>';
                }

                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
