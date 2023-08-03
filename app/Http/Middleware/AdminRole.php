<?php

namespace App\Http\Middleware;

use App\Builder\ReturnApi;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $loggedUser = Auth::user();
        if (!$loggedUser->is_admin)

            return ReturnApi::Error('Você não tem permissão para realizar essa ação.', 401);

        return $next($request);
    }
}
