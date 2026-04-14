# [writeout.ai](https://writeout.ai)

Transcribe and translate audio files using OpenAI's Whisper API.

## Demo

![demo](./docs/writeout-demo.gif)

## How it works

Writeout uses the recently released [OpenAI Whisper](https://platform.openai.com/docs/guides/speech-to-text) API to transcribe audio files.
You can upload any audio file, and the application will send it through the OpenAI Whisper API using Laravel's queued jobs.
Translation makes use of the new [OpenAI Chat API](https://platform.openai.com/docs/guides/code) and chunks the generated VTT file into smaller parts to fit them into 
the prompt context limit.

## Running Locally

### Clone the repository

```bash
git clone https://github.com/beyondcode/writeout.ai
```

### Configure your environment

1. Copy `.env.example` to `.env`
2. Add your `OPENAI_API_KEY`
3. Keep `QUEUE_CONNECTION=sync` for the simplest local setup
4. Keep `TRANSCRIPT_STORAGE_DISK=public` unless you already have a DigitalOcean Spaces bucket configured
5. Keep `WRITEOUT_ALLOW_GUEST_UPLOADS=true` if you want to test uploads locally without GitHub OAuth
6. Set `WRITEOUT_TRANSCRIPTION_DRIVER=local_whisper` if you want real local transcription without OpenAI billing
7. Keep `WRITEOUT_TRANSLATION_DRIVER=mock` if you only need real transcription locally and do not want local translation work yet

### Prepare the app

```bash
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link
```

### Start the app

```bash
php artisan serve
```

When guest uploads are enabled, local uploads are stored on the `public` disk and made public automatically so you can reopen the transcript without signing in.
When `WRITEOUT_TRANSCRIPTION_DRIVER=local_whisper`, uploads are transcribed on your own machine instead of through OpenAI.
When `WRITEOUT_TRANSLATION_DRIVER=mock`, translations are still mocked for local development.

### Install the local Whisper dependency

```bash
python -m pip install faster-whisper
```

The first local transcription run will download the selected Whisper model automatically. `tiny` is the easiest CPU-friendly starting point; if you want more accuracy later, switch the model to `base` or `small`.
The app keeps the Hugging Face cache under Laravel's `storage/app/huggingface` folder by default, and `HF_HUB_DISABLE_SYMLINKS_WARNING=1` keeps the Windows symlink warning out of normal local runs. If you later need higher download limits for diarization or larger models, add your own `HF_TOKEN`.

### Create an OpenAI account and link your API key

1. Sign up at [OpenAI](https://openai.com/) to create a free account (you'll get $8 credits)
2. Click on the "User" / "API Keys" menu item and create a new API key
3. Configure the `OPENAI_API_KEY` environment variable in your `.env` file

## Sponsors

### [What The Diff](https://whatthediff.ai/?ref=gh-writeout) - your AI powered code review assistant

[![What The Diff](https://whatthediff.ai/images/card.png)](https://whatthediff.ai/?ref=gh-writeout)
