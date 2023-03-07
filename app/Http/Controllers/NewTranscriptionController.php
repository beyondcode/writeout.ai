<?php

namespace App\Http\Controllers;

class NewTranscriptionController extends Controller
{
    public function __invoke()
    {
        return view('transcribe');
    }
}
