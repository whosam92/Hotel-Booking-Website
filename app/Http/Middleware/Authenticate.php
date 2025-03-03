<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            if (Auth::guard('admin')->check()) {
                return route('admin_login'); // Ensure admins go to the correct login page
            } elseif (Auth::guard('customer')->check()) {
                return route('customer_login'); // Customers go to their login
            }
            return route('login'); // Default login route
        }
    }
    
}