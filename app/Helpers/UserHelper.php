<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Session;

class UserHelper
{
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
    
    protected static function filterByPayGroups($query, $groupIds)
    {
        return $query->whereIn('employees.id', function($subQuery) use ($groupIds) {
            $subQuery->select('payroll_profiles.emp_id')
                ->from('payroll_profiles')
                ->whereIn('payroll_profiles.employee_payday_id', $groupIds);
        });
    }
    
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

    public static function getAccessibleEmployeeIds($userId = null, $pdo = null)
    {
        if ($userId === null && $pdo === null) {
            $userId = Session::get('users_id');
        }
        
        if ($userId === null && $pdo !== null) {
            $userId = static::getLoggedInUserId($pdo);
        }
        
        if (!$userId) {
            return [];
        }
        
        if ($pdo !== null) {
            return static::getAccessibleEmployeeIdsPDO($userId, $pdo);
        }
        
        $userPayGroups = DB::table('user_has_pay_groups')
            ->where('user_id', $userId)
            ->pluck('group_id')
            ->toArray();
        
        if (!empty($userPayGroups)) {
            return static::getEmployeeIdsByPayGroups($userPayGroups);
        }
        
        $userEmployee = DB::table('users')
            ->join('employees', 'users.emp_id', '=', 'employees.emp_id')
            ->where('users.id', $userId)
            ->whereNotNull('employees.hierarchy_id')
            ->first(['employees.hierarchy_id']);
        
        if ($userEmployee && $userEmployee->hierarchy_id) {
            return static::getEmployeeIdsByHierarchy($userEmployee->hierarchy_id);
        }
        
        return DB::table('employees')
            ->where('deleted', 0)
            ->pluck('emp_id')
            ->toArray();
    }

    protected static function getEmployeeIdsByPayGroups($groupIds)
    {
        return DB::table('payroll_profiles')
            ->join('employees', 'payroll_profiles.emp_id', '=', 'employees.id')
            ->whereIn('payroll_profiles.employee_payday_id', $groupIds)
            ->where('employees.deleted', 0)
            ->distinct()
            ->pluck('employees.emp_id')
            ->toArray();
    }

    protected static function getEmployeeIdsByHierarchy($hierarchyId)
    {
        $userHierarchy = DB::table('company_hierarchies')
            ->where('id', $hierarchyId)
            ->first(['order_number']);
        
        if (!$userHierarchy) {
            return [];
        }
        
        return DB::table('employees')
            ->leftJoin('company_hierarchies', 'employees.hierarchy_id', '=', 'company_hierarchies.id')
            ->where('employees.deleted', 0)
            ->where(function($q) use ($userHierarchy) {
                $q->whereNull('employees.hierarchy_id')
                    ->orWhere('company_hierarchies.order_number', '>=', $userHierarchy->order_number);
            })
            ->pluck('employees.emp_id')
            ->toArray();
    }

    public static function getLoggedInUserId($pdo = null)
    {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['users_id'])) {
            return $_SESSION['users_id'];
        }
        
        if ($pdo !== null && isset($_COOKIE['laravel_session'])) {
            $sessionId = $_COOKIE['laravel_session'];
            
            try {
                $stmt = $pdo->prepare("
                    SELECT payload 
                    FROM sessions 
                    WHERE id = ? 
                    LIMIT 1
                ");
                $stmt->execute([$sessionId]);
                $session = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($session && !empty($session['payload'])) {
                    $payload = base64_decode($session['payload']);
                    $data = unserialize($payload);
                    return $data['users_id'] ?? null;
                }
            } catch (\Exception $e) {
                return null;
            }
        }
        
        return null;
    }

    protected static function getAccessibleEmployeeIdsPDO($userId, $pdo)
    {
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
                AND e.deleted = 0
            ");
            $stmt->execute($userPayGroups);
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }
        
        $stmt = $pdo->prepare("
            SELECT e.hierarchy_id 
            FROM users u
            JOIN employees e ON u.emp_id = e.emp_id
            WHERE u.id = ? AND e.hierarchy_id IS NOT NULL
        ");
        $stmt->execute([$userId]);
        $userEmployee = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($userEmployee && $userEmployee['hierarchy_id']) {
            $stmt = $pdo->prepare("
                SELECT ch.order_number
                FROM company_hierarchies ch
                WHERE ch.id = ?
            ");
            $stmt->execute([$userEmployee['hierarchy_id']]);
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
        }
        
        $stmt = $pdo->prepare("SELECT emp_id FROM employees WHERE deleted = 0");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}