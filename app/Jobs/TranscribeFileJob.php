<?php

namespace App\Jobs;

use App\Enum\TranscriptStatus;
use App\Models\Transcript;
use App\Support\LocalWhisperTranscriber;
use App\Support\MockTranscriptFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

class TranscribeFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected Transcript $transcript)
    {
    }

    public function handle(LocalWhisperTranscriber $localWhisperTranscriber)
    {
        $transcriptDisk = config('writeout.transcript_disk');
        $driver = config('writeout.transcription_driver');

        $this->transcript->update([
            'status' => TranscriptStatus::TRANSCRIBING,
        ]);

        try {
            if ($driver === 'mock') {
                $transcriptionResults = [
                    'language' => 'English',
                    'duration' => 14,
                    'transcript' => MockTranscriptFactory::makeVtt($this->transcript),
                ];
            } elseif ($driver === 'local_whisper') {
                $transcriptionResults = $localWhisperTranscriber->transcribe($this->transcript);
            } else {
                $openAiResults = OpenAI::audio()->transcribe([
                    'model' => 'whisper-1',
                    'file' => fopen(storage_path('app/'.$this->transcript->hash), 'r'),
                    'prompt' => $this->transcript->prompt,
                    'temperature' => 0.2,
                    'response_format' => 'vtt',
                ]);

                $transcriptionResults = [
                    'language' => $openAiResults->language,
                    'duration' => $openAiResults->duration,
                    'transcript' => $openAiResults->text,
                ];
            }

            $this->transcript->update([
                'source_language' => $transcriptionResults['language'],
                'status' => TranscriptStatus::COMPLETED,
                'duration' => $transcriptionResults['duration'],
                'transcript' => $transcriptionResults['transcript'],
                'error' => null,
            ]);
        } catch (\Throwable $e) {
            report($e);

            Storage::disk($transcriptDisk)->delete($this->transcript->hash);

            $this->transcript->update([
                'status' => TranscriptStatus::FAILED,
                'error' => $e->getMessage(),
            ]);
        }

        Storage::disk('local')->delete($this->transcript->hash);
    }
}
