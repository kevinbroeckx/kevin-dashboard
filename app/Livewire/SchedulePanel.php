<?php

namespace App\Livewire;

use App\Services\OpenClawService;
use Livewire\Component;

class SchedulePanel extends Component
{
    public array $jobs = [];
    public ?string $nextAction = null;
    public bool $connected = false;

    public function mount(): void
    {
        $this->loadJobs();
    }

    public function loadJobs(): void
    {
        $api = app(OpenClawService::class);
        $result = $api->listCronJobs();

        if ($result && isset($result['result'])) {
            $this->connected = true;
            $this->jobs = collect($result['result']['jobs'] ?? [])
                ->map(function ($job) {
                    return [
                        'id' => $job['id'] ?? $job['jobId'] ?? '',
                        'name' => $job['name'] ?? 'Unnamed',
                        'schedule' => $this->formatSchedule($job['schedule'] ?? []),
                        'next' => isset($job['nextRunAt']) ? \Carbon\Carbon::parse($job['nextRunAt'])->format('M d, H:i') : '-',
                        'status' => ($job['enabled'] ?? true) ? 'active' : 'disabled',
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
