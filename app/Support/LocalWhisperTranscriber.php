<?php

namespace App\Support;

use App\Models\Transcript;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Symfony\Component\Process\Process;

class LocalWhisperTranscriber
{
    public function transcribe(Transcript $transcript): array
    {
        File::ensureDirectoryExists(config('writeout.huggingface.home'));
        File::ensureDirectoryExists(config('writeout.huggingface.hub_cache'));

        $command = [
            config('writeout.local_whisper.command'),
            base_path('scripts/local_whisper_transcribe.py'),
            '--input',
            storage_path('app/'.$transcript->hash),
            '--model',
            config('writeout.local_whisper.model'),
            '--device',
            config('writeout.local_whisper.device'),
            '--compute-type',
            config('writeout.local_whisper.compute_type'),
        ];

        if (filled($transcript->prompt)) {
            $command[] = '--prompt';
            $command[] = $transcript->prompt;
        }

        $process = new Process($command, base_path(), [
            'PYTHONIOENCODING' => 'utf-8',
            'HF_HOME' => config('writeout.huggingface.home'),
            'HF_HUB_CACHE' => config('writeout.huggingface.hub_cache'),
            'HF_HUB_DISABLE_SYMLINKS_WARNING' => config('writeout.huggingface.disable_symlinks_warning') ? '1' : '0',
            'HF_HUB_DISABLE_PROGRESS_BARS' => config('writeout.huggingface.disable_progress_bars') ? '1' : '0',
            'HF_TOKEN' => (string) config('writeout.huggingface.token'),
        ]);

        $process->setTimeout(3600);
        $process->run();

        if (! $process->isSuccessful()) {
            $error = trim($process->getErrorOutput());

            throw new RuntimeException(
                $error !== ''
                    ? $error
                    : 'Local Whisper transcription failed to start.',
            );
        }

        $payload = json_decode($process->getOutput(), true);

        if (! is_array($payload)) {
            throw new RuntimeException('Local Whisper returned an invalid response.');
        }

        if (! isset($payload['language'], $payload['duration'], $payload['transcript'])) {
            throw new RuntimeException('Local Whisper returned an incomplete response.');
        }

        return $payload;
    }
}
