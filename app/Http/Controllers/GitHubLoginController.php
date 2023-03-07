<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GitHubLoginController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback()
    {
        $user = Socialite::driver('github')->user();

        $user = User::firstOrCreate([
            'github_id' => $user->getId(),
        ], [
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'github_username' => $user->getNickname(),
        ]);

        auth()->login($user);

        return redirect()->action(NewTranscriptionController::class);
    }
}
