<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Excel;
use Illuminate\Http\Request;


class RptEmployeeCarderReportController extends Controller
{
    protected $serviceHeaders = array('Below 3 Months', '3 Months', '6 Months', '1 Year completed and above');
    protected $ageHeaders = array('Below 25', '26-35', '36-45', '46-55', '56 Above');
    protected $reportNotes = array('Followed up on employee attendance and punctuality issues.');
    protected $preparedBy = ' ';



    public function index()
    {
        $user = Auth::user();
        $permission = $user->can('employee-report');
        if (!$permission) {
            abort(403);
        }

        return view('Report.employee_carder_report');
    }

    public function generatePdf(Request $request)
    {
         $user = Auth::user();
        if (!$user || !$user->can('employee-report')) {
            abort(403);
        }

        $this->validate($request, array(
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ));

        $report = $this->buildReport($request->input('from_date'), $request->input('to_date'));

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('Report.employee_carder_report_pdf', array('report' => $report));
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download($this->fileName($report) . '.pdf');
    }

    public function generateExcel(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->can('employee-report')) {
            abort(403);
        }

       $this->validate($request, array(
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
        ));


        $report = $this->buildReport($request->input('from_date'), $request->input('to_date'));

        Excel::create($this->fileName($report), function ($excel) use ($report) {
            $excel->setTitle('Employee Carder Report');
            $excel->sheet('Carder Report', function ($sheet) use ($report) {
                $this->fillSheet($sheet, $report);
            });
        })->download('xlsx');
    }

 


    protected function fileName($report)
    {
        return 'Carder_Report_' . $report['from'] . '_to_' . $report['to'];
    }

    protected function fmtDate($value)
    {
        $time = strtotime($value);
        return $time ? date('Y.m.d', $time) : '';
    }

    protected function clean($value, $default)
    {
        $value = trim((string) $value);
        return $value === '' ? $default : $value;
    }

    protected function parseDate($value)
    {
        $value = trim((string) $value);
        if ($value === '' || strpos($value, '0000') === 0) {
            return null;
        }
        try {
            $date = new \DateTime($value);
        } catch (\Exception $e) {
            return null;
        }
        if ((int) $date->format('Y') < 1900) {
            return null;
        }
        return $date;
    }

    /**
     * Business Unit  = companies.name   (employees.emp_company)
     * "Company" col  = departments.name (employees.emp_department)
     * Designation    = job_titles.title (employees.emp_job_code)
     */
    protected function baseQuery()
    {
        return DB::table('employees')
            ->leftJoin('departments', 'departments.id', '=', 'employees.emp_department')
            ->leftJoin('companies', 'companies.id', '=', 'employees.emp_company')
            ->leftJoin('job_titles', 'job_titles.id', '=', 'employees.emp_job_code')
            ->where('employees.deleted', 0)
            ->select(
                'employees.id',
                'employees.emp_gender',
                'employees.emp_birthday',
                'employees.emp_join_date',
                'employees.emp_permanent_date',
                'employees.resignation_date',
                'departments.name as department_name',
                'companies.name as company_name',
                'job_titles.title as designation'
            );
    }

    protected function buildReport($from, $to)
    {
        $from = date('Y-m-d', strtotime($from));
        $to = date('Y-m-d', strtotime($to));

        // joined within the range
        $recruits = $this->baseQuery()
            ->whereDate('employees.emp_join_date', '>=', $from)
            ->whereDate('employees.emp_join_date', '<=', $to)
            ->get();

        // resigned within the range
        $resigned = $this->baseQuery()
            ->where('employees.is_resigned', 1)
            ->whereDate('employees.resignation_date', '>=', $from)
            ->whereDate('employees.resignation_date', '<=', $to)
            ->get();

        // confirmed (permanent) within the range
        $confirmed = $this->baseQuery()
            ->whereDate('employees.emp_permanent_date', '>=', $from)
            ->whereDate('employees.emp_permanent_date', '<=', $to)
            ->get();

        // employees on the roll as at the "to" date (used for carder / service / age tables)
        $active = $this->baseQuery()
            ->whereDate('employees.emp_join_date', '<=', $to)
            ->where(function ($q) use ($to) {
                $q->where('employees.is_resigned', 0)
                    ->orWhereNull('employees.is_resigned')
                    ->orWhereDate('employees.resignation_date', '>', $to);
            })
            ->get();

        return $this->assemble($from, $to, $recruits, $resigned, $confirmed, $active);
    }

    /**
     * Pure data shaping (no DB) - $recruits / $resigned / $confirmed / $active are
     * iterables of objects with the columns selected in baseQuery().
     */
    protected function assemble($from, $to, $recruits, $resigned, $confirmed, $active)
    {
        $recruitRows = $this->groupMovements($recruits, 'emp_join_date');
        $resignRows = $this->groupMovements($resigned, 'resignation_date');
        $confirmRows = $this->groupMovements($confirmed, 'emp_permanent_date');

        $roll = $this->aggregateActive($active, $to);

        return array(
            'from'         => $from,
            'to'           => $to,
            'from_label'   => $this->fmtDate($from),
            'to_label'     => $this->fmtDate($to),
            'generated_at' => date('Y.m.d H:i'),
            'recruitments' => $recruitRows,
            'resignations' => $resignRows,
            'confirmed'    => $confirmRows,
            'summary'      => array(
                array('Recruitments', $this->sumCount($recruitRows)),
                array('Confirmed Recruitment', $this->sumCount($confirmRows)),
                array('Resignations', $this->sumCount($resignRows)),
            ),
            'carder'       => $roll['carder'],
            'service'      => $roll['service'],
            'age'          => $roll['age'],
            'notes'        => $this->reportNotes,
            'prepared_by'  => $this->preparedBy,
        );
    }

    protected function sumCount($rows)
    {
        $total = 0;
        foreach ($rows as $group) {
            $total += $group['count'];
        }
        return $total;
    }

    protected function groupMovements($rows, $dateField)
    {
        $tmp = array();
        foreach ($rows as $r) {
            $date = $this->parseDate($r->$dateField);
            if (!$date) {
                continue;
            }
            $designation = $this->clean($r->designation, '-');
            $company = $this->clean($r->company_name, 'Unassigned');
            $key = $company . '|' . $date->format('Y-m-d') . '|' . $designation;

            if (!isset($tmp[$key])) {
                $tmp[$key] = array(
                    'company'     => $company,
                    'designation' => $designation,
                    'date'        => $date->format('Y.m.d'),
                    'count'       => 0,
                );
            }
            $tmp[$key]['count']++;
        }

        ksort($tmp, SORT_NATURAL | SORT_FLAG_CASE);

        $groups = array();
        foreach ($tmp as $row) {
            $c = $row['company'];
            if (!isset($groups[$c])) {
                $groups[$c] = array('company' => $c, 'count' => 0, 'rows' => array());
            }
            $groups[$c]['rows'][] = array('designation' => $row['designation'], 'date' => $row['date'], 'count' => $row['count']);
            $groups[$c]['count'] += $row['count'];
        }
        return array_values($groups);
    }

    protected function serviceBucket($joinDate, $toDate)
    {
        $join = $this->parseDate($joinDate);
        if (!$join) {
            return null;
        }
        $diff = $join->diff($toDate);
        if ($diff->invert) {
            return 0;
        }
        $months = ($diff->y * 12) + $diff->m;
        if ($months < 3) {
            return 0;
        }
        if ($months < 6) {
            return 1;
        }
        if ($months < 12) {
            return 2;
        }
        return 3;
    }

    protected function ageBucket($birthday, $toDate)
    {
        $dob = $this->parseDate($birthday);
        if (!$dob) {
            return null;
        }
        $age = $dob->diff($toDate)->y;
        if ($age <= 25) {
            return 0;
        }
        if ($age <= 35) {
            return 1;
        }
        if ($age <= 45) {
            return 2;
        }
        if ($age <= 55) {
            return 3;
        }
        return 4;
    }

    protected function aggregateActive($employees, $to)
    {
        $toDate = new \DateTime($to);

        $carder = array();   // business unit => department => array(male, female, count)
        $service = array();  // "Business Unit - Department" => array(4 buckets)
        $age = array();      // "Business Unit - Department" => array(5 buckets)
        $missingDob = 0;

        foreach ($employees as $e) {
            $bu = $this->clean($e->company_name, 'Unassigned');
            $dep = $this->clean($e->department_name, 'Unassigned');
            $label = $bu . ' - ' . $dep;

            // ---- carder (gender)
            if (!isset($carder[$bu][$dep])) {
                $carder[$bu][$dep] = array('male' => 0, 'female' => 0, 'count' => 0);
            }
            $gender = strtolower(substr(trim((string) $e->emp_gender), 0, 1));
            if ($gender === 'm') {
                $carder[$bu][$dep]['male']++;
            } elseif ($gender === 'f') {
                $carder[$bu][$dep]['female']++;
            }
            $carder[$bu][$dep]['count']++;

            // ---- service period
            $sb = $this->serviceBucket($e->emp_join_date, $toDate);
            if ($sb !== null) {
                if (!isset($service[$label])) {
                    $service[$label] = array(0, 0, 0, 0);
                }
                $service[$label][$sb]++;
            }

            // ---- age
            $ab = $this->ageBucket($e->emp_birthday, $toDate);
            if ($ab === null) {
                $missingDob++;
            } else {
                if (!isset($age[$label])) {
                    $age[$label] = array(0, 0, 0, 0, 0);
                }
                $age[$label][$ab]++;
            }
        }

        // ---- shape carder
        ksort($carder, SORT_NATURAL | SORT_FLAG_CASE);
        $groups = array();
        $tm = 0;
        $tf = 0;
        $tc = 0;
        foreach ($carder as $bu => $deps) {
            ksort($deps, SORT_NATURAL | SORT_FLAG_CASE);
            $rows = array();
            $gm = 0;
            $gf = 0;
            $gc = 0;
            foreach ($deps as $dep => $v) {
                $rows[] = array('company' => $dep, 'male' => $v['male'], 'female' => $v['female'], 'count' => $v['count']);
                $gm += $v['male'];
                $gf += $v['female'];
                $gc += $v['count'];
            }
            $groups[] = array('business_unit' => $bu, 'rows' => $rows, 'male' => $gm, 'female' => $gf, 'count' => $gc);
            $tm += $gm;
            $tf += $gf;
            $tc += $gc;
        }

        return array(
            'carder'  => array('groups' => $groups, 'male' => $tm, 'female' => $tf, 'count' => $tc),
            'service' => $this->shapeMatrix($service, $this->serviceHeaders, 0),
            'age'     => $this->shapeMatrix($age, $this->ageHeaders, $missingDob),
        );
    }

    protected function shapeMatrix($data, $headers, $missingDob)
    {
        ksort($data, SORT_NATURAL | SORT_FLAG_CASE);

        $totals = array_fill(0, count($headers), 0);
        $rows = array();
        $grand = 0;
        foreach ($data as $label => $values) {
            $count = array_sum($values);
            $rows[] = array('label' => $label, 'values' => $values, 'count' => $count);
            foreach ($values as $i => $v) {
                $totals[$i] += $v;
            }
            $grand += $count;
        }

        return array(
            'headers'     => $headers,
            'rows'        => $rows,
            'totals'      => $totals,
            'count'       => $grand,
            'missing_dob' => $missingDob,
        );
    }

    // ------------------------------------------------------------------
    //  Excel layout (same look as the management report sample)
    // ------------------------------------------------------------------

    protected function fillSheet($sheet, $report)
    {
        $sheet->setWidth(array('A' => 36, 'B' => 30, 'C' => 26, 'D' => 22, 'E' => 26, 'F' => 15, 'G' => 15));

        // title
        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'EMPLOYEE CARDER REPORT');
        $this->xl($sheet, 'A1:G1', array('bg' => '1F4E78', 'color' => 'FFFFFF', 'bold' => true, 'size' => 18, 'align' => 'center', 'border' => true));
        $sheet->setHeight(1, 32);

        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Period - ' . $report['from_label'] . ' to ' . $report['to_label']);
        $this->xl($sheet, 'A2:G2', array('bold' => true, 'align' => 'left'));

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', 'Generated on - ' . $report['generated_at']);
        $this->xl($sheet, 'A3:G3', array('align' => 'left'));

        $r = 5;
        $r = $this->xlMovement($sheet, $r, "\xE2\x9C\x85  Recruitments", 'Joined Date', $report['recruitments']);
        $r = $this->xlMovement($sheet, $r, "\xE2\x9C\x85  Resignations", 'Resignation Date', $report['resignations']);
        $r = $this->xlMovement($sheet, $r, "\xE2\x9C\x85  Confirmed Recruitments", 'Confirmed Date', $report['confirmed']);
        $r = $this->xlSummary($sheet, $r, $report['summary']);
        $r = $this->xlCarder($sheet, $r, $report['carder']);
        $r = $this->xlMatrix($sheet, $r, 'Service Period Analysis', $report['service']);
        $r = $this->xlMatrix($sheet, $r, 'Age Wise Analysis', $report['age']);
        $this->xlNotes($sheet, $r, $report);
    }

    /** Additional Notes / Thank you / Prepared by */
    protected function xlNotes($sheet, $r, $report)
    {
        $notes = isset($report['notes']) ? $report['notes'] : array();
        $preparedBy = isset($report['prepared_by']) ? $report['prepared_by'] : '';

        $sheet->mergeCells('A' . $r . ':G' . $r);
        $sheet->setCellValue('A' . $r, 'Additional Notes');
        $this->xl($sheet, 'A' . $r . ':G' . $r, array('bg' => 'FFFF00', 'bold' => true, 'align' => 'left', 'border' => true));
        $r++;

        $sheet->mergeCells('A' . $r . ':G' . $r);
        $sheet->setCellValue('A' . $r, 'Employee Disciplinary Update');
        $this->xl($sheet, 'A' . $r . ':G' . $r, array('bg' => '92D050', 'bold' => true, 'align' => 'left', 'border' => true));
        $r++;

        foreach ($notes as $note) {
            $sheet->mergeCells('A' . $r . ':G' . $r);
            $sheet->setCellValue('A' . $r, '*' . $note);
            $this->xl($sheet, 'A' . $r . ':G' . $r, array('bold' => true, 'align' => 'left', 'border' => true));
            $r++;
        }

        $r++;
        $sheet->setCellValue('A' . $r, 'Thank you');
        $this->xl($sheet, 'A' . $r . ':A' . $r, array('size' => 12, 'align' => 'left'));
        $r += 4; // 3 blank rows = space for the signature

        if ($preparedBy !== '') {
            $sheet->setCellValue('A' . $r, $preparedBy);
            $this->xl($sheet, 'A' . $r . ':A' . $r, array('bold' => true, 'size' => 12, 'align' => 'left'));
        }
    }

    /** Apply fill / font / alignment / border to a range. */
    protected function xl($sheet, $range, $o)
    {
        $bg = isset($o['bg']) ? $o['bg'] : null;
        $color = isset($o['color']) ? $o['color'] : '000000';
        $bold = isset($o['bold']) ? $o['bold'] : false;
        $size = isset($o['size']) ? $o['size'] : 11;
        $align = isset($o['align']) ? $o['align'] : 'left';

        $sheet->cells($range, function ($cells) use ($bg, $color, $bold, $size, $align) {
            if ($bg) {
                $cells->setBackground('#' . $bg);
            }
            $cells->setFontColor('#' . $color);
            $cells->setFont(array('family' => 'Calibri', 'size' => $size, 'bold' => $bold));
            $cells->setAlignment($align);
            $cells->setValignment('center');
        });

        if (!empty($o['border'])) {
            $sheet->setBorder($range, 'thin');
        }
    }

    protected function xlSection($sheet, $r, $title, $lastCol, $bg, $size = 11)
    {
        $sheet->mergeCells('A' . $r . ':' . $lastCol . $r);
        $sheet->setCellValue('A' . $r, $title);
        $this->xl($sheet, 'A' . $r . ':' . $lastCol . $r, array('bg' => $bg, 'bold' => true, 'size' => $size, 'align' => 'left', 'border' => true));
    }

    protected function xlHeader($sheet, $r, $headers, $lastCol, $bg, $color)
    {
        $sheet->row($r, $headers);
        $this->xl($sheet, 'A' . $r . ':' . $lastCol . $r, array('bg' => $bg, 'color' => $color, 'bold' => true, 'size' => 11, 'align' => 'center', 'border' => true));
    }

    protected function xlEmpty($sheet, $r, $lastCol)
    {
        $sheet->mergeCells('A' . $r . ':' . $lastCol . $r);
        $sheet->setCellValue('A' . $r, 'No records for the selected period');
        $this->xl($sheet, 'A' . $r . ':' . $lastCol . $r, array('align' => 'center', 'border' => true));
        return $r + 1;
    }

    /** Recruitments / Resignations / Confirmed - No | Designation | Company (merged per company) | Date | Count */
    protected function xlMovement($sheet, $r, $title, $dateHeader, $groups)
    {
        $this->xlSection($sheet, $r, $title, 'E', 'D9EAD3');
        $r++;
        $this->xlHeader($sheet, $r, array('No', 'Designation', 'Company', $dateHeader, 'Count'), 'E', '1F4E78', 'FFFFFF');
        $r++;

        if (empty($groups)) {
            return $this->xlEmpty($sheet, $r, 'E') + 1;
        }

        $start = $r;
        $no = 0;
        foreach ($groups as $g) {
            $gStart = $r;
            $first = true;
            foreach ($g['rows'] as $row) {
                $no++;
                $sheet->row($r, array($no, $row['designation'], $first ? $g['company'] : '', $row['date'], $row['count']));
                $first = false;
                $r++;
            }
            if ($r - 1 > $gStart) {
                $sheet->mergeCells('C' . $gStart . ':C' . ($r - 1));
            }
        }
        $end = $r - 1;
        $this->xl($sheet, 'A' . $start . ':E' . $end, array('align' => 'center', 'border' => true));
        $this->xl($sheet, 'B' . $start . ':C' . $end, array('align' => 'left', 'border' => true));

        return $r + 1;
    }

    /** Metric | Count */
    protected function xlSummary($sheet, $r, $summary)
    {
        $this->xlSection($sheet, $r, "\xF0\x9F\x93\x88  Recruitments and Resignations", 'B', 'D9EAD3');
        $r++;
        $this->xlHeader($sheet, $r, array('Metric', 'Count'), 'B', '1F4E78', 'FFFFFF');
        $r++;
        $start = $r;
        foreach ($summary as $row) {
            $sheet->row($r, array($row[0], $row[1]));
            $r++;
        }
        $end = $r - 1;
        $this->xl($sheet, 'A' . $start . ':B' . $end, array('bold' => true, 'align' => 'left', 'border' => true));
        $this->xl($sheet, 'B' . $start . ':B' . $end, array('bold' => true, 'align' => 'center', 'border' => true));

        return $r + 1;
    }

    /** No | Business Unit | Company | Male | Female | Count */
    protected function xlCarder($sheet, $r, $carder)
    {
        $this->xlSection($sheet, $r, 'Age Carder Summary', 'F', 'D9EAD3');
        $r++;
        $this->xlHeader($sheet, $r, array('No', 'Business Unit', 'Company', 'Male', 'Female', 'Count'), 'F', '1F4E78', 'FFFFFF');
        $r++;

        if (empty($carder['groups'])) {
            return $this->xlEmpty($sheet, $r, 'F') + 1;
        }

        $no = 0;
        foreach ($carder['groups'] as $g) {
            $no++;
            $start = $r;
            $first = true;
            foreach ($g['rows'] as $row) {
                $sheet->row($r, array($first ? $no : '', $first ? $g['business_unit'] : '', $row['company'], $row['male'], $row['female'], $row['count']));
                $first = false;
                $r++;
            }
            $end = $r - 1;

            $this->xl($sheet, 'A' . $start . ':F' . $end, array('align' => 'center', 'border' => true));
            $this->xl($sheet, 'B' . $start . ':C' . $end, array('align' => 'left', 'border' => true));
            if ($end > $start) {
                $sheet->mergeCells('A' . $start . ':A' . $end);
                $sheet->mergeCells('B' . $start . ':B' . $end);
            }

            if (count($g['rows']) > 1) {
                $sheet->row($r, array('Total - ' . $g['business_unit'], '', '', $g['male'], $g['female'], $g['count']));
                $sheet->mergeCells('A' . $r . ':C' . $r);
                $this->xl($sheet, 'A' . $r . ':F' . $r, array('bg' => 'FFFF00', 'bold' => true, 'size' => 12, 'align' => 'center', 'border' => true));
                $r++;
            }
        }

        // Total - Male / Female
        $sheet->row($r, array('Total - Male / Female', '', '', $carder['male'], $carder['female'], $carder['count']));
        $sheet->mergeCells('A' . $r . ':C' . $r);
        $this->xl($sheet, 'A' . $r . ':F' . $r, array('bg' => '92D050', 'bold' => true, 'size' => 12, 'align' => 'center', 'border' => true));
        $r++;

        // Grand total
        $sheet->row($r, array('Grand Total', '', '', $carder['count'], '', ''));
        $sheet->mergeCells('A' . $r . ':C' . $r);
        $sheet->mergeCells('D' . $r . ':F' . $r);
        $this->xl($sheet, 'A' . $r . ':F' . $r, array('bg' => 'FFFF00', 'bold' => true, 'size' => 16, 'align' => 'center', 'border' => true));
        $sheet->setHeight($r, 24);

        return $r + 2;
    }

    /** Service period / Age wise: Company | buckets... | Count */
    protected function xlMatrix($sheet, $r, $title, $data)
    {
        $cols = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
        $lastCol = $cols[count($data['headers']) + 1];

        $this->xlSection($sheet, $r, $title, $lastCol, 'C5D3FF', 14);
        $r++;
        $this->xlHeader($sheet, $r, array_merge(array('Company'), $data['headers'], array('Count')), $lastCol, 'FFC000', '000000');
        $r++;

        if (empty($data['rows'])) {
            return $this->xlEmpty($sheet, $r, $lastCol) + 1;
        }

        $start = $r;
        foreach ($data['rows'] as $row) {
            $sheet->row($r, array_merge(array($row['label']), $row['values'], array($row['count'])));
            $r++;
        }
        $end = $r - 1;
        $this->xl($sheet, 'A' . $start . ':' . $lastCol . $end, array('align' => 'center', 'border' => true));
        $this->xl($sheet, 'A' . $start . ':A' . $end, array('align' => 'left', 'border' => true));

        // totals
        $sheet->row($r, array_merge(array('Grand Total'), $data['totals'], array($data['count'])));
        $this->xl($sheet, 'A' . $r . ':' . $lastCol . $r, array('bg' => 'FFFF00', 'bold' => true, 'size' => 12, 'align' => 'center', 'border' => true));
        $this->xl($sheet, 'A' . $r . ':A' . $r, array('bg' => 'FFFF00', 'bold' => true, 'size' => 12, 'align' => 'left', 'border' => true));
        $r++;

        if (!empty($data['missing_dob'])) {
            $sheet->mergeCells('A' . $r . ':' . $lastCol . $r);
            $sheet->setCellValue('A' . $r, 'Note: ' . $data['missing_dob'] . ' employee(s) have no valid birthday and are not included in this table.');
            $this->xl($sheet, 'A' . $r . ':' . $lastCol . $r, array('align' => 'left'));
            $r++;
        }

        return $r + 1;
    }
}