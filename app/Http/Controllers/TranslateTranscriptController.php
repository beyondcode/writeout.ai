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

        $language = trim((string) $request->get('language', ''));

        if ($language === '') {
            return redirect()->action(ShowTranscriptController::class, $transcript);
        }

        if ($transcript->translations[$language] ?? null) {
            return redirect()->action(ShowTranscriptController::class, [
                'transcript' => $transcript,
                'language' => $language,
            ]);
        }

        $transcript->update([
            'status' => TranscriptStatus::TRANSLATING->value,
        ]);

        $this->dispatch(new TranslateTranscriptJob($transcript, $language));

        return redirect()->action(ShowTranscriptController::class, [
            'transcript' => $transcript,
            'language' => $language,
        ]);
    }
}
