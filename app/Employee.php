<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{

    protected $table = 'employees';
    protected $guarded = [];
    protected $fillable = [
        'emp_id',
        'emp_etfno',
        'emp_etfno_a',
        'emp_first_name',
        'emp_med_name',
        'emp_last_name',
        'emp_fullname',
        'emp_name_with_initial',
        'calling_name',
        'emp_national_id',
        'emp_mobile',
        'emp_work_telephone',
        'tp1',
        'emp_birthday',
        'emp_address',
        'emp_address_2',
        'emp_addressT1',
        'emp_address_T2',
        'emp_email',
        'emp_other_email',
        'emp_join_date',
        'emp_permanent_date',
        'emp_assign_date',
        'emp_department',
        'emp_status',
        'emp_location',
        'emp_company',
        'emp_job_code',
        'emp_shift',
        'emp_drive_license',
        'emp_license_expire_date',
        'emp_gender',
        'emp_marital_status',
        'emp_nationality',
        'no_of_casual_leaves',
        'no_of_annual_leaves',
        'job_category_id',
        'work_category_id',
        'hierarchy_id',
        'financial_id',
        'ds_divition',
        'gsn_divition',
        'gsn_name',
        'gsn_contactno',
        'police_station',
        'police_contactno',
        'leave_approve_person',
        'is_resigned',
        'resignation_date',
        'resignation_remark',
        'deleted',
    ];

    public function country()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function attachments()
    {
        return $this->hasMany(EmployeeAttachment::class, 'emp_id', 'emp_id');
    }

    public function employeePicture()
    {
        return $this->hasOne(EmployeePicture::class, 'emp_id');
    }
}
