<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttachment extends Model
{

    protected $table = 'employee_attachments';
    protected $primaryKey = 'emp_ath_id';

    protected $fillable = [
        'emp_id',
        'emp_ath_file_name',
        'emp_ath_size',
        'emp_ath_type',
        'attachment_type',
        'emp_ath_by',
        'emp_ath_time',
        'empcomment',
        'insert_user',
        'created_at',
        'updated_at',
        'update_user',
        'update_date',
    ];
    
    public function attachment_type_rel()
    {
        return $this->belongsTo(AttachmentType::class,'attachment_type', 'id' );
    }
}
