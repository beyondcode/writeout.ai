<?php

namespace App\Support;

use App\Models\Transcript;
use Illuminate\Support\Str;

class MockTranscriptFactory
{
    public static function makeVtt(Transcript $transcript): string
    {
        $prompt = Str::of((string) $transcript->prompt)->squish()->trim();

        $promptLine = $prompt->isNotEmpty()
            ? $prompt->limit(110)->value()
            : 'This is a locally generated demo transcript so you can keep building without OpenAI billing.';

        return implode(PHP_EOL, [
            'WEBVTT',
            '',
            '1',
            '00:00:00.000 --> 00:00:04.000',
            'Mock transcript generated in local mode.',
            '',
            '2',
            '00:00:04.000 --> 00:00:09.000',
            $promptLine,
            '',
            '3',
            '00:00:09.000 --> 00:00:14.000',
            'Set WRITEOUT_MOCK_AI=false when you are ready to use the real OpenAI API.',
        ]);
    }

    public static function translateVtt(string $vtt, string $language): string
    {
        $lines = preg_split('/\r\n|\r|\n/', $vtt) ?: [];

        $translated = array_map(
            fn (string $line) => self::shouldTranslateLine($line)
                ? '['.$language.' demo] '.$line
                : $line,
            $lines,
        );

        return implode(PHP_EOL, $translated);
    }

    protected static function shouldTranslateLine(string $line): bool
    {
        $trimmed = trim($line);

        if ($trimmed === '' || $trimmed === 'WEBVTT') {
            return false;
        }

        if (is_numeric($trimmed)) {
            return false;
        }

        return ! str_contains($trimmed, '-->');
    }
}
