<?php

namespace App\Events;

use App\Models\Incident;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class IncidentBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $incident;
    private $eventType;

    /**
     * Create a new event instance.
     */
    public function __construct($incident, $eventType)
    {
        $this->incident = $incident;
        $this->eventType = $eventType;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('incident.' . ($this->incident->user_id));
    }


    /**
     * Get the data to broadcast for the event.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return ['incident' => $this->incident, 'eventType' => $this->eventType];
    }
}
