<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Check the route prefix to determine which login route to use
        if ($request->is('admin') || $request->is('admin/*')) {
            return route('admin.login');
        }

        if ($request->is('organizer') || $request->is('organizer/*')) {
            return route('organizer.login');
        }

        // Default to main login (if you have one)
        return route('organizer.login'); // or create a main login route
    }
}
