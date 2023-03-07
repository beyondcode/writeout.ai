<?php

namespace App\Enum;

enum TranscriptStatus: string
{
    case PENDING = 'pending';
    case TRANSCRIBING = 'transcribing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case TRANSLATING = 'translating';
    case TRANSLATED = 'translated';
}
