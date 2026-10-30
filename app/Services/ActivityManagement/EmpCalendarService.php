<?php

namespace App\Services\ActivityManagement;

use App\ActivityManagement\EmpCalendar;

class EmpCalendarService
{
    /**
     * Retrieve all active employee calendars with related employee data.
     */
    public function getAllShedules()
    {
        return EmpCalendar::join('employees', 'emp_calendar.emp_id', '=', 'employees.emp_id')
            ->where('emp_calendar.status', '!=', 3)
            ->orderBy('emp_calendar.id', 'desc')
            ->select(
                'emp_calendar.*',
                'employees.emp_name_with_initial',
                'employees.calling_name'
            )
            ->get();
    }

    /**
     * Store a newly created employee calendar.
     */
    public function store(array $data)
    {
        $empCalendar = new EmpCalendar;
        $empCalendar->emp_id = $data['employee'] ?? null;
        $empCalendar->title = $data['title'] ?? null;
        $empCalendar->date = $data['date'] ?? null;
        $empCalendar->time = $data['time'] ?? null;
        $empCalendar->remark = $data['remark'] ?? null;
        $empCalendar->status = 1;
        $empCalendar->save();

        return $empCalendar;
    }

    /**
     * Find an employee calendar by ID, including employee display name.
     */
    public function find($id)
    {
        return EmpCalendar::join('employees', 'emp_calendar.emp_id', '=', 'employees.emp_id')
            ->where('emp_calendar.id', $id)
            ->select(
                'emp_calendar.*',
                'employees.emp_name_with_initial',
                'employees.calling_name'
            )
            ->firstOrFail();
    }

    /**
     * Update an existing employee calendar.
     */
    public function update($id, array $data)
    {
        EmpCalendar::whereId($id)->update([
            'emp_id' => $data['employee'] ?? null,
            'title' => $data['title'] ?? null,
            'date' => $data['date'] ?? null,
            'time' => $data['time'] ?? null,
            'remark' => $data['remark'] ?? null,
        ]);
    }

    /**
     * Soft-delete an employee calendar by setting its status to 3.
     */
    public function destroy($id)
    {
        $empCalendar = EmpCalendar::findOrFail($id);
        $empCalendar->status = 3;
        $empCalendar->save();
    }
}
