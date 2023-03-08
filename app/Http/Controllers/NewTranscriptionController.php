<?php

namespace App\Http\Controllers;

use App\SendStack;

class NewTranscriptionController extends Controller
{
    public function __invoke(SendStack $sendStack)
    {

        return view('transcribe', [
            'isSubscribed' => $sendStack->isActiveSubscriber(auth()->user()->email),
        ]);
    }
}
