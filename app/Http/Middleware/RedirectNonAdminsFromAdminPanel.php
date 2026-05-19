<?php

namespace App\Http\Middleware;

use App\Enums\UserRoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectNonAdminsFromAdminPanel
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->user()->role !== UserRoleEnum::ADMIN) {
            return redirect()->to(filament()->getPanel('taxCalculator')->getUrl());
        }

        return $next($request);
    }
}
