@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto p-4 my-24" xmlns="http://www.w3.org/1999/html">
        <div class="mx-auto max-w-2xl md:text-center">
            <h2
                class="font-display text-3xl tracking-tight text-slate-900 sm:text-5xl">
                Transcribe your audio.
            </h2>
            <p class="mt-4 text-lg tracking-tight text-slate-700">
                Upload your audio file and we'll transcribe it for you. It's that easy.
            </p>

            @if ($transcriptionDriver === 'local_whisper')
                <div class="mt-8 rounded-md bg-emerald-50 p-4 text-left">
                    <h3 class="text-sm font-medium text-emerald-900">
                        Local Whisper mode is enabled
                    </h3>
                    <p class="mt-2 text-sm text-emerald-800">
                        New uploads will be transcribed on this machine without using OpenAI billing.
                    </p>
                    @if ($translationDriver === 'mock')
                        <p class="mt-2 text-sm text-emerald-800">
                            Transcript translation is still running in demo mode for local development.
                        </p>
                    @endif
                </div>
            @elseif ($mockAiEnabled)
                <div class="mt-8 rounded-md bg-amber-50 p-4 text-left">
                    <h3 class="text-sm font-medium text-amber-900">
                        Mock AI mode is enabled
                    </h3>
                    <p class="mt-2 text-sm text-amber-800">
                        Uploads will complete with a demo transcript and demo translations, so you can keep building locally without OpenAI billing.
                    </p>
                </div>
            @endif

            @if ($errors->isNotEmpty())
                <div class="rounded-md bg-red-50 p-4 mt-12">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">There were {{ $errors->count() }} {{ \Illuminate\Support\Str::plural('error', $errors->count()) }} with your submission</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <ul role="list" class="text-left list-disc space-y-1 pl-5">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <form class="pt-8 flex flex-col space-y-4" action="{{ url('/transcribe') }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="text-left">
                    <label for="file" class="block text-sm font-medium leading-6 text-gray-900">Audio File</label>
                    <div class="mt-2">
                        <input
                            type="file"
                            id="file"
                            name="file"
                            accept=".mp3,.mp4,.mpeg,.mpga,.m4a,.wav,.webm,audio/*,video/mp4"
                        />
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Supported formats: mp3, mp4, mpeg, mpga, m4a, wav and webm. <br>
                        Max 25 MB.
                    </p>
                </div>

                <div class="text-left">
                    <label for="prompt" class="block text-sm font-medium leading-6 text-gray-900">Prompt (optional)</label>
                    <div class="mt-2">
                        <textarea
                            id="prompt"
                            name="prompt"
                            rows="3"
                            class="block w-full rounded-md border-0 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:py-1.5 sm:text-sm sm:leading-6"
                            placeholder="The transcript is about Laravel which makes development with PHP a breeze."
                        >{{ old('prompt') }}</textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        The prompt can be used to provide additional information about the audio. This can be very helpful
                        for correcting specific words or acronyms that the AI model often misrecognizes in the audio.
                    </p>
                </div>

                @if (! auth()->check() && $guestUploadsEnabled)
                    <input type="hidden" name="public" value="1">
                    <div class="rounded-md bg-blue-50 p-4 text-left">
                        <p class="text-sm text-blue-900">
                            Guest uploads are enabled in this environment. Your transcript will be public so you can reopen
                            it without logging in.
                        </p>
                    </div>
                @else
                    <div class="text-left">
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input
                                    id="public"
                                    name="public"
                                    type="checkbox"
                                    value="1"
                                    @checked(old('public', true))
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                >
                            </div>
                            <div class="ml-3">
                                <label for="public" class="text-sm font-medium leading-6 text-gray-900">Make this transcript public</label>
                                <p class="text-sm text-gray-500">Anyone with the transcript link will be able to view it.</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if (auth()->check() && auth()->user()->email && !$isSubscribed)
                    <div class="text-left">
                        <div class="relative flex items-start">
                            <div class="flex h-6 items-center">
                                <input id="newsletter" aria-describedby="comments-description" name="newsletter" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            </div>
                            <div class="ml-3">
                                <label for="newsletter" class="text-sm font-medium leading-6 text-gray-900">Sign up to our newsletter</label>
                                <p id="comments-description" class="text-sm text-gray-500">Stay informed about our developer tools, such as writeout.ai.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <button type="submit" class="group inline-flex items-center justify-center rounded-full py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-slate-900 text-white hover:bg-slate-700 hover:text-slate-100 active:bg-slate-800 active:text-slate-300 focus-visible:outline-slate-900">Transcribe</button>
            </form>
        </div>
    </div>
@endsection
