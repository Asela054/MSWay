<?php

namespace App\Helpers;

class EmployeeHelper
{
    /**
     * Configuration for employee display formats
     * 
     * @var array
     */
    protected static $displayConfig = [
        'format' => 'initial_calling',  // Default format
        'separator' => ' - ',           // Default separator
    ];

    /**
     * Set display configuration
     * 
     * @param array $config
     */
    public static function setDisplayConfig(array $config)
    {
        static::$displayConfig = array_merge(static::$displayConfig, $config);
    }

    /**
     * Get employee display name based on configured format
     * 
     * @param object $employee
     * @return string
     */
    public static function getDisplayName($employee)
    {
        $format = static::$displayConfig['format'];
        $separator = static::$displayConfig['separator'];
        
        switch ($format) {
            case 'initial_only':
                return $employee->emp_name_with_initial;
                
            case 'calling_only':
                return $employee->calling_name;
                
            case 'initial_calling':
                return $employee->emp_name_with_initial . $separator . $employee->calling_name;
                
            case 'initial_id':
                return $employee->emp_name_with_initial . $separator . $employee->emp_id;
                
            case 'calling_id':
                return $employee->calling_name . $separator . $employee->emp_id;
                
            default:
                return $employee->emp_name_with_initial;
        }
    }
}