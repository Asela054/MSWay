<?php

namespace App\Http\Controllers\Production_Module_Opma;

use App\Http\Controllers\Controller;
use App\ProductionModule_Opma\Product;
use App\ProductionModule_Opma\Size;
use App\ProductionModule_Opma\StylePart;
use App\ProductionModule_Opma\StylePartSize;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;

class StylePartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index($style_id)
    {
        $user = auth()->user();
        $permission = $user->can('product-list');
        if (!$permission) {
            abort(403);
        }

        $style = Product::findOrFail($style_id);
        $sizes = Size::orderBy('id', 'asc')->get();
        $currency = DB::table('opma_currency_types')->select('id', 'currency')->get();
        return view('Opma_Production.Daily_Production.stylepart', compact('sizes', 'currency', 'style_id', 'style'));
    }

    public function list($style_id)
    {
        $user = auth()->user();
        $permission = $user->can('product-list');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $parts = DB::table('opma_style_parts as sp')
            ->leftJoin('opma_currency_types as c', 'c.id', '=', 'sp.currency_type')
            ->where('sp.style_id', $style_id)
            ->where('sp.status', 1)
            ->select(
                'sp.id',
                'sp.style_details',
                'sp.part_name',
                'sp.scales',
                'sp.unit_price',
                'c.currency as currency_name'
            )
            ->orderBy('sp.id', 'desc')
            ->get();

        $part_ids = $parts->pluck('id')->toArray();
        $size_names = Size::pluck('size', 'id')->toArray();

        $part_sizes = array();
        if (!empty($part_ids)) {
            $rows = DB::table('opma_style_part_sizes')
                ->whereIn('opma_style_part_id', $part_ids)
                ->get();
            foreach ($rows as $row) {
                if (isset($size_names[$row->opma_size_id])) {
                    $part_sizes[$row->opma_style_part_id][] = $size_names[$row->opma_size_id];
                }
            }
        }

        $data = array();
        foreach ($parts as $part) {
            $data[] = array(
                'id'            => $part->id,
                'style_details' => $part->style_details,
                'part_name'     => $part->part_name,
                'sizes'         => isset($part_sizes[$part->id]) ? implode(', ', $part_sizes[$part->id]) : '',
                'scales'        => $part->scales,
                'currency_name' => $part->currency_name,
                'unit_price'    => $part->unit_price,
            );
        }

        return response()->json(['data' => $data]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('product-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'style_id'      => 'required',
            'style_details' => 'required',
            'part_name'     => 'required',
            'scales'        => 'required',
            'unit_price'    => 'required|numeric'
        );
        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        DB::beginTransaction();
        try {
            $part = new StylePart;
            $part->style_id = $request->input('style_id');
            $part->style_details = $request->input('style_details');
            $part->part_name = $request->input('part_name');
            $part->scales = $request->input('scales');
            $part->currency_type = $request->input('currency');
            $part->unit_price = $request->input('unit_price');
            $part->status = 1;
            $part->create_by = $user->id;
            $part->update_by = $user->id;
            $part->save();

            if (!empty($request->sizes)) {
                foreach ($request->sizes as $size_id) {
                    $partSize = new StylePartSize;
                    $partSize->opma_style_part_id = $part->id;
                    $partSize->opma_size_id = $size_id;
                    $partSize->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['Something went wrong. Please try again.']]);
        }

        return response()->json(['success' => 'Style Part Added Successfully.']);
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        if (request()->ajax()) {
            $data = StylePart::findOrFail($id);
            $sizes = StylePartSize::where('opma_style_part_id', $id)->pluck('opma_size_id')->toArray();
            return response()->json(['result' => $data, 'sizes' => $sizes]);
        }
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('product-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $rules = array(
            'style_details' => 'required',
            'part_name'     => 'required',
            'scales'        => 'required',
            'unit_price'    => 'required|numeric'
        );
        $error = Validator::make($request->all(), $rules);
        if ($error->fails()) {
            return response()->json(['errors' => $error->errors()->all()]);
        }

        DB::beginTransaction();
        try {
            $part = StylePart::findOrFail($request->hidden_id);

            $part->update(array(
                'style_details' => $request->style_details,
                'part_name'     => $request->part_name,
                'scales'        => $request->scales,
                'currency_type' => $request->currency,
                'unit_price'    => $request->unit_price,
                'update_by'     => $user->id
            ));

            $new_sizes = $request->input('sizes', array());
            $existing_sizes = StylePartSize::where('opma_style_part_id', $request->hidden_id)
                ->pluck('opma_size_id')
                ->toArray();

            $to_add = array_diff($new_sizes, $existing_sizes);
            $to_remove = array_diff($existing_sizes, $new_sizes);

            if (!empty($to_remove)) {
                StylePartSize::where('opma_style_part_id', $request->hidden_id)
                    ->whereIn('opma_size_id', $to_remove)
                    ->delete();
            }

            if (!empty($to_add)) {
                foreach ($to_add as $size_id) {
                    $partSize = new StylePartSize;
                    $partSize->opma_style_part_id = $request->hidden_id;
                    $partSize->opma_size_id = $size_id;
                    $partSize->save();
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['Something went wrong. Please try again.']]);
        }

        return response()->json(['success' => 'Data is successfully updated']);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('product-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $data = StylePart::findOrFail($id);
        StylePartSize::where('opma_style_part_id', $id)->delete();
        $data->status = 3;
        $data->update_by = $user->id;
        $data->save();
        return response()->json(['success' => 'Data is successfully deleted']);
    }
}