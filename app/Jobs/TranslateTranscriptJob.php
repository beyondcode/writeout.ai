<?php

namespace App\Jobs;

use App\Enum\TranscriptStatus;
use App\Models\Transcript;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

class TranslateTranscriptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const TOKENS_PER_CHUNK = 1500;

    public $timeout = 3600;

    public function __construct(private Transcript $transcript, private string $language)
    {
    }

    public function handle()
    {
        $chunks = $this->getTranscriptChunks();

        try {
            $translation = '';

            foreach ($chunks as $chunk) {
                $result = $this->translateChunk($chunk);

                $translation .= $result->choices[0]->message->content;
            }

            $translations = $this->transcript->translations ?? [];
            $translations[$this->language] = $translation;

            $this->transcript->update([
                'status' => TranscriptStatus::TRANSLATED->value,
                'translations' => $translations,
            ]);
        } catch (\Exception $e) {
            $this->transcript->update([
                'status' => TranscriptStatus::COMPLETED->value,
            ]);
        }
    }

    protected function getTranscriptChunks(): array
    {
        $transcript = $this->transcript->transcript;

        $currentChunk = '';
        $chunks = [];

        collect(explode(PHP_EOL.PHP_EOL, $transcript))
            ->map(function ($section) use (&$tokens, &$currentChunk, &$chunks) {
                $chunkSize = strlen($currentChunk) + strlen($section) / 3.5;

                if ($chunkSize > self::TOKENS_PER_CHUNK) {
                    $chunks[] = $currentChunk;
                    $currentChunk = '';
                }

                $currentChunk .= $section.PHP_EOL.PHP_EOL;
            });

        $chunks[] = $currentChunk;

        return $chunks;
    }

    public function translateChunk(string $chunk): CreateResponse
    {
        return OpenAI::chat()
            ->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'Translate the VTT file to ' . $this->language],
                    ['role' => 'user', 'content' => $chunk],
                ],
            ]);
    }
}
