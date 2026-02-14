<?php

namespace App\Livewire;

use App\Services\OpenClawService;
use Livewire\Component;

class QuickActionsPanel extends Component
{
    public array $jobs = [];
    public array $lastAction = [];
    public bool $showSessionHistory = false;
    public array $sessionHistory = [];
    public ?string $selectedJobId = null;
    public ?string $actionStatus = null;
    public ?string $actionError = null;
    public string $sessionKey = 'main';
    public string $authToken = '';

    public function mount(): void
    {
        $this->loadJobs();
        $this->sessionKey = config('services.openclaw.session_key', 'main');
        // Get last 2 chars of token for display
        $token = config('services.openclaw.token', '');
        $this->authToken = $token ? '...' . substr($token, -8) : 'none';
    }

    public function loadJobs(): void
    {
        $api = app(OpenClawService::class);
        $result = $api->listCronJobs();

        if ($result && isset($result['jobs'])) {
            $this->jobs = collect($result['jobs'] ?? [])
                ->map(function ($job) {
                    return [
                        'id' => $job['id'] ?? '',
                        'name' => $job['name'] ?? 'Unnamed',
                        'enabled' => $job['enabled'] ?? true,
                    ];
                })
                ->toArray();
        } else {
            $this->jobs = [];
        }
    }

    public function executeCronJob(?string $jobId = null): void
    {
        $jobId = $jobId ?? $this->selectedJobId;
        if (!$jobId) {
            $this->actionError = 'No job selected';
            return;
        }

        $this->actionStatus = 'executing';
        $this->actionError = null;

        $api = app(OpenClawService::class);
        $result = $api->executeCronJob($jobId);

        if ($result && ($result['result']['success'] ?? false)) {
            $this->lastAction = [
                'type' => 'job_executed',
                'jobId' => $jobId,
                'jobName' => collect($this->jobs)->firstWhere('id', $jobId)['name'] ?? $jobId,
                'timestamp' => now()->format('H:i:s'),
                'status' => 'success',
            ];
            $this->actionStatus = 'success';
            // Auto-clear success after 3 seconds
            $this->dispatch('clearActionStatus');
        } else {
            $this->actionError = $result['result']['error'] ?? 'Failed to execute job';
            $this->actionStatus = null;
        }

        $this->selectedJobId = null;
    }

    public function restartGateway(): void
    {
        $this->actionStatus = 'restarting';
        $this->actionError = null;

        $api = app(OpenClawService::class);
        $result = $api->restartGateway();

        if ($result && ($result['result']['success'] ?? false)) {
            $this->lastAction = [
                'type' => 'gateway_restarted',
                'timestamp' => now()->format('H:i:s'),
                'status' => 'success',
            ];
            $this->actionStatus = 'success';
            $this->dispatch('clearActionStatus');
        } else {
            $this->actionError = $result['result']['error'] ?? 'Failed to restart gateway';
            $this->actionStatus = null;
        }
    }

    public function viewSessionHistory(): void
    {
        $api = app(OpenClawService::class);
        $result = $api->getSessionHistory($this->sessionKey, limit: 50);

        if ($result) {
            $this->sessionHistory = $result['result']['history'] ?? [];
            $this->showSessionHistory = true;
        } else {
            $this->actionError = 'Failed to load session history';
        }
    }

    public function copyToClipboard(string $text, string $label): void
    {
        // JavaScript will handle the actual copy
        $this->dispatch('copyToClipboard', text: $text, label: $label);
    }

    public function closeSessionHistory(): void
    {
        $this->showSessionHistory = false;
    }

    public function render()
    {
        return view('livewire.quick-actions-panel');
    }
}
