<?php

namespace App\Events;

use App\Models\Transcript;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TranscriptionStatusChangedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(protected Transcript $transcript)
    {
    }

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('transcript-'.$this->transcript->id);
    }
}
