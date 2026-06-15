<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\User;


class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Laravel\Fortify\Http\Requests\VerifyEmailRequest  $request
     * @return \Illuminate\Http\Response
     */
    // public function __invoke(VerifyEmailRequest $request)
    // {
    //     echo "testing";exit();
    //     if ($request->user()->hasVerifiedEmail()) {
    //         return $request->wantsJson()
    //             ? new JsonResponse('', 204)
    //             : redirect()->intended(Fortify::redirects('email-verification').'?verified=1');
    //     }

    //     if ($request->user()->markEmailAsVerified()) {
    //         event(new Verified($request->user()));
    //     }

    //     return $request->wantsJson()
    //         ? new JsonResponse('', 202)
    //         : redirect()->intended(Fortify::redirects('email-verification').'?verified=1');
    // }

    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id')); //takes user ID from verification link. Even if somebody would hijack the URL, signature will be fail the request
        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(config('fortify.home') . '?verified=1');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        
        $message = __('Your email has been verified.');

        return redirect('login')->with('status', $message); //if user is already logged in it will redirect to the dashboard page
    }
}
