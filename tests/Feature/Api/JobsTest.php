<?php

namespace Tests\Feature\Api;

use App\Jobs\CreateIncidentJob;
use App\Jobs\DeleteIncidentJob;
use App\Jobs\UpdateIncidentJob;
use App\Models\Incident;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class JobsTest extends TestCase
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

    public function test_job_create(): void
    {
        $this->login();
        Queue::fake();

        Incident::factory(1)->create();

        $incident = Incident::first();

        $this->assertNotNull($incident);

        CreateIncidentJob::dispatch([$incident]);

        Queue::assertPushed(CreateIncidentJob::class);
    }

    public function test_job_put(): void
    {
        $this->login();
        Queue::fake();

        Incident::factory(1)->create();

        $incident = Incident::first();

        $this->assertNotNull($incident);

        UpdateIncidentJob::dispatch($incident, [$incident]);

        Queue::assertPushed(UpdateIncidentJob::class);
    }

    public function test_job_delete(): void
    {
        $this->login();
        Queue::fake();

        Incident::factory(1)->create();

        $incident = Incident::first();

        $this->assertNotNull($incident);

        DeleteIncidentJob::dispatch($incident->id);

        Queue::assertPushed(DeleteIncidentJob::class);
    }
}
