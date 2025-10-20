<?php

namespace App\Http\Controllers;

use App\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductMachines;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductMachineController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($product_id)
    {
        $user = Auth::user();
        $permission = $user->can('product-list');
        if(!$permission) {
            abort(403);
        }

        $product_machines = DB::table('product_machines')
            ->join('machines', 'product_machines.machine_id', '=', 'machines.id')
            ->select('product_machines.*', 'machines.machine')
            ->where('product_machines.product_id', $product_id)
            ->orderBy('product_machines.id', 'asc')
            ->get();

        $machines = DB::table('machines')
            ->select('id', 'machine')
            ->get();

        $products = DB::table('product')
            ->select('id', 'productname')
            ->where('id', $product_id)
            ->first();
        
        return view('Daily_Production.product_machines', compact('product_machines', 'products', 'machines'))->with('id', $product_id);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('product-create');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rules = array(
            'machine' => 'required|exists:machines,id',
            'product_id' => 'required|exists:product,id',
            'semi_price' => 'nullable|numeric|min:0',
            'full_price' => 'nullable|numeric|min:0',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $existing = ProductMachines::where('machine_id', $request->machine)
            ->where('product_id', $request->product_id)
            ->first();
        
        if ($existing) {
            return response()->json(['errors' => ['This machine is already assigned to this product.']]);
        }

        $product_machine = new ProductMachines();
        $product_machine->machine_id = $request->input('machine');
        $product_machine->product_id = $request->input('product_id');
        $product_machine->semi_price = $request->input('semi_price');
        $product_machine->full_price = $request->input('full_price');
        $product_machine->status = 1; 
        $product_machine->create_by = Auth::id();
        $product_machine->save();

        return response()->json(['success' => 'Machine Added successfully.']);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $permission = $user->can('product-edit');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if (request()->ajax()) {
            $data = ProductMachines::findOrFail($id);
            return response()->json(['result' => $data]);
        }
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $permission = $user->can('product-edit');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $rules = array(
            'machine' => 'required|exists:machines,id',
            'semi_price' => 'nullable|numeric|min:0',
            'full_price' => 'nullable|numeric|min:0',
        );

        $error = Validator::make($request->all(), $rules);

        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $existing = ProductMachines::where('machine_id', $request->machine)
            ->where('product_id', $request->product_id)
            ->where('id', '!=', $request->hidden_id)
            ->first();
        
        if ($existing) {
            return response()->json(['errors' => ['This machine is already assigned to this product.']]);
        }

        $form_data = array(
            'machine_id' => $request->machine,
            'semi_price' => $request->semi_price,
            'full_price' => $request->full_price,
            'update_by' => Auth::id(),
        );

        ProductMachines::whereId($request->hidden_id)->update($form_data);

        return response()->json(['success' => 'Machine is successfully updated']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $permission = $user->can('product-delete');
        if(!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        try {
            $data = ProductMachines::findOrFail($id);
            $data->delete();
            return response()->json(['success' => 'Machine deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete machine'], 500);
        }
    }

    public function Machine_list_sel2(Request $request)
    {
        if ($request->ajax()) {
            $page = $request->input('page', 1);
            $product = $request->input('product');
            $term = $request->input('term', '');
            $resultCount = 25;

            $offset = ($page - 1) * $resultCount;

            $query = ProductMachines::join('machines', 'product_machines.machine_id', '=', 'machines.id')
                ->where('product_machines.product_id', $product)
                ->select(
                    DB::raw('machines.id as id'),  
                    DB::raw('machines.machine as text')
                );

            if (!empty($term)) {
                $query->where('machines.machine', 'LIKE', '%' . $term . '%');
            }

            $breeds = $query->orderBy('machines.machine')
                ->skip($offset)
                ->take($resultCount)
                ->get();

            $count = ProductMachines::where('product_id', $product)->count();
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
}