<?php

namespace App\Http\Controllers;

use App\Models\Transcript;
use Illuminate\Http\Request;

class ShowTranscriptVttController extends Controller
{
    public function __invoke(Transcript $transcript, Request $request)
    {
        $this->authorize('view', $transcript);

        $transcriptVtt = $transcript->translations[$request->get('language')] ?? $transcript->transcript;

        return response($transcriptVtt, 200, [
            'Content-Type' => 'text/vtt',
        ]);
    }
}
