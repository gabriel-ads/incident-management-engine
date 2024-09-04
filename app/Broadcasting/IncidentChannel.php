<?php

namespace App\Broadcasting;

use App\Models\Incident;
use App\Models\User;

class IncidentChannel
{
    /**
     * Create a new channel instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     */
    public function join(User $user, Incident $incident): array|bool
    {
        return false;
    }
}
