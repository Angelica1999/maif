<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request as RequestFacade;

class BlockSecureForNonAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowed_users = [2760, 2680];

        if (Auth::check() && !in_array(Auth::user()->userid, $allowed_users) && RequestFacade::secure()) {
            abort(403, 'Forbidden Access');
        }
        return $next($request);
    }
}
