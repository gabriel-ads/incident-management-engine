<?php

namespace App\Jobs;

use App\Models\Incident;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobCreateIncident implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private $incidentId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Incident::find($this->incidentId);
    }
}
