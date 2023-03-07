<?php

use App\Http\Controllers\DownloadTranscriptController;
use App\Http\Controllers\GitHubLoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\NewTranscriptionController;
use App\Http\Controllers\ShowTranscriptController;
use App\Http\Controllers\ShowTranscriptVttController;
use App\Http\Controllers\TranscribeAudioController;
use App\Http\Controllers\TranslateTranscriptController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::view('/', 'welcome');
Route::view('imprint', 'static.imprint');
Route::view('privacy-policy', 'static.privacy-policy');
Route::get('logout', LogoutController::class);
Route::get('login', [GitHubLoginController::class, 'redirect'])->name('login');
Route::get('auth/callback', [GitHubLoginController::class, 'callback']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('transcribe', NewTranscriptionController::class);
    Route::post('transcribe', TranscribeAudioController::class);
    Route::get('transcript/{transcript}.vtt', ShowTranscriptVttController::class);
    Route::get('transcript/{transcript}/download', DownloadTranscriptController::class);
    Route::get('transcript/{transcript}', ShowTranscriptController::class);
    Route::post('transcript/{transcript}', TranslateTranscriptController::class);
});
