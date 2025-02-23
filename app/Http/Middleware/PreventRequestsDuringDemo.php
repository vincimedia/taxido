<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventRequestsDuringDemo
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
            if(env('APP_DEMO'))
                return back()->with('error','Oops! This action disabled in demo mode.');
        }

        return $next($request);
    }
}
