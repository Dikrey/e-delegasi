<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as BaseResponse;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$roles
     * @return Response|RedirectResponse|BaseResponse
     */
    public function handle(Request $request, Closure $next, ...$roles): Response|RedirectResponse|BaseResponse
    {
        if (!in_array(auth()->user()->role, $roles)){
            abort(BaseResponse::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
