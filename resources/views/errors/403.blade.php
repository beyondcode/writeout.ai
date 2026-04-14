@extends('layouts.app')
@section('content')
    @php
        $canTranscribeWithoutLogin = config('writeout.allow_guest_uploads');
        $ctaUrl = auth()->check() || $canTranscribeWithoutLogin ? url('/transcribe') : route('login');
        $ctaLabel = auth()->check() || $canTranscribeWithoutLogin
            ? 'Transcribe your own audio'
            : 'Log in to transcribe your own audio';
    @endphp

    <div class="max-w-7xl mx-auto my-24 h-screen">
        <div class="mx-auto max-w-2xl md:text-center">
            <h2
                class="font-display text-3xl tracking-tight text-slate-900 sm:text-5xl">
                Uh Oh!
            </h2>
            <p class="mt-4 text-lg tracking-tight text-slate-700">
                This transcript is private and you do not have permission to view it.
            </p>
            <div class="mt-12">
                <a class="group inline-flex items-center justify-center rounded-full py-2 px-4 text-sm font-semibold focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 bg-blue-600 text-white hover:text-slate-100 hover:bg-blue-500 active:bg-blue-800 active:text-blue-100 focus-visible:outline-blue-600"
                   href="{{ $ctaUrl }}">
                    <span>{{ $ctaLabel }}</span>
                </a>
            </div>
        </div>
    </div>
@endsection
