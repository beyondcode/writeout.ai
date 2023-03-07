@extends('layouts.app')
@section('content')
    <div class="max-w-7xl mx-auto my-24 h-full">
        <div class="mx-auto max-w-2xl md:text-center">
            <h2
                class="font-display text-3xl tracking-tight text-slate-900 sm:text-5xl">
                Uh Oh!
            </h2>
            <p class="mt-4 text-lg tracking-tight text-slate-700">
                Looks like this transcript doesn't exist.
            </p>
            <div class="mt-12">
                <a class="group inline-flex items-center justify-center rounded-full py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-blue-600 text-white hover:text-slate-100 hover:bg-blue-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600"
                   href="/transcribe">
                    <span>Transcribe your own audio</span>
                </a>
            </div>
        </div>
    </div>
@endsection
