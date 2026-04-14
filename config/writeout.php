<?php

$isLocal = env('APP_ENV') === 'local';
$legacyMockAi = env('WRITEOUT_MOCK_AI');
$legacyMockEnabled = $legacyMockAi === null
    ? null
    : filter_var($legacyMockAi, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);

$transcriptionDriver = env(
    'WRITEOUT_TRANSCRIPTION_DRIVER',
    $legacyMockEnabled === null
        ? ($isLocal ? 'mock' : 'openai')
        : ($legacyMockEnabled ? 'mock' : ($isLocal ? 'local_whisper' : 'openai')),
);

$translationDriver = env(
    'WRITEOUT_TRANSLATION_DRIVER',
    $legacyMockEnabled === null
        ? ($isLocal ? 'mock' : 'openai')
        : ($legacyMockEnabled ? 'mock' : 'openai'),
);

return [
    'allow_guest_uploads' => env('WRITEOUT_ALLOW_GUEST_UPLOADS', $isLocal),

    'transcript_disk' => env('TRANSCRIPT_STORAGE_DISK', env('DO_BUCKET') ? 'do' : 'public'),

    'transcription_driver' => $transcriptionDriver,

    'translation_driver' => $translationDriver,

    'mock_ai' => $transcriptionDriver === 'mock' || $translationDriver === 'mock',

    'local_whisper' => [
        'command' => env('WRITEOUT_LOCAL_WHISPER_COMMAND', PHP_OS_FAMILY === 'Windows' ? 'py' : 'python3'),
        'model' => env('WRITEOUT_LOCAL_WHISPER_MODEL', 'tiny'),
        'device' => env('WRITEOUT_LOCAL_WHISPER_DEVICE', 'cpu'),
        'compute_type' => env('WRITEOUT_LOCAL_WHISPER_COMPUTE_TYPE', 'int8'),
    ],

    'huggingface' => [
        'home' => env('HF_HOME', storage_path('app/huggingface')),
        'hub_cache' => env('HF_HUB_CACHE', storage_path('app/huggingface/hub')),
        'token' => env('HF_TOKEN'),
        'disable_symlinks_warning' => env('HF_HUB_DISABLE_SYMLINKS_WARNING', true),
        'disable_progress_bars' => env('HF_HUB_DISABLE_PROGRESS_BARS', true),
    ],

    'translatable_languages' => [
        'English',
        'German',
        'Spanish',
        'French',
        'Italian',
        'Dutch',
        'Portuguese',
        'Russian',
        'Japanese',
        'Chinese',
        'Klingon',
    ],
];
