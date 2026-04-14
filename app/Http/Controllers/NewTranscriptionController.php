<?php

namespace App\Http\Controllers;

use App\SendStack;
use Illuminate\Contracts\Auth\Authenticatable;

class NewTranscriptionController extends Controller
{
    public function __invoke(SendStack $sendStack)
    {
        /** @var Authenticatable|null $user */
        $user = auth()->user();

        return view('transcribe', [
            'isSubscribed' => $user?->email ? $sendStack->isActiveSubscriber($user->email) : false,
            'guestUploadsEnabled' => config('writeout.allow_guest_uploads'),
            'mockAiEnabled' => config('writeout.mock_ai'),
            'transcriptionDriver' => config('writeout.transcription_driver'),
            'translationDriver' => config('writeout.translation_driver'),
        ]);
    }
}
