<?php

namespace App\Http\Controllers;

use App\Models\Transcript;

class ShowTranscriptController extends Controller
{
    public function __invoke(Transcript $transcript)
    {
        if (auth()->check()) {
            $this->authorize('view', $transcript);
        } else {
            abort_if(!$transcript->public, 403);
        }

        return view('transcript', [
            'transcript' => $transcript,
        ]);
    }
}
