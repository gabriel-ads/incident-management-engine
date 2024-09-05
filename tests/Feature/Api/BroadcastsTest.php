<?php

namespace Tests\Feature\Api;

use App\Events\IncidentBroadcast;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BroadcastsTest extends TestCase
{
    use RefreshDatabase;

    public function login(): string
    {
        $users =  User::factory(5)->create();

        $login = $this->post(
            '/api/auth/login',
            [
                'email' => $users[0]->email,
                'password' => "password"
            ]
        );

        $token = $login->json('access_token');

        return $token;
    }
    public function test_incident_created_event_is_broadcasted()
    {
        $this->login();
        Event::fake();

        $incident = Incident::factory(1)->create();

        event(new IncidentBroadcast($incident, 'create'));

        Event::assertDispatched(IncidentBroadcast::class, function ($event) use ($incident) {
            return $event->incident === $incident;
        });
    }
}
