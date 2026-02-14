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

        if ($sessionStatus) {
            $this->connected = true;
            // Parse the statusText to extract key info
            $statusText = $sessionStatus['statusText'] ?? '';
            
            // Extract model from status text (e.g., "?? Model: anthropic/claude-haiku-4-5")
            $model = 'unknown';
            if (preg_match('/Model:\s*([^\s]+)/', $statusText, $m)) {
                $model = $m[1];
            }
            
            // Extract token usage (e.g., "?? Tokens: 22 in / 596 out")
            $tokensInfo = '-';
            if (preg_match('/Tokens:\s*([^#]+)/i', $statusText, $m)) {
                $tokensInfo = trim($m[1]);
            }
            
            $this->status = [
                'state' => 'online',
                'model' => $model,
                'session' => $sessionStatus['sessionKey'] ?? 'main',
                'uptime' => '-',
                'usage' => $tokensInfo,
                'cost' => null,
                'host' => gethostname(),
                'php' => PHP_VERSION,
                'laravel' => app()->version(),
            ];
        } else {
            $this->connected = $api->isReachable();
            $this->status = [
                'state' => $this->connected ? 'online' : 'offline',
                'model' => 'unknown',
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
