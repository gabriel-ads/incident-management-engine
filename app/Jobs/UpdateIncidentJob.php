<?php

namespace App\Jobs;

use App\Events\IncidentBroadcast;
use App\Models\Incident;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateIncidentJob implements ShouldQueue
{
    use Queueable;

    protected $incident;
    protected $updateData;

    /**
     * Create a new job instance.
     */
    public function __construct(Incident $incident, array $updateData)
    {
        $this->incident = $incident;
        $this->updateData = $updateData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->incident->update($this->updateData);
        IncidentBroadcast::dispatch($this->incident, 'update');
    }
}
