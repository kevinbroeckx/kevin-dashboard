<?php

namespace App\Livewire;

use App\Services\OpenClawService;
use Livewire\Component;

class MessengerPanel extends Component
{
    public string $message = '';
    public array $messages = [];
    public bool $sending = false;

    public function sendMessage(): void
    {
        $text = trim($this->message);
        if (empty($text)) return;

        $this->messages[] = [
            'from' => 'you',
            'text' => $text,
            'time' => now()->format('H:i'),
        ];

        $this->message = '';
        $this->sending = true;

        try {
            $service = app(OpenClawService::class);
            $result = $service->sendMessage($text);

            $reply = 'No response received.';
            if ($result && isset($result['choices'][0]['message']['content'])) {
                // Standard OpenAI chat completions format
                $reply = $result['choices'][0]['message']['content'];
            } elseif ($result && isset($result['error'])) {
                $reply = '⚠️ ' . ($result['error']['message'] ?? 'Unknown error');
            }

            $this->messages[] = [
                'from' => 'kevin',
                'text' => $reply,
                'time' => now()->format('H:i'),
            ];
        } catch (\Exception $e) {
            $this->messages[] = [
                'from' => 'kevin',
                'text' => '⚠️ Connection error: ' . $e->getMessage(),
                'time' => now()->format('H:i'),
            ];
        }

        $this->sending = false;
    }

    public function render()
    {
        return view('livewire.messenger-panel');
    }
}
