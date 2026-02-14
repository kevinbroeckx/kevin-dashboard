<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenClawService
{
    protected string $baseUrl;
    protected string $authToken;

    public function __construct()
    {
        $host = config('services.openclaw.host', '127.0.0.1');
        $port = config('services.openclaw.port', 18789);
        $this->baseUrl = "http://{$host}:{$port}";
        $this->authToken = config('services.openclaw.token', '');
    }

    /**
     * Invoke a tool via the Gateway HTTP API.
     */
    public function invokeTool(string $tool, array $args = [], ?string $sessionKey = null): ?array
    {
        try {
            $payload = [
                'tool' => $tool,
                'args' => (object) $args,
            ];

            if ($sessionKey) {
                $payload['sessionKey'] = $sessionKey;
            }

            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->authToken}",
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/tools/invoke", $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('OpenClaw API error', [
                'tool' => $tool,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('OpenClaw API exception', [
                'tool' => $tool,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get session status.
     */
    public function getSessionStatus(?string $sessionKey = null): ?array
    {
        return $this->invokeTool('session_status', [], $sessionKey);
    }

    /**
     * List sessions.
     */
    public function listSessions(int $limit = 10, int $messageLimit = 3): ?array
    {
        return $this->invokeTool('sessions_list', [
            'limit' => $limit,
            'messageLimit' => $messageLimit,
        ]);
    }

    /**
     * Get session history.
     */
    public function getSessionHistory(string $sessionKey, int $limit = 20, bool $includeTools = true): ?array
    {
        return $this->invokeTool('sessions_history', [
            'sessionKey' => $sessionKey,
            'limit' => $limit,
            'includeTools' => $includeTools,
        ]);
    }

    /**
     * List cron jobs.
     */
    public function listCronJobs(): ?array
    {
        return $this->invokeTool('cron', ['action' => 'list']);
    }

    /**
     * Get cron scheduler status.
     */
    public function getCronStatus(): ?array
    {
        return $this->invokeTool('cron', ['action' => 'status']);
    }

    /**
     * Send a message to a session.
     */
    /**
     * Send a message via the OpenAI-compatible chat completions endpoint.
     * This is synchronous — it runs a full agent turn and returns the reply.
     */
    public function sendMessage(string $message, string $user = 'dashboard'): ?array
    {
        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->authToken}",
                    'Content-Type' => 'application/json',
                    'x-openclaw-agent-id' => 'main',
                ])
                ->post("{$this->baseUrl}/v1/chat/completions", [
                    'model' => 'anthropic/claude-haiku-4-5',
                    'user' => $user,
                    'messages' => [
                        ['role' => 'user', 'content' => $message],
                    ],
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('OpenClaw chat API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('OpenClaw chat API exception', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Check if the gateway is reachable.
     */
    public function isReachable(): bool
    {
        try {
            $response = Http::timeout(3)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->authToken}",
                ])
                ->get("{$this->baseUrl}/health");

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
