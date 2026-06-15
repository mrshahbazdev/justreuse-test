<?php

namespace Laravel\Fortify\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        if ( (auth()->user()->hasRole('SuperAdmin') == true) || (auth()->user()->hasRole('Admin') == true) )
        $force_redirect = 'admin_login';
    else 
        $force_redirect = 'login';

        return $request->wantsJson()
                    ? response()->json(['two_factor' => false])
                    : redirect()->intended(Fortify::redirects($force_redirect));
    }
}
