<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('transcribe {file}', function () {
    $file = realpath($this->argument('file'));

    $result = (new \App\Actions\TranscribeFile())->handle($file, 'This is an episode of the Laravel News podcast.');

    info(json_encode($result->toArray(), JSON_PRETTY_PRINT));

})->purpose('Display an inspiring quote');
