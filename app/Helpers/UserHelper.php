<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Session;

class UserHelper
{
    /**
     * Apply user-based employee filtering to a query
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param int|null $userId - Optional user ID, defaults to session user
     * @return \Illuminate\Database\Query\Builder
     */
    public static function applyEmployeeFilter($query, $userId = null)
    {
        $userId = $userId ?? Session::get('users_id');
        
        if (!$userId) {
            return $query;
        }
        
        $userPayGroups = DB::table('user_has_pay_groups')
            ->where('user_id', $userId)
            ->pluck('group_id')
            ->toArray();
        
        if (!empty($userPayGroups)) {
            return static::filterByPayGroups($query, $userPayGroups);
        }
        
        $userEmployee = DB::table('users')
            ->join('employees', 'users.emp_id', '=', 'employees.emp_id')
            ->where('users.id', $userId)
            ->whereNotNull('employees.hierarchy_id')
            ->first(['employees.hierarchy_id']);
        
        if ($userEmployee && $userEmployee->hierarchy_id) {
            return static::filterByHierarchy($query, $userEmployee->hierarchy_id);
        }
        
        return $query;
    }
    
    /**
     * Filter employees by pay groups
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $groupIds
     * @return \Illuminate\Database\Query\Builder
     */
    protected static function filterByPayGroups($query, $groupIds)
    {
        return $query->whereIn('employees.id', function($subQuery) use ($groupIds) {
            $subQuery->select('payroll_profiles.emp_id')
                ->from('payroll_profiles')
                ->whereIn('payroll_profiles.employee_payday_id', $groupIds);
        });
    }
    
    /**
     * Filter employees by hierarchy (hide lower order numbers)
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param int $hierarchyId
     * @return \Illuminate\Database\Query\Builder
     */
    protected static function filterByHierarchy($query, $hierarchyId)
    {
        $userHierarchy = DB::table('company_hierarchies')
            ->where('id', $hierarchyId)
            ->first(['order_number']);
        
        if (!$userHierarchy) {
            return $query;
        }
        
        return $query->where(function($q) use ($userHierarchy) {
            $q->whereNull('employees.hierarchy_id')
                ->orWhereIn('employees.hierarchy_id', function($subQuery) use ($userHierarchy) {
                    $subQuery->select('id')
                        ->from('company_hierarchies')
                        ->where('order_number', '>=', $userHierarchy->order_number);
                });
        });
    }
    
    /**
     * Check if user has pay group restrictions
     * 
     * @param int|null $userId
     * @return bool
     */
    public static function hasPayGroupRestrictions($userId = null)
    {
        $userId = $userId ?? Session::get('users_id');
        
        if (!$userId) {
            return false;
        }
        
        return DB::table('user_has_pay_groups')
            ->where('user_id', $userId)
            ->exists();
    }
    
    /**
     * Check if user has hierarchy restrictions
     * 
     * @param int|null $userId
     * @return bool
     */
    public static function hasHierarchyRestrictions($userId = null)
    {
        $userId = $userId ?? Session::get('users_id');
        
        if (!$userId) {
            return false;
        }
        
        return DB::table('users')
            ->join('employees', 'users.emp_id', '=', 'employees.emp_id')
            ->where('users.id', $userId)
            ->whereNotNull('employees.hierarchy_id')
            ->exists();
    }
    
    /**
     * Get accessible employee IDs for a user (PDO version for non-Laravel)
     * 
     * @param int $userId
     * @param \PDO $pdo
     * @return array
     */
    public static function getAccessibleEmployeeIds($userId, $pdo = null)
    {
        if (!$userId) {
            return [];
        }
        
        if ($pdo === null) {
            return static::getAccessibleEmployeeIdsLaravel($userId);
        }
        
        $stmt = $pdo->prepare("SELECT group_id FROM user_has_pay_groups WHERE user_id = ?");
        $stmt->execute([$userId]);
        $userPayGroups = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        if (!empty($userPayGroups)) {
            $placeholders = implode(',', array_fill(0, count($userPayGroups), '?'));
            $stmt = $pdo->prepare("
                SELECT DISTINCT e.emp_id
                FROM payroll_profiles pp
                JOIN employees e ON pp.emp_id = e.id
                WHERE pp.employee_payday_id IN ($placeholders)
            ");
            $stmt->execute($userPayGroups);
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }
        
        $stmt = $pdo->prepare("
            SELECT ch.order_number 
            FROM users u
            JOIN employees e ON u.emp_id = e.emp_id
            JOIN company_hierarchies ch ON e.hierarchy_id = ch.id
            WHERE u.id = ? AND e.hierarchy_id IS NOT NULL
        ");
        $stmt->execute([$userId]);
        $userHierarchy = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($userHierarchy) {
            $stmt = $pdo->prepare("
                SELECT e.emp_id
                FROM employees e
                LEFT JOIN company_hierarchies ch ON e.hierarchy_id = ch.id
                WHERE e.deleted = 0 
                AND (e.hierarchy_id IS NULL OR ch.order_number >= ?)
            ");
            $stmt->execute([$userHierarchy['order_number']]);
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }
        
        $stmt = $pdo->prepare("
            SELECT emp_id 
            FROM employees 
            WHERE deleted = 0 
        ");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Get accessible employee IDs using Laravel DB (original method)
     */
    protected static function getAccessibleEmployeeIdsLaravel($userId)
    {
        $query = DB::table('employees')
            ->where('deleted', 0);
        
        $query = static::applyEmployeeFilter($query, $userId);
        
        return $query->pluck('emp_id')->toArray();
    }
}