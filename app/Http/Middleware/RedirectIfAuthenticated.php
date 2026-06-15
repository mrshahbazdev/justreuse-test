<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                //return redirect(RouteServiceProvider::HOME);
                $role = Auth::user()->roles;
                foreach ($role as $row) {
                    $rolename = strtolower($row->name);
                    if ($rolename == 'admin') {
                        return redirect(RouteServiceProvider::ADMIN);
                    } elseif ($rolename == 'superAdmin') {
                        return redirect(RouteServiceProvider::ADMIN);
                    } elseif ($rolename == 'user') {
                        //return Redirect::intended('/');
                        return redirect(RouteServiceProvider::USER);
                    } else {
                        return redirect('/login');
                    }
                }
            }
        }

        return $next($request);
    }
}
