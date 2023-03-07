<?php

namespace App\Http\Controllers;

use App\Jobs\TranscribeFileJob;
use App\Models\Transcript;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TranscribeAudioController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->authorize('create', Transcript::class);

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:'.(25 * 1024), // max 25MB
            ]
        ]);

        $filename = Str::random(40).'.'.$request->file('file')->getClientOriginalExtension();

        // Store the file in the public disk
        $path = $request->file('file')
            ->storePubliclyAs('transcribe', $filename, 'do');

        // Store the file locally temporarily for OpenAI
        $request->file('file')
            ->storeAs('transcribe', $filename, 'local');

        $transcript = Transcript::create([
            'user_id' => $request->user()->id,
            'hash' => $path,
            'prompt' => $request->input('prompt', ''),
        ]);

        $this->dispatch(new TranscribeFileJob($transcript));

        return redirect()->action(ShowTranscriptController::class, $transcript);
    }
}
