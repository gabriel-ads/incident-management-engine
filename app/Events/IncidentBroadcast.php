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
    private $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($incident, $eventType, $userId = null)
    {
        $this->incident = $incident;
        $this->eventType = $eventType;
        $this->userId = $userId;
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
        // return match ($this->eventType) {
        //     'create' => ['incident' => $this->incident, 'eventType' => $this->eventType],
        //     'update' => ['incident' => $this->incident, 'eventType' => $this->eventType],
        //     'delete' => ['incidentId' => $this->incident, 'eventType' => $this->eventType]
        // };
        return ['incident' => $this->incident, 'eventType' => $this->eventType];
    }
}
