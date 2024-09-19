<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('incident.{user_id}', function (User $user, int $user_id) {
  return $user->id === $user_id;
});
