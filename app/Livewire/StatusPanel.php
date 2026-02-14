<?php

namespace App\Livewire;

use App\Services\OpenClawService;
use Livewire\Component;

class StatusPanel extends Component
{
    public array $status = [];
    public bool $connected = false;

    public function mount(): void
    {
        $this->loadStatus();
    }

    public function loadStatus(): void
    {
        $api = app(OpenClawService::class);

        // Try to get real data
        $sessionStatus = $api->getSessionStatus();

        if ($sessionStatus && isset($sessionStatus['result'])) {
            $result = $sessionStatus['result'];
            $this->connected = true;
            $this->status = [
                'state' => 'online',
                'model' => $result['model'] ?? 'unknown',
                'session' => $result['session'] ?? 'main',
                'uptime' => $result['uptime'] ?? '-',
                'usage' => $result['usage'] ?? null,
                'cost' => $result['cost'] ?? null,
                'host' => gethostname(),
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
            ];
        } else {
            $this->connected = $api->isReachable();
            $this->status = [
                'state' => $this->connected ? 'online' : 'offline',
                'model' => 'claude-opus-4-6',
                'session' => 'main',
                'uptime' => '-',
                'usage' => null,
                'cost' => null,
                'host' => gethostname(),
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
            ];
        }
    }

    public function render()
    {
        return view('livewire.status-panel');
    }
}
