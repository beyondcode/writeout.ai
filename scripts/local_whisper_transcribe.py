import argparse
import json
import logging
import math
import sys


def format_timestamp(seconds: float) -> str:
    total_milliseconds = max(0, int(round(seconds * 1000)))
    hours, remainder = divmod(total_milliseconds, 3_600_000)
    minutes, remainder = divmod(remainder, 60_000)
    secs, milliseconds = divmod(remainder, 1000)

    return f"{hours:02}:{minutes:02}:{secs:02}.{milliseconds:03}"


def build_vtt(segments) -> str:
    lines = ["WEBVTT", ""]

    for index, segment in enumerate(segments, start=1):
        lines.append(str(index))
        lines.append(
            f"{format_timestamp(segment.start)} --> {format_timestamp(segment.end)}"
        )
        lines.append(segment.text.strip())
        lines.append("")

    return "\n".join(lines).strip() + "\n"


def main() -> int:
    parser = argparse.ArgumentParser()
    parser.add_argument("--input", required=True)
    parser.add_argument("--prompt", default="")
    parser.add_argument("--model", default="small")
    parser.add_argument("--device", default="cpu")
    parser.add_argument("--compute-type", default="int8")
    args = parser.parse_args()

    try:
        from faster_whisper import WhisperModel
    except ImportError as exc:
        raise RuntimeError(
            "Local Whisper is not installed. Run `python -m pip install faster-whisper` first."
        ) from exc

    logging.getLogger("faster_whisper").setLevel(logging.WARNING)

    model = WhisperModel(
        args.model,
        device=args.device,
        compute_type=args.compute_type,
    )

    segments_iter, info = model.transcribe(
        args.input,
        initial_prompt=args.prompt or None,
        vad_filter=True,
    )

    segments = list(segments_iter)
    transcript = build_vtt(segments)
    duration = info.duration or (segments[-1].end if segments else 0)

    payload = {
        "language": info.language or "unknown",
        "duration": int(math.ceil(duration)),
        "transcript": transcript,
    }

    json.dump(payload, sys.stdout)

    return 0


if __name__ == "__main__":
    try:
        raise SystemExit(main())
    except Exception as exc:  # pragma: no cover
        print(str(exc), file=sys.stderr)
        raise SystemExit(1)
