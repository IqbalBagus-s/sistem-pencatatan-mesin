<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class AuthHelper
{
    /**
     * Get current authenticated user from any guard
     * 
     * @return array ['user' => User|null, 'guard' => string|null]
     */
    public static function getCurrentUser()
    {
        foreach (['approver', 'checker', 'host'] as $guard) {
            if (Auth::guard($guard)->check()) {
                return [
                    'user' => Auth::guard($guard)->user(),
                    'guard' => $guard
                ];
            }
        }
        
        return ['user' => null, 'guard' => null];
    }
    
    /**
     * Get current authenticated user object
     * 
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function user()
    {
        $result = self::getCurrentUser();
        return $result['user'];
    }
    
    /**
     * Get current guard name
     * 
     * @return string|null
     */
    public static function guard()
    {
        $result = self::getCurrentUser();
        return $result['guard'];
    }
    
    /**
     * Check if current user is approver
     */
    public static function isApprover()
    {
        return Auth::guard('approver')->check();
    }
    
    /**
     * Check if current user is checker
     */
    public static function isChecker()
    {
        return Auth::guard('checker')->check();
    }
    
    /**
     * Check if current user is host
     */
    public static function isHost()
    {
        return Auth::guard('host')->check();
    }
    
    /**
     * Get user role name
     * 
     * @return string|null
     */
    public static function getRoleName()
    {
        if (self::isApprover()) return 'Approver';
        if (self::isChecker()) return 'Checker';
        if (self::isHost()) return 'Host';
        return null;
    }
}