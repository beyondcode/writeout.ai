<?php

namespace App\Http\Controllers;

class LogoutController extends Controller
{
    public function __invoke()
    {
        auth()->logout();

        return redirect()->to('/');
    }
}
