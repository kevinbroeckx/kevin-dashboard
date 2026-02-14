<?php

namespace App\Livewire;

use App\Services\OpenClawService;
use Livewire\Component;

class ActivityPanel extends Component
{
    public array $activities = [];
    public bool $connected = false;

    public function mount(): void
    {
        $this->loadActivities();
    }

    public function loadActivities(): void
    {
        $api = app(OpenClawService::class);
        $result = $api->getSessionHistory('agent:main:main', 20, true);

        if ($result && isset($result['result'])) {
            $this->connected = true;
            $this->activities = collect($result['result']['messages'] ?? [])
                ->reverse()
                ->take(15)
                ->map(function ($msg) {
                    $role = $msg['role'] ?? 'unknown';
                    $hasTools = !empty($msg['tool_calls'] ?? $msg['toolCalls'] ?? []);

                    if ($role === 'assistant' && $hasTools) {
                        $toolName = $msg['tool_calls'][0]['name'] ?? $msg['toolCalls'][0]['name'] ?? 'tool';
                        return [
                            'type' => 'tool',
                            'icon' => '🔧',
                            'title' => $toolName,
                            'detail' => $this->truncate($msg['content'] ?? '', 80),
                            'time' => $this->formatTime($msg['timestamp'] ?? null),
                        ];
                    }

                    if ($role === 'assistant') {
                        return [
                            'type' => 'message',
                            'icon' => '💬',
                            'title' => 'Kevin replied',
                            'detail' => $this->truncate($msg['content'] ?? '', 80),
                            'time' => $this->formatTime($msg['timestamp'] ?? null),
                        ];
                    }

                    if ($role === 'user') {
                        return [
                            'type' => 'system',
                            'icon' => '👤',
                            'title' => 'User message',
                            'detail' => $this->truncate($msg['content'] ?? '', 80),
                            'time' => $this->formatTime($msg['timestamp'] ?? null),
                        ];
                    }

                    return [
                        'type' => 'system',
                        'icon' => '⚙️',
                        'title' => $role,
                        'detail' => $this->truncate($msg['content'] ?? '', 80),
                        'time' => $this->formatTime($msg['timestamp'] ?? null),
                    ];
                })
                ->values()
                ->toArray();
        } else {
            $this->connected = false;
            $this->activities = [];
        }
    }

    protected function truncate(string $text, int $length): string
    {
        $text = strip_tags($text);
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }

    protected function formatTime(?string $timestamp): string
    {
        if (!$timestamp) return '-';
        try {
            return \Carbon\Carbon::parse($timestamp)->timezone('Europe/Brussels')->format('H:i');
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function render()
    {
        return view('livewire.activity-panel');
    }
}
