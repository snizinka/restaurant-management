<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function create() {
        return view('auth.login');
    }

    public function store() {



    }
}
