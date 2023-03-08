@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto my-24 w-full p-4">
        @if ($transcript->isPending() || $transcript->isTranscribing())
            <div class="mx-auto max-w-2xl md:text-center w-full">
                <h2
                    class="font-display text-3xl tracking-tight text-slate-900 sm:text-5xl">
                    Transcription in progress...
                </h2>
                <p class="mt-12 text-lg tracking-tight text-slate-700">
                    We're transcribing your audio. This may take some time.<br />
                    The page will refresh automatically when the transcription is complete.
                </p>
            </div>
            @include('partials.banner')
        @elseif ($transcript->failed())
            <div class="mx-auto max-w-2xl md:text-center">
                <h2
                    class="font-display text-3xl tracking-tight text-slate-900 sm:text-5xl">
                    Transcription failed.
                </h2>
                <p class="mt-4 text-lg tracking-tight text-slate-700">
                    We were unable to transcribe your audio. Please try again.
                </p>
            </div>
            @include('partials.banner')
        @elseif($transcript->isTranscribed() || $transcript->isTranslated() || $transcript->isTranslating())
            <div class="mx-auto max-w-2xl md:text-center mb-12">
                <h2
                    class="font-display text-3xl tracking-tight text-slate-900 sm:text-5xl">
                    @if ($transcript->isTranslating())
                        Your transcript is translating...
                    @else
                        Your transcript is ready!
                    @endif
                </h2>

                @if ($transcript->isTranslating())
                    <p class="mt-4 text-lg tracking-tight text-slate-700">
                        We're translating your transcript. This may take some time.<br />
                        The page will refresh automatically when the translation is complete.
                    </p>
                @else
                    <div class="mt-12 text-lg tracking-tight text-slate-700">
                        <form method="post" action="{{ action(\App\Http\Controllers\TranslateTranscriptController::class, $transcript) }}" class="flex items-center space-x-4 justify-center">
                            @csrf
                            <label for="language" class="block text-sm font-medium leading-6 text-gray-900">Translate to: </label>
                            <select id="language" name="language" class="block rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected value="">Original Language</option>
                                @foreach(config('writeout.translatable_languages') as $language)
                                    <option value="{{ $language }}" @if(request()->get('language') === $language) selected @endif>
                                        {{ $language }} @if($transcript->translations[$language] ?? false) (translated) @endif
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="group inline-flex items-center justify-center rounded-md py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-blue-600 text-white hover:text-slate-100 hover:bg-blue-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600"
                               href="/transcribe">
                                <span>Change</span>
                            </button>
                        </form>
                    </div>
                @endif
                <div class="mt-12 text-lg tracking-tight text-slate-700">
                    <a class="group inline-flex items-center justify-center rounded-full py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-gray-600 text-white hover:text-slate-100 hover:bg-gray-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600"
                       href="{{ action(\App\Http\Controllers\DownloadTranscriptController::class, [
                    'transcript' => $transcript,
                    'language' => request()->get('language') ?? '',
                ]) }}">
                        <span>Download transcript</span>
                    </a>

                    <a class="group inline-flex items-center justify-center rounded-full py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-blue-600 text-white hover:text-slate-100 hover:bg-blue-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600"
                       href="/transcribe">
                        <span>Transcribe new audio</span>
                    </a>
                </div>
            </div>
            @include('partials.banner')
            <div id="webvtt-player"
                 data-audio="{{ Storage::disk('do')->url($transcript->hash) }}"
                 data-transcript="{{ action(\App\Http\Controllers\ShowTranscriptVttController::class, [
                    'transcript' => $transcript,
                    'language' => request()->get('language') ?? '',
                ]) }}"
             />
        @endif
    </div>

    <script src="https://umd-mith.github.io/webvtt-player/webvtt-player.js"></script>

    @if ($transcript->isPending() || $transcript->isTranscribing() || $transcript->isTranslating())
        <script>
            setTimeout(function () {
                window.location.reload();
            }, 5000);
        </script>
    @endif
@endsection
