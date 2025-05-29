<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait WithAuthentication
{
    /**
     * Get the authenticated user from available guards
     * 
     * @param array $guards Guards to check
     * @return mixed User object or null if not authenticated
     */
    protected function getAuthenticatedUser(array $guards = ['approver', 'checker'])
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return Auth::guard($guard)->user();
            }
        }
        return null;
    }

    /**
     * Get the current active guard
     * 
     * @param array $guards Guards to check
     * @return string|null The name of the active guard or null if none found
     */
    protected function getCurrentGuard(array $guards = ['approver', 'checker'])
    {
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $guard;
            }
        }
        return null;
    }

    /**
     * Ensure user is authenticated and return user or redirect
     * 
     * @param array $guards Guards to check
     * @return mixed User object or redirect response
     */
    protected function ensureAuthenticatedUser(array $guards = ['approver', 'checker'])
    {
        $user = $this->getAuthenticatedUser($guards);
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Sesi login Anda telah berakhir. Silakan login kembali.');
        }

        return $user;
    }

    /**
     * Check if user is authenticated with specific guard
     * 
     * @param string $guard Guard to check
     * @return bool
     */
    protected function isAuthenticatedAs(string $guard)
    {
        return Auth::guard($guard)->check();
    }
} 