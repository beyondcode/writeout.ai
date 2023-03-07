<?php

namespace App\Http\Controllers;

use App\Models\Transcript;

class ShowTranscriptController extends Controller
{
    public function __invoke(Transcript $transcript)
    {
        $this->authorize('view', $transcript);

        return view('transcript', [
            'transcript' => $transcript,
        ]);
    }
}
