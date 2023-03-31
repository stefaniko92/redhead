<?php

namespace App\Http\Middleware;

use Closure;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Approver
{
    use ApiResponseHelpers;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(!$request->user()->hasApproverProfile) {
            return $this->respondForbidden();
        }
        return $next($request);
    }
}
