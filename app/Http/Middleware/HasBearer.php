<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class HasBearer
{
    use HttpResponses;
    public function handle(Request $request, Closure $next): Response
    {
        $user = PersonalAccessToken::findToken($request->bearerToken());

        if (!$user) {
            return $this->error('401', ['unauthorized' => ['You need to be authorized first']], 401);
        }

        return $next($request);
    }
}
