<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LeaveAprLevelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index()
    {
        $user       = auth()->user();
        $permission = $user->can('leave-appr-level-list');

        if (!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return view('Leave.leaveApprovelLevel');
    }


    public function list(Request $request)
    {
        $user       = auth()->user();
        $permission = $user->can('leave-appr-level-list');
        if (!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $query = DB::table('leave_approval_hierarchy as lah')
            ->join('departments as d',   'lah.department_id', '=', 'd.id')
            ->join('job_titles as jt',   'lah.title_id',      '=', 'jt.id')
            ->join('leave_approval_levels as lal', 'lah.approval_level', '=', 'lal.level')
            ->select(
                'lah.id',
                'd.name as department_name',
                'jt.title as job_title',
                'lah.approval_level',
                'lah.department_id'
            );

        // DataTables server-side params
        $totalRecords    = $query->count();
        $filteredRecords = $totalRecords;

        $search = $request->input('search.value');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('d.name',         'LIKE', "%{$search}%")
                  ->orWhere('jt.title',     'LIKE', "%{$search}%")
                  ->orWhere('lah.approval_level', 'LIKE', "%{$search}%");
            });
            $filteredRecords = $query->count();
        }

        $orderColumnIndex = $request->input('order.0.column', 0);
        $orderDir         = $request->input('order.0.dir', 'desc');
        $columns          = ['lah.id', 'd.name', 'jt.title', 'lah.approval_level'];
        $orderColumn      = $columns[$orderColumnIndex] ?? 'lah.id';
        $query->orderBy($orderColumn, $orderDir);

        $start  = $request->input('start',  0);
        $length = $request->input('length', 10);
        $data   = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw'            => intval($request->input('draw')),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data'            => $data,
        ]);
    }


    public function store(Request $request)
    {
        $user       = Auth::user();
        $permission = $user->can('leave-appr-level-create');
        if (!$permission) {
            return response()->json(['errors' => ['You do not have permission to create leave approval level.']]);
        }

        $department_id  = $request->input('department');
        $title_ids      = $request->input('title_id',      []);
        $approval_levels = $request->input('approval_level', []);

        if (empty($department_id)) {
            return response()->json(['errors' => ['Please select a department.']]);
        }

        if (empty($title_ids)) {
            return response()->json(['errors' => ['Please add at least one job title with an approval level.']]);
        }

        $seenTitles  = [];
        $seenLevels  = [];
        $rows        = [];

        foreach ($title_ids as $i => $title_id) {
            $level = $approval_levels[$i] ?? null;

            if (empty($title_id) || empty($level)) {
                return response()->json(['errors' => ['Each row must have both a Job Title and an Approval Level.']]);
            }

            if (in_array($title_id, $seenTitles)) {
                return response()->json(['errors' => ['Within the same department, a single Job Title cannot be assigned to two or more approval levels.']]);
            }

            if (in_array($level, $seenLevels)) {
                return response()->json(['errors' => ['Within the same department, a single Approval Level cannot be assigned to two or more job titles.']]);
            }

            $seenTitles[] = $title_id;
            $seenLevels[] = $level;
            $rows[]       = ['title_id' => $title_id, 'level' => $level];
        }

        $existingRows = DB::table('leave_approval_hierarchy')
            ->where('department_id', $department_id)
            ->get();

        $existingTitleIds = $existingRows->pluck('title_id')->toArray();
        $existingLevels   = $existingRows->pluck('approval_level')->toArray();

        foreach ($rows as $row) {
            if (in_array($row['title_id'], $existingTitleIds)) {
                return response()->json(['errors' => ['One or more job titles already have an approval level assigned for this department.']]);
            }
            if (in_array($row['level'], $existingLevels)) {
                return response()->json(['errors' => ['One or more approval levels are already assigned to a job title for this department.']]);
            }
        }

        $current_date_time = Carbon::now()->toDateTimeString();
        foreach ($rows as $row) {
            DB::table('leave_approval_hierarchy')->insert([
                'department_id'  => $department_id,
                'title_id'       => $row['title_id'],
                'approval_level' => $row['level'],
                'created_at'     => $current_date_time,
            ]);
        }

        return response()->json(['success' => 'Leave Approval Level added successfully.']);
    }


    public function edit($id)
    {
        $user       = auth()->user();
        $permission = $user->can('leave-appr-level-edit');
        if (!$permission) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // $id here is the department_id
        $department = DB::table('departments')->where('id', $id)->first();

        if (!$department) {
            return response()->json(['error' => 'Department not found.'], 404);
        }

        $rows = DB::table('leave_approval_hierarchy as lah')
            ->join('job_titles as jt', 'lah.title_id', '=', 'jt.id')
            ->where('lah.department_id', $id)
            ->select('lah.id', 'lah.title_id', 'jt.title as title_text', 'lah.approval_level')
            ->get();

        return response()->json([
            'department'      => $department,
            'hierarchy_rows'  => $rows,
        ]);
    }


    public function update(Request $request)
    {
        $user       = Auth::user();
        $permission = $user->can('leave-appr-level-edit');
        if (!$permission) {
            return response()->json(['errors' => ['You do not have permission to update leave approval level.']]);
        }

        $department_id   = $request->input('department');
        $row_ids         = $request->input('row_id',        []);
        $title_ids       = $request->input('title_id',      []);
        $approval_levels = $request->input('approval_level', []);

        if (empty($department_id)) {
            return response()->json(['errors' => ['Please select a department.']]);
        }

        if (empty($title_ids)) {
            return response()->json(['errors' => ['Please add at least one job title with an approval level.']]);
        }

        $seenTitles = [];
        $seenLevels = [];
        $rows       = [];

        foreach ($title_ids as $i => $title_id) {
            $level  = $approval_levels[$i] ?? null;
            $row_id = $row_ids[$i]         ?? null;

            if (empty($title_id) || empty($level)) {
                return response()->json(['errors' => ['Each row must have both a Job Title and an Approval Level.']]);
            }

            if (in_array($title_id, $seenTitles)) {
                return response()->json(['errors' => ['Within the same department, a single Job Title cannot be assigned to two or more approval levels.']]);
            }

            if (in_array($level, $seenLevels)) {
                return response()->json(['errors' => ['Within the same department, a single Approval Level cannot be assigned to two or more job titles.']]);
            }

            $seenTitles[] = $title_id;
            $seenLevels[] = $level;
            $rows[]       = ['row_id' => $row_id, 'title_id' => $title_id, 'level' => $level];
        }

        $submittedIds = array_values(array_filter($row_ids, function($v) { return !empty($v); }));

        $deleteQuery = DB::table('leave_approval_hierarchy')
            ->where('department_id', $department_id);
        if (count($submittedIds)) {
            $deleteQuery->whereNotIn('id', $submittedIds);
        }
        $deleteQuery->delete();

        $current_date_time = Carbon::now()->toDateTimeString();

        foreach ($rows as $row) {
            if (!empty($row['row_id'])) {
                DB::table('leave_approval_hierarchy')
                    ->where('id', $row['row_id'])
                    ->where('department_id', $department_id)
                    ->update([
                        'title_id'       => $row['title_id'],
                        'approval_level' => $row['level'],
                        'created_at'     => $current_date_time,
                    ]);
            } else {
                DB::table('leave_approval_hierarchy')->insert([
                    'department_id'  => $department_id,
                    'title_id'       => $row['title_id'],
                    'approval_level' => $row['level'],
                    'updated_at'     => $current_date_time,
                ]);
            }
        }

        return response()->json(['success' => 'Leave Approval Level updated successfully.']);
    }


    public function destroy($id)
    {
        $user       = Auth::user();
        $permission = $user->can('leave-appr-level-delete');
        if (!$permission) {
            return response()->json(['errors' => ['You do not have permission to remove leave approval level.']]);
        }

        // $id is a single leave_approval_hierarchy row id
        DB::table('leave_approval_hierarchy')->where('id', $id)->delete();

        return response()->json(['success' => 'Record removed successfully.']);
    }


    public function jobTitle_list(Request $request)
    {
        $page        = (int) $request->input('page', 1);
        $resultCount = 25;
        $offset      = ($page - 1) * $resultCount;
        $term        = $request->input('term', '');

        $breeds = DB::table('job_titles')
            ->where('title', 'LIKE', "%{$term}%")
            ->orderBy('id')
            ->skip($offset)
            ->take($resultCount)
            ->get([DB::raw('id as id'), DB::raw('title as text')]);

        $count      = DB::table('job_titles')->where('title', 'LIKE', "%{$term}%")->count();
        $morePages  = ($offset + $resultCount) < $count;

        return response()->json([
            'results'    => $breeds,
            'pagination' => ['more' => $morePages],
        ]);
    }


    public function apLevel_list2(Request $request)
    {
        $page        = (int) $request->input('page', 1);
        $resultCount = 25;
        $offset      = ($page - 1) * $resultCount;
        $term        = $request->input('term', '');

        $levels = DB::table('leave_approval_levels')
            ->where('level', 'LIKE', "%{$term}%")
            ->orderBy('level')
            ->skip($offset)
            ->take($resultCount)
            ->get([DB::raw('level as id'), DB::raw('level as text')]);

        $count     = DB::table('leave_approval_levels')->where('level', 'LIKE', "%{$term}%")->count();
        $morePages = ($offset + $resultCount) < $count;

        return response()->json([
            'results'    => $levels,
            'pagination' => ['more' => $morePages],
        ]);
    }


    public function getDepartmentHierarchy(Request $request)
    {
        $department_id = $request->input('department_id');

        if (!$department_id) {
            return response()->json(['rows' => []]);
        }

        $rows = DB::table('leave_approval_hierarchy as lah')
            ->join('job_titles as jt', 'lah.title_id', '=', 'jt.id')
            ->where('lah.department_id', $department_id)
            ->select('lah.id', 'lah.title_id', 'jt.title as title_text', 'lah.approval_level')
            ->get();

        return response()->json(['rows' => $rows]);
    }
}
