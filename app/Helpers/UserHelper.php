<?php

namespace App\Helpers;

use Session;

class UserHelper
{
    protected static $connection = null;
    
    protected static function getConnection()
    {
        if (self::$connection === null) {
            $config = config('database.connections.mysql');
            self::$connection = new \mysqli(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database']
            );
            
            if (self::$connection->connect_error) {
                throw new \Exception('Database connection failed: ' . self::$connection->connect_error);
            }
        }
        return self::$connection;
    }
    
    public static function applyEmployeeFilter($query, $userId = null)
    {
        $userId = $userId ?? Session::get('users_id');
        
        if (!$userId) {
            return $query;
        }
        
        $userPayGroups = \DB::table('user_has_pay_groups')
            ->where('user_id', $userId)
            ->pluck('group_id')
            ->toArray();
        
        if (!empty($userPayGroups)) {
            return static::filterByPayGroups($query, $userPayGroups);
        }
        
        $userEmployee = \DB::table('users')
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
        $userHierarchy = \DB::table('company_hierarchies')
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

    public static function getAccessibleEmployeeIds($userId = null, $mysqli = null)
    {
        if ($mysqli !== null) {
            return static::getAccessibleEmployeeIdsMySQLi($userId, $mysqli);
        }
        
        if ($userId === null) {
            $userId = Session::get('users_id');
        }
        
        if (!$userId) {
            return [];
        }
        
        $userPayGroups = \DB::table('user_has_pay_groups')
            ->where('user_id', $userId)
            ->pluck('group_id')
            ->toArray();
        
        if (!empty($userPayGroups)) {
            return static::getEmployeeIdsByPayGroups($userPayGroups);
        }
        
        $userEmployee = \DB::table('users')
            ->join('employees', 'users.emp_id', '=', 'employees.emp_id')
            ->where('users.id', $userId)
            ->whereNotNull('employees.hierarchy_id')
            ->first(['employees.hierarchy_id']);
        
        if ($userEmployee && $userEmployee->hierarchy_id) {
            return static::getEmployeeIdsByHierarchy($userEmployee->hierarchy_id);
        }
        
        return \DB::table('employees')
            ->where('deleted', 0)
            ->pluck('emp_id')
            ->toArray();
    }

    protected static function getEmployeeIdsByPayGroups($groupIds)
    {
        return \DB::table('payroll_profiles')
            ->join('employees', 'payroll_profiles.emp_id', '=', 'employees.id')
            ->whereIn('payroll_profiles.employee_payday_id', $groupIds)
            ->where('employees.deleted', 0)
            ->distinct()
            ->pluck('employees.emp_id')
            ->toArray();
    }

    protected static function getEmployeeIdsByHierarchy($hierarchyId)
    {
        $userHierarchy = \DB::table('company_hierarchies')
            ->where('id', $hierarchyId)
            ->first(['order_number']);
        
        if (!$userHierarchy) {
            return [];
        }
        
        return \DB::table('employees')
            ->leftJoin('company_hierarchies', 'employees.hierarchy_id', '=', 'company_hierarchies.id')
            ->where('employees.deleted', 0)
            ->where(function($q) use ($userHierarchy) {
                $q->whereNull('employees.hierarchy_id')
                    ->orWhere('company_hierarchies.order_number', '>=', $userHierarchy->order_number);
            })
            ->pluck('employees.emp_id')
            ->toArray();
    }

    public static function getLoggedInUserId($mysqli = null)
    {
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['users_id'])) {
            return $_SESSION['users_id'];
        }
        
        if ($mysqli !== null && isset($_COOKIE['laravel_session'])) {
            $sessionId = $_COOKIE['laravel_session'];
            
            try {
                $stmt = $mysqli->prepare("
                    SELECT payload 
                    FROM sessions 
                    WHERE id = ? 
                    LIMIT 1
                ");
                $stmt->bind_param("s", $sessionId);
                $stmt->execute();
                $result = $stmt->get_result();
                $session = $result->fetch_assoc();
                $stmt->close();
                
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

    protected static function getAccessibleEmployeeIdsMySQLi($userId, $mysqli)
    {
        try {
            $stmt = $mysqli->prepare("SELECT group_id FROM user_has_pay_groups WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $userPayGroups = [];
            while ($row = $result->fetch_assoc()) {
                $userPayGroups[] = intval($row['group_id']);
            }
            $stmt->close();
            
            if (!empty($userPayGroups)) {
                $placeholders = implode(',', array_fill(0, count($userPayGroups), '?'));
                $types = str_repeat('i', count($userPayGroups));
                
                $stmt = $mysqli->prepare("
                    SELECT DISTINCT e.emp_id
                    FROM payroll_profiles pp
                    INNER JOIN employees e ON pp.emp_id = e.id
                    WHERE pp.employee_payday_id IN ($placeholders)
                    AND e.deleted = 0
                ");
                $stmt->bind_param($types, ...$userPayGroups);
                $stmt->execute();
                $result = $stmt->get_result();
                $empIds = [];
                while ($row = $result->fetch_assoc()) {
                    $empIds[] = intval($row['emp_id']);
                }
                $stmt->close();
                return $empIds;
            }
            
            $stmt = $mysqli->prepare("
                SELECT e.hierarchy_id 
                FROM users u
                INNER JOIN employees e ON u.emp_id = e.emp_id
                WHERE u.id = ? AND e.hierarchy_id IS NOT NULL
                LIMIT 1
            ");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            $userEmployee = $result->fetch_assoc();
            $stmt->close();
            
            if ($userEmployee && !empty($userEmployee['hierarchy_id'])) {
                $hierarchyId = intval($userEmployee['hierarchy_id']);
                
                $stmt = $mysqli->prepare("
                    SELECT order_number
                    FROM company_hierarchies
                    WHERE id = ?
                    LIMIT 1
                ");
                $stmt->bind_param("i", $hierarchyId);
                $stmt->execute();
                $result = $stmt->get_result();
                $userHierarchy = $result->fetch_assoc();
                $stmt->close();
                
                if ($userHierarchy && isset($userHierarchy['order_number'])) {
                    $orderNumber = intval($userHierarchy['order_number']);
                    
                    $stmt = $mysqli->prepare("
                        SELECT e.emp_id
                        FROM employees e
                        LEFT JOIN company_hierarchies ch ON e.hierarchy_id = ch.id
                        WHERE e.deleted = 0 
                        AND (e.hierarchy_id IS NULL OR ch.order_number >= ?)
                    ");
                    $stmt->bind_param("i", $orderNumber);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $empIds = [];
                    while ($row = $result->fetch_assoc()) {
                        $empIds[] = intval($row['emp_id']);
                    }
                    $stmt->close();
                    return $empIds;
                }
            }
            
            $result = $mysqli->query("SELECT emp_id FROM employees WHERE deleted = 0");
            $empIds = [];
            while ($row = $result->fetch_assoc()) {
                $empIds[] = intval($row['emp_id']);
            }
            return $empIds;
            
        } catch (\Exception $e) {
            error_log("UserHelper MySQLi Error: " . $e->getMessage());
            return [];
        }
    }
}