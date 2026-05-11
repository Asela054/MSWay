<?php

namespace App\Http\Controllers\ERP_KT;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ERPJobCreateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-list');
        if (!$permission) {
            abort(403);
        }
        return view('ERP_KT.job_create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-create');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $tableData = $request->input('tableData');
        if (empty($tableData) || !is_array($tableData)) {
            return response()->json(['errors' => ['No data provided.']]);
        }

        DB::beginTransaction();
        try {
            $firstRow = $tableData[0];
            $jobId = DB::table('kt_job_inquiry')->insertGetId([
                'customer_id' => $firstRow['col_1'],
                'inquiry_id'  => $firstRow['col_4'],
                'start_from'  => $firstRow['col_2'],
                'end_at'      => $firstRow['col_3'],
                'reading_hours' => round(Carbon::parse($firstRow['col_2'])->diffInMinutes(Carbon::parse($firstRow['col_3'])) / 60, 2),
                'job_description' => !empty($firstRow['col_8']) ? trim($firstRow['col_8']) : null,
                'remarks'         => !empty($firstRow['col_9']) ? trim($firstRow['col_9']) : null,
                'created_at'  => Carbon::now()->toDateTimeString(),
                'updated_at'  => Carbon::now()->toDateTimeString(),
            ]);
            foreach ($tableData as $row) {
                DB::table('kt_job_details')->insert([
                    'job_id'     => $jobId,
                    'emp_id'     => !empty($row['col_6']) ? trim($row['col_6']) : null,
                    'job_title'  => !empty($row['col_7']) ? trim($row['col_7']) : null,
                    'machine_id' => !empty($row['col_5']) ? trim($row['col_5']) : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
            DB::commit();
            return response()->json(['success' => 'Job Created Successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['Failed: ' . $e->getMessage()]]);
        }
    }

    public function edit($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $job = DB::table('kt_job_inquiry AS j')
            ->leftJoin('kt_inquiry_details AS d', 'd.id', '=', 'j.inquiry_id')
            ->leftJoin('kt_inquiries AS i',  'i.id', '=', 'j.inquiry_id')
            ->leftJoin('kt_customer AS c',  'c.id', '=', 'j.customer_id')
            ->where('j.id', $id)
            ->select('j.id', 'j.customer_id', 'c.name as customer_name', 'j.inquiry_id', 'd.inquiry', 'j.start_from', 'j.end_at','j.job_description', 'j.remarks')
            ->first();

        $details = DB::table('kt_job_details AS jd')
            ->leftJoin('employees AS e', 'e.emp_id', '=', 'jd.emp_id')
            ->leftJoin('kt_machines AS m', 'm.id', '=', 'jd.machine_id')
            ->leftJoin('job_titles AS jt', 'jt.id', '=', 'jd.job_title')
            ->where('jd.job_id', $id)
            ->select(
                'jd.id',
                'jd.machine_id',
                'm.machine_name',
                'jd.emp_id',
                DB::raw("CONCAT(e.emp_name_with_initial,' - ',e.calling_name) as emp_name"),
                'jd.job_title',
                'jt.title as job_title_name'
            )->get();
        return response()->json(['result' => $job, 'details' => $details]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-edit');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }

        $tableData = $request->input('tableData');

        if (empty($tableData) || !is_array($tableData)) {
            return response()->json(['errors' => ['No data provided.']]);
        }

        $firstRow = $tableData[0];
        $jobId = $request->input('hidden_id');

        DB::beginTransaction();
        try {
            DB::table('kt_job_inquiry')->where('id', $jobId)->update([
                'customer_id'     => $firstRow['col_1'],
                'inquiry_id'      => $firstRow['col_4'],
                'start_from'      => $firstRow['col_2'],
                'end_at'          => $firstRow['col_3'],
                'reading_hours'   => round(Carbon::parse($firstRow['col_2'])->diffInMinutes(Carbon::parse($firstRow['col_3'])) / 60, 2),
                'job_description' => !empty($firstRow['col_8']) ? trim($firstRow['col_8']) : null,
                'remarks'         => !empty($firstRow['col_9']) ? trim($firstRow['col_9']) : null,
                'updated_at'      => Carbon::now()->toDateTimeString(),
            ]);

            DB::table('kt_job_details')->where('job_id', $jobId)->delete();

            foreach ($tableData as $detailRow) {   
                DB::table('kt_job_details')->insert([
                    'job_id'     => $jobId,
                    'emp_id'     => !empty($detailRow['col_6']) ? trim($detailRow['col_6']) : null,
                    'job_title'  => !empty($detailRow['col_7']) ? trim($detailRow['col_7']) : null,
                    'machine_id' => !empty($detailRow['col_5']) ? trim($detailRow['col_5']) : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();
            return response()->json(['success' => 'Job Updated Successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['errors' => ['Failed: ' . $e->getMessage()]]);
        }
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $permission = $user->can('kt-job-delete');
        if (!$permission) {
            return response()->json(['error' => 'UnAuthorized'], 401);
        }
        DB::table('kt_job_inquiry')->where('id', $id)->update(['status' => 3]);

        return response()->json(['success' => 'Job Deleted Successfully.']);
    }


    //searches customers by name or ID
    public function customerList(Request $request)
    {
        $search = $request->term;
        $customers = DB::table('kt_customer')
            ->where('name', 'like', '%' . $search . '%')
            ->orWhere('id', 'like', '%' . $search . '%')
            ->get(['id', 'name']);

        $results = $customers->map(function ($c) {
            return ['id' => $c->id, 'text' => $c->name];
        });

        return response()->json(['results' => $results]);
    }

    //searches induiry list and filter approved inquires only
    public function inquiryList(Request $request)
    {
        $search = $request->term;
        $inquiries = DB::table('kt_inquiry_details')
            ->where('approve_status', 1)
            ->where('inquiry', 'like', '%' . $search . '%')
            ->get(['id', 'inquiry']);

        $results = $inquiries->map(function ($i) {
            return ['id' => $i->id, 'text' => $i->inquiry];
        });

        return response()->json(['results' => $results]);
    }

    public function machineList(Request $request)
    {
        $search = $request->term;
        $machines = DB::table('kt_machines')
            ->where('status', 1)
            ->where('machine_name', 'like', '%' . $search . '%')
            ->get(['id', 'machine_name']);

        $results = $machines->map(function ($m) {
            return ['id' => $m->id, 'text' => $m->machine_name];
        });

        return response()->json(['results' => $results]);
    }

    public function jobTitleList(Request $request)
    {
        $search = $request->term;
        $titles = DB::table('job_titles')
            ->where('title', 'like', '%' . $search . '%')
            ->get(['id', 'title']);
        return response()->json(['results' => $titles->map(function ($t) {
            return ['id' => $t->id, 'text' => $t->title];
        })]);
    }

    //job_title_id filter by employee
    public function employeeListByTitle(Request $request)
    {
        $search       = $request->term;
        $jobTitleId   = $request->job_title_id;
        $query = DB::table('employees AS e')
            ->join('job_titles AS jt', 'jt.id', '=', 'e.emp_job_code')
            ->where('e.deleted', 0)
            ->where('e.is_resigned', 0);
        if ($jobTitleId) {
            $query->where('e.emp_job_code', $jobTitleId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('e.emp_name_with_initial', 'like', '%' . $search . '%')
                    ->orWhere('e.calling_name', 'like', '%' . $search . '%');
            });
        }

        $employees = $query->get(['e.emp_id', DB::raw("CONCAT(e.emp_name_with_initial,' - ',e.calling_name) as emp_name")]);

        return response()->json(['results' => $employees->map(function ($e) {
            return ['id' => $e->emp_id, 'text' => $e->emp_name];
        })]);
    }
}
