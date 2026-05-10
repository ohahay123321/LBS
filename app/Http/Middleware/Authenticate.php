<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as BaseAuthenticate;
use Illuminate\Http\Request;

class Authenticate extends BaseAuthenticate
{
    protected function redirectTo(Request $request): ?string
    {
        if (! $request->expectsJson()) {
            if ($request->is('admin/*') || $request->is('admin')) {
                return route('admin.login');
            }
            if ($request->is('student/*') || $request->is('student')) {
                return route('student.login');
            }

            return route('admin.login');
        }

        return null;
    }
}
