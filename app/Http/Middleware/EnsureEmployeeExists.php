<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeExists
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && !$request->user()->employee) {
            abort(403, 'No employee profile found. Please contact HR.');
        }

        return $next($request);
    }
}
