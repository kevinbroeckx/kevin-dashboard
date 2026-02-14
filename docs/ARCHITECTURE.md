# Architecture

Technical deep-dive into the Kevin Dashboard: how it's structured, how it talks to OpenClaw, and how to extend it.

## Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    Browser (Livewire UI)                    │
│  Status | Schedule | Messenger | Activity | Kanban         │
└────────────────────┬────────────────────────────────────────┘
                     │
                     │ HTTP (Livewire + AJAX)
                     │
         ┌───────────▼────────────┐
         │  Laravel 12 App        │
         │  - OpenClawService     │
         │  - Livewire Components │
         │  - Routes/Middleware   │
         └───────────┬────────────┘
                     │
                     │ HTTP API calls (Bearer token auth)
                     │
         ┌───────────▼────────────────────────────┐
         │   OpenClaw Gateway (port 18789)        │
         │   - /v1/chat/completions               │
         │   - /tools/invoke                      │
         │   - /health                            │
         └────────────┬─────────────────────────┬─┘
                      │                         │
          ┌───────────▼──────┐      ┌──────────▼──────┐
          │  Agent Session   │      │  Cron Scheduler │
          │ (agent:main:main)│      │  /  Tool Policy │
          └──────────────────┘      └─────────────────┘
```

## Stack

### Frontend

- **Framework**: Livewire 4 (reactive Laravel components)
- **Styling**: Tailwind CSS 4
- **Build**: Vite (Lightning-fast HMR)
- **Language**: Blade templates + Alpine.js

### Backend

- **Framework**: Laravel 12
- **Language**: PHP 8.4+
- **Database**: SQLite (lightweight, zero-config)
- **Queue**: Database-backed (built-in)

### Infrastructure

- **Dev Server**: PHP built-in server (`php artisan serve`)
- **Gateway**: OpenClaw Gateway (HTTP REST + WebSocket)
- **Auth**: Bearer token (gateway password)

## File Structure

```
kevin-dashboard/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # API controllers (minimal)
│   │   └── Middleware/           # Auth, CORS, etc
│   ├── Livewire/
│   │   ├── StatusPanel.php       # Real-time session status
│   │   ├── SchedulePanel.php     # Cron job listing
│   │   ├── MessengerPanel.php    # Chat with Kevin
│   │   ├── ActivityPanel.php     # Event feed
│   │   └── KanbanPanel.php       # Task board
│   ├── Services/
│   │   └── OpenClawService.php   # Gateway API wrapper
│   ├── Models/
│   │   └── Task.php              # Kanban tasks
│   └── Providers/
│       └── AppServiceProvider.php
├── resources/
│   ├── views/
│   │   ├── layouts/app.blade.php # Main layout
│   │   ├── dashboard.blade.php   # Dashboard page
│   │   └── livewire/             # Panel templates
│   ├── css/
│   │   └── app.css               # Tailwind + custom theme
│   └── js/
│       └── app.js                # Alpine setup
├── routes/
│   ├── web.php                   # Web routes
│   └── api.php                   # API routes (future)
├── config/
│   ├── services.php              # OpenClaw config
│   └── database.php              # SQLite config
├── database/
│   ├── migrations/               # DB schema
│   ├── seeders/                  # Demo data
│   └── database.sqlite           # SQLite file (auto-created)
├── public/
│   ├── index.php                 # Entry point
│   └── build/                    # Compiled assets (Vite)
├── .env.example                  # Template config
├── composer.json                 # PHP dependencies
├── package.json                  # Node dependencies
├── tailwind.config.js            # Tailwind theme
└── vite.config.js                # Vite bundler config
```

## Component Details

### OpenClawService

**Location**: `app/Services/OpenClawService.php`

Central hub for all OpenClaw Gateway communication. Uses Laravel's HTTP client with bearer token authentication.

**Key Methods**:

```php
// Chat completions (synchronous, for messenger)
sendMessage(string $message, string $user = 'dashboard'): ?array

// List all sessions
listSessions(int $limit = 10): ?array

// Get a session's history
getSessionHistory(string $sessionKey, int $limit = 20): ?array

// List cron jobs
listCronJobs(): ?array

// Get gateway status
isReachable(): bool
```

**Model Selection**:
- Defaults to `anthropic/claude-haiku-4-5` (cheap)
- Can override per-request in methods

### Livewire Components

Each panel is a reactive Livewire component that:
1. Loads data on `mount()`
2. Auto-refreshes via `wire:poll`
3. Emits updates to browser without page reload

**Example: StatusPanel**

```php
public function mount(): void {
    $this->loadStatus();  // Initial load
}

public function loadStatus(): void {
    $api = app(OpenClawService::class);
    $result = $api->getSessionStatus();
    
    // Parse response into $this->status
}

// View has: wire:poll.30s="loadStatus"
// Refreshes every 30 seconds automatically
```

### Polling Strategy

- **Status**: 30s (session metrics don't change fast)
- **Schedule**: 60s (cron jobs are stable)
- **Activity**: 15s (captures recent events)
- **Messenger**: On-demand (user submits message)
- **Kanban**: Manual (user creates/edits tasks)

## API Integration

### /v1/chat/completions

Used by Quick Messenger for synchronous, request-response interaction.

**Request**:
```json
{
  "model": "anthropic/claude-haiku-4-5",
  "user": "dashboard",
  "messages": [
    { "role": "user", "content": "Your message here" }
  ]
}
```

**Response**:
```json
{
  "choices": [
    {
      "message": {
        "role": "assistant",
        "content": "Response text here"
      }
    }
  ]
}
```

### /tools/invoke

Used for one-off tool calls (currently disabled in dashboard, but available for future use).

**Request**:
```json
{
  "tool": "sessions_list",
  "args": {}
}
```

**Response**:
```json
{
  "ok": true,
  "result": { ... }
}
```

## Authentication

All Gateway requests include a Bearer token:

```
Authorization: Bearer YOUR_GATEWAY_PASSWORD
```

Set in `.env`:
```
OPENCLAW_TOKEN=your_password_here
```

Loaded by `OpenClawService::__construct()` from `config/services.php`.

## Data Flow Example: Quick Messenger

1. **User types message** → Form submit
2. **Livewire transmits** → AJAX POST to `/livewire/message`
3. **MessengerPanel::sendMessage()** is called
4. **OpenClawService::sendMessage()** builds request
5. **HTTP POST to Gateway** → `/v1/chat/completions`
6. **Gateway runs agent turn** → Calls model, executes tools, returns reply
7. **Response parsed** → Extracted `choices[0].message.content`
8. **Livewire updates UI** → New message appears in chat bubble
9. **JavaScript auto-scrolls** → Scroll to bottom of feed

**Round-trip time**: ~2-5s (Haiku model latency)

## Cost Model

### Per-Request Costs

| Component | Model | Cost/1K Tokens | Use Case |
|-----------|-------|---|----------|
| Messenger | Haiku | €0.01 | Dashboard chats, testing |
| Activity Feed | Via API | N/A | Polling existing data |
| Status/Schedule | Via API | N/A | Polling existing data |

**Total Cost**: Mostly Haiku usage (cheap). Status/Schedule/Activity just query existing sessions (no inference).

### Monthly Estimate

- 50 dashboard chats/day
- ~500 tokens avg per chat
- Haiku: €0.01/1K tokens

```
50 chats × 30 days × 500 tokens × €0.01/1K
= 750,000 tokens × €0.01/1K
= €7.50/month
```

Plus cost of actual agent work (happens via Telegram/main session, not dashboard).

## Extending the Dashboard

### Adding a New Panel

1. **Generate component**:
   ```bash
   php artisan make:livewire MyPanel
   ```

2. **Implement logic** (e.g., `app/Livewire/MyPanel.php`):
   ```php
   public function mount(): void {
       $this->loadData();
   }

   public function loadData(): void {
       $api = app(OpenClawService::class);
       $this->data = $api->someMethod();
   }
   ```

3. **Create view** (e.g., `resources/views/livewire/my-panel.blade.php`):
   ```blade
   <div class="rounded-xl border..." wire:poll.30s="loadData">
       <!-- Your HTML here -->
   </div>
   ```

4. **Add to layout** (`resources/views/layouts/app.blade.php`):
   ```blade
   <div class="lg:col-span-4">
       @livewire('my-panel')
   </div>
   ```

### Adding a New Gateway API Call

1. **Add method to OpenClawService**:
   ```php
   public function getMyData(): ?array {
       return $this->invokeTool('my_tool', [
           'arg1' => 'value1',
       ]);
   }
   ```

2. **Call from component**:
   ```php
   $api = app(OpenClawService::class);
   $result = $api->getMyData();
   ```

## Performance Considerations

### Polling Overhead

Each panel's polling request takes ~200ms + network latency.

- 4 panels × 15-60s intervals = ~4-15 requests/minute to gateway
- ~240-900 requests/hour
- Negligible CPU impact

### Database

SQLite is single-file, zero-config, but has limitations:

- Concurrent writes will block
- Not recommended for >100 concurrent users
- Perfect for single-user dashboard

### Frontend Performance

- **Livewire**: Uses morphdom diffing (only updates changed elements)
- **Tailwind**: Purged at build time (~30KB CSS)
- **Alpine.js**: Minimal (5KB), handles scroll/interactivity)
- **Load time**: ~200ms on localhost

## Security Notes

⚠️ **This dashboard is designed for trusted, local networks only.**

- No built-in user authentication (relies on gateway token)
- No rate limiting on API routes
- No CORS restrictions
- Exposes agent session history

**For production use**:
- Add OAuth/JWT authentication
- Enable HTTPS only
- Restrict to VPN/private network
- Implement rate limiting
- Audit logging

---

**Questions?** Check the README or open a discussion.
