<?php

namespace App\Livewire;

use App\Services\OpenClawService;
use Livewire\Component;

class SchedulePanel extends Component
{
    public array $jobs = [];
    public ?string $nextAction = null;
    public bool $connected = false;
    public array $executingJobs = [];
    public ?string $lastExecutedJob = null;

    public function mount(): void
    {
        $this->loadJobs();
    }

    public function loadJobs(): void
    {
        $api = app(OpenClawService::class);
        $result = $api->listCronJobs();

        if ($result && isset($result['jobs'])) {
            $this->connected = true;
            $this->jobs = collect($result['jobs'] ?? [])
                ->map(function ($job) {
                    $nextRunMs = $job['state']['nextRunAtMs'] ?? null;
                    $nextRunFormatted = '-';
                    if ($nextRunMs) {
                        try {
                            $nextRunFormatted = \Carbon\Carbon::createFromTimestampMs($nextRunMs)
                                ->timezone('Europe/Brussels')
                                ->format('M d, H:i');
                        } catch (\Exception $e) {
                            $nextRunFormatted = '-';
                        }
                    }
                    
                    $lastRunMs = $job['state']['lastRunAtMs'] ?? null;
                    $lastRunFormatted = '-';
                    if ($lastRunMs) {
                        try {
                            $lastRunFormatted = \Carbon\Carbon::createFromTimestampMs($lastRunMs)
                                ->timezone('Europe/Brussels')
                                ->format('M d, H:i');
                        } catch (\Exception $e) {
                            $lastRunFormatted = '-';
                        }
                    }
                    
                    return [
                        'id' => $job['id'] ?? '',
                        'name' => $job['name'] ?? 'Unnamed',
                        'schedule' => $this->formatSchedule($job['schedule'] ?? []),
                        'next' => $nextRunFormatted,
                        'last' => $lastRunFormatted,
                        'status' => ($job['enabled'] ?? true) ? 'active' : 'disabled',
                        'lastResult' => $job['state']['lastResult'] ?? 'pending',
                    ];
                })
                ->toArray();

            // Find soonest next run
            $this->nextAction = collect($this->jobs)
                ->where('status', 'active')
                ->sortBy('next')
                ->first()['next'] ?? null;
        } else {
            $this->connected = false;
            $this->jobs = [];
            $this->nextAction = null;
        }
    }

    public function executeJob(string $jobId): void
    {
        $this->executingJobs[$jobId] = true;

        $api = app(OpenClawService::class);
        $result = $api->executeCronJob($jobId);

        $this->lastExecutedJob = $jobId;
        unset($this->executingJobs[$jobId]);

        // Reload jobs to get updated status
        $this->loadJobs();
    }

    protected function formatSchedule(array $schedule): string
    {
        return match ($schedule['kind'] ?? '') {
            'cron' => $schedule['expr'] ?? 'cron',
            'every' => 'Every ' . round(($schedule['everyMs'] ?? 0) / 60000) . ' min',
            'at' => 'Once at ' . ($schedule['at'] ?? '?'),
            default => json_encode($schedule),
        };
    }

    public function render()
    {
        return view('livewire.schedule-panel');
    }
}
