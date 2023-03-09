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

### Create an OpenAI account and link your API key.

1. Sign up at [OpenAI](https://openai.com/) to create a free account (you'll get $8 credits)
2. Click on the "User" / "API Keys" menu item and create a new API key
3. Configure the `OPENAI_API_KEY` environment variable in your `.env` file

## Sponsors

### [What The Diff](https://whatthediff.ai/?ref=gh-writeout) - your AI powered code review assistant

[![What The Diff](https://whatthediff.ai/images/card.png)](https://whatthediff.ai/?ref=gh-writeout)
