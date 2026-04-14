<?php

namespace App\Http\Controllers;

use App\Jobs\TranscribeFileJob;
use App\Models\Transcript;
use App\Models\User;
use App\SendStack;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TranscribeAudioController extends Controller
{
    public function __invoke(Request $request, SendStack $sendStack)
    {
        $this->authorize('create', Transcript::class);

        $uploader = $this->resolveUploader($request);
        $isGuestUpload = $request->user() === null;
        $transcriptDisk = config('writeout.transcript_disk');

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:'.(25 * 1024), // max 25MB
            ],
        ]);

        if ($request->boolean('newsletter') && $uploader->email) {
            $sendStack->updateOrSubscribe($uploader->email, [config('app.name')]);
        }

        $filename = Str::random(40).'.'.$request->file('file')->getClientOriginalExtension();

        // Store the file in the public disk
        $path = $request->file('file')
            ->storePubliclyAs('transcribe', $filename, $transcriptDisk);

        // Store the file locally temporarily for OpenAI
        $request->file('file')
            ->storeAs('transcribe', $filename, 'local');

        $transcript = Transcript::create([
            'user_id' => $uploader->id,
            'hash' => $path,
            'prompt' => $request->input('prompt', ''),
            'public' => $isGuestUpload ? true : $request->boolean('public', true),
        ]);

        $this->dispatch(new TranscribeFileJob($transcript));

        return redirect()->action(ShowTranscriptController::class, $transcript);
    }

    protected function resolveUploader(Request $request): User
    {
        if ($request->user() instanceof User) {
            return $request->user();
        }

        abort_unless(config('writeout.allow_guest_uploads'), 403);

        return User::firstOrCreate(
            ['github_id' => 'local-uploader'],
            [
                'github_username' => 'local-uploader',
                'name' => 'Local Uploader',
                'email' => null,
            ],
        );
    }
}
