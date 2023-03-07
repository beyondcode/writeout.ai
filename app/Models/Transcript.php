<?php

namespace App\Models;

use App\Enum\TranscriptStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transcript extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'translations' => 'array',
        'public' => 'boolean',
    ];

    public function translatedLanguages(): array
    {
        return array_keys($this->translations ?? []);
    }

    public function isPending(): bool
    {
        return $this->status === TranscriptStatus::PENDING->value;
    }

    public function isTranscribing(): bool
    {
        return $this->status === TranscriptStatus::TRANSCRIBING->value;
    }

    public function isTranscribed(): bool
    {
        return $this->status === TranscriptStatus::COMPLETED->value;
    }

    public function isTranslated(): bool
    {
        return $this->status === TranscriptStatus::TRANSLATED->value;
    }

    public function isTranslating(): bool
    {
        return $this->status === TranscriptStatus::TRANSLATING->value;
    }

    public function failed(): bool
    {
        return $this->status === TranscriptStatus::FAILED->value;
    }
}
