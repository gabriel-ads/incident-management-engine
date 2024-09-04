<?php

namespace App\Jobs;

use App\Events\IncidentBroadcast;
use App\Models\Incident;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class DeleteIncidentJob implements ShouldQueue
{
    use Queueable;

    protected $incidentId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $incidentId)
    {
        $this->incidentId = $incidentId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $incident = Incident::find($this->incidentId);

            if ($incident) {
                $incident->delete();
                IncidentBroadcast::dispatch($incident, 'delete', $incident->user_id);
                Log::info('Incident deleted and broadcast dispatched', ['incident' => $incident]);
            } else {
                Log::warning('Incident not found for deletion', ['incident_id' => $this->incidentId]);
            }
        } catch (Exception $e) {
            Log::error('Failed to delete incident', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
