<?php

namespace App\Http\Controllers;

use App\Enum\TranscriptStatus;
use App\Jobs\TranslateTranscriptJob;
use App\Models\Transcript;
use Illuminate\Http\Request;

class TranslateTranscriptController extends Controller
{
    public function __invoke(Transcript $transcript, Request $request)
    {
        $this->authorize('view', $transcript);

        if ($request->get('language') === null) {
            return redirect()->action(ShowTranscriptController::class, $transcript);
        }

        if ($transcript->translations[$request->get('language')] ?? null) {
            return redirect()->action(ShowTranscriptController::class, [
                'transcript' => $transcript,
                'language' => $request->get('language'),
            ]);
        }

        $transcript->update([
            'status' => TranscriptStatus::TRANSLATING->value,
        ]);

        $this->dispatch(new TranslateTranscriptJob($transcript, $request->get('language')));

        return redirect()->action(ShowTranscriptController::class, [
            'transcript' => $transcript,
            'language' => $request->get('language'),
        ]);
    }
}
