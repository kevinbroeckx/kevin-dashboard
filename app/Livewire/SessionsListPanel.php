<?php

namespace App\Livewire;

use App\Services\OpenClawService;
use Livewire\Component;

class SessionsListPanel extends Component
{
    public array $sessions = [];
    public bool $connected = false;

    public function mount(): void
    {
        $this->loadSessions();
    }

    public function loadSessions(): void
    {
        $api = app(OpenClawService::class);
        $result = $api->listSessions(15, 1);

        if ($result && isset($result['sessions'])) {
            $this->connected = true;
            $this->sessions = collect($result['sessions'] ?? [])
                ->map(function ($session) {
                    $lastMsg = '';
                    if (!empty($session['messages'])) {
                        $msg = end($session['messages']);
                        $lastMsg = is_array($msg['content']) && !empty($msg['content'])
                            ? $this->truncate($msg['content'][0]['text'] ?? '', 50)
                            : $this->truncate($msg['content'] ?? '', 50);
                    }

                    $updatedAt = isset($session['updatedAt'])
                        ? \Carbon\Carbon::createFromTimestampMs($session['updatedAt'])
                            ->timezone('Europe/Brussels')
                            ->format('H:i')
                        : '-';

                    return [
                        'key' => $session['key'] ?? '',
                        'displayName' => $session['displayName'] ?? $session['key'] ?? 'unknown',
                        'model' => $session['model'] ?? 'unknown',
                        'tokens' => $session['totalTokens'] ?? 0,
                        'lastMsg' => $lastMsg,
                        'updatedAt' => $updatedAt,
                    ];
                })
                ->values()
                ->toArray();
        } else {
            $this->connected = false;
            $this->sessions = [];
        }
    }

    protected function truncate(string $text, int $length): string
    {
        $text = strip_tags($text);
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }

    public function render()
    {
        return view('livewire.sessions-list-panel');
    }
}
