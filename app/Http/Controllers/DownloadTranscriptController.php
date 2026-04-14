<?php

namespace App\Http\Controllers;

use App\Models\Transcript;
use Illuminate\Http\Request;

class DownloadTranscriptController extends Controller
{
    public function __invoke(Transcript $transcript, Request $request)
    {
        $this->authorize('view', $transcript);

        $transcriptVtt = $transcript->translations[$request->get('language')] ?? $transcript->transcript;
        $plainTextTranscript = $this->plainTextFromVtt($transcriptVtt);

        return response($plainTextTranscript, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="transcript.txt"',
            'Content-Length' => strlen($plainTextTranscript),
        ]);
    }

    protected function plainTextFromVtt(string $transcriptVtt): string
    {
        $normalizedVtt = str_replace(["\r\n", "\r"], "\n", trim($transcriptVtt));
        $blocks = preg_split("/\n{2,}/", $normalizedVtt) ?: [];
        $plainTextBlocks = [];

        foreach ($blocks as $block) {
            $lines = array_values(array_filter(
                array_map('trim', explode("\n", trim($block))),
                fn (string $line) => $line !== '',
            ));

            if ($lines === [] || $lines[0] === 'WEBVTT') {
                continue;
            }

            if (isset($lines[1]) && str_contains($lines[1], '-->')) {
                array_shift($lines);
            }

            if (isset($lines[0]) && str_contains($lines[0], '-->')) {
                array_shift($lines);
            }

            if ($lines === []) {
                continue;
            }

            $plainTextBlocks[] = implode(' ', $lines);
        }

        return implode(PHP_EOL.PHP_EOL, $plainTextBlocks);
    }
}
