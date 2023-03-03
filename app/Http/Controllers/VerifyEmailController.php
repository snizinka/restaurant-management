<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    public function verify(Request $request): RedirectResponse
    {
        $user = User::where('id', $request->id)->first();

        if ($user->hasVerifiedEmail()) {
            return redirect('http://127.0.0.1:8080' . '/email/verify/already-success');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect('http://127.0.0.1:8080' . '/email/verify/success');
    }

    public function reset(Request $request): RedirectResponse {
        return redirect('http://127.0.0.1:8080'.'/resetpassword');
    }
}
