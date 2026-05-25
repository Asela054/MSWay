<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeDevices extends Model
{
    protected $table = 'employee_assigned_devices';

    public function assignedDevice()
    {
        return $this->belongsTo(AssignedDevice::class, 'device_type', 'id');
    }

    // Returns device name whether device_type is a word or a numeric ID
    public function getDeviceNameAttribute()
    {
        if (is_numeric($this->device_type)) {
            $device = $this->assignedDevice;
            return $device ? $device->device_name : $this->device_type;
        }
        return $this->device_type;
    }
}