<?php

namespace App\Jobs;

use App\Events\IncidentBroadcast;
use App\Events\IncidentCreated;
use App\Models\Incident;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateIncidentJob implements ShouldQueue
{
    use Queueable;

    protected $newIncident;

    /**
     * Create a new job instance.
     */
    public function __construct(array $newIncident)
    {
        $this->newIncident = $newIncident;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $incident = Incident::create($this->newIncident);
            IncidentBroadcast::dispatch($incident, 'create');
        } catch (Exception $e) {
            throw $e;
        }
    }
}
