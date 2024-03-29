<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class VerifyEmailController extends Controller
{
    public function verify(Request $request): RedirectResponse
    {
        $user = User::where('id', $request->id)->first();

        if ($user->hasVerifiedEmail()) {
            return redirect(config('app.front-side') . '/email/verify/already-success');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect(config('app.front-side') . '/email/verify/success');
    }

    public function reset(Request $request): RedirectResponse {
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        return redirect('http://127.0.0.1:8080'.'/resetpassword');
    }
}
