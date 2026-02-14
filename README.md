# Kevin Dashboard

A real-time monitoring and control panel for [OpenClaw](https://openclaw.ai) — manage agent sessions, view activity feeds, schedule tasks, and interact with your AI assistant through a unified web interface.

## Features

- **Status Panel** — Real-time session metrics (model, uptime, token usage, cost)
- **Schedule Panel** — View and manage cron jobs with next execution times
- **Quick Messenger** — Chat directly with Kevin (OpenClaw agent) using Haiku (cost-optimized)
- **Activity Feed** — Real-time log of all tool calls, messages, and system events
- **Kanban Board** — Task management with full CRUD persistence
- **Auto-Refresh** — All panels poll the OpenClaw Gateway API for live data

## Architecture

- **Frontend**: Laravel 12 + Livewire 4 + Tailwind CSS 4 + Vite
- **Backend**: Laravel PHP 8.4+ (portable, no admin required)
- **Database**: SQLite (zero setup)
- **Integration**: OpenClaw Gateway HTTP APIs (`/v1/chat/completions`, `/tools/invoke`)

## Quick Start

### Prerequisites

- PHP 8.4+ (portable setup included)
- Node.js 20+
- OpenClaw Gateway running on port 18789
- 5 minutes

### Installation

1. **Clone and enter the directory**
   ```bash
   cd kevin-dashboard
   ```

2. **Copy environment config**
   ```bash
   cp .env.example .env
   ```

3. **Set your OpenClaw Gateway password**
   ```bash
   # Edit .env and set:
   OPENCLAW_TOKEN=your_gateway_password_here
   OPENCLAW_HOST=127.0.0.1  # or your gateway host
   OPENCLAW_PORT=18789
   ```

4. **Install dependencies**
   ```bash
   composer install
   npm install
   npm run build
   ```

5. **Generate app key**
   ```bash
   php artisan key:generate
   ```

6. **Run migrations** (creates SQLite database)
   ```bash
   php artisan migrate
   ```

7. **Start dev server**
   ```bash
   php artisan serve --host=0.0.0.0 --port=8000
   ```

8. **Open in browser**
   ```
   http://localhost:8000
   ```

## Configuration

### OpenClaw Gateway Setup

The dashboard requires:

1. **Enable `/v1/chat/completions` endpoint** in your OpenClaw config:
   ```json
   {
     "gateway": {
       "http": {
         "endpoints": {
           "chatCompletions": { "enabled": true }
         }
       }
     }
   }
   ```

2. **Allow `sessions_send` on HTTP tools API** (for future async messaging):
   ```json
   {
     "gateway": {
       "tools": {
         "allow": ["sessions_send"]
       }
     }
   }
   ```

3. **Verify gateway is reachable**:
   ```bash
   curl -H 'Authorization: Bearer YOUR_TOKEN' \
     http://127.0.0.1:18789/health
   ```

### Dashboard Environment

Edit `.env` to customize:

```bash
APP_NAME=Kevin Dashboard
APP_ENV=local
APP_DEBUG=true
OPENCLAW_HOST=127.0.0.1
OPENCLAW_PORT=18789
OPENCLAW_TOKEN=your_gateway_password_here
```

## Usage

### Panels Overview

| Panel | Refresh | Purpose |
|-------|---------|---------|
| **Status** | 30s | Session health, model, uptime, cost |
| **Schedule** | 60s | Cron jobs, next execution times |
| **Quick Messenger** | N/A | Real-time chat with Kevin (Haiku) |
| **Activity Feed** | 15s | Tool calls, messages, events |
| **Kanban** | Manual | Task management |

### Quick Messenger

- Send natural language messages to Kevin directly
- Responses use **Haiku** model (fast, cheap: ~€0.01/1K tokens)
- Perfect for quick queries, dashboard iterations, testing
- Conversations are isolated per `user` (doesn't pollute main session history)

### Cost Optimization

The dashboard is cost-optimized:

- **Haiku** for all dashboard interactions (€0.01/1K tokens)
- **Opus** reserved for complex architecture decisions, research
- Batch work into larger chunks to reduce API calls
- Monitor session cost in Status panel

## Development

### Adding a New Panel

1. Create a Livewire component:
   ```bash
   php artisan make:livewire MyNewPanel
   ```

2. Implement data loading:
   ```php
   public function mount(): void {
       $api = app(OpenClawService::class);
       $this->data = $api->someMethod();
   }
   ```

3. Create the view at `resources/views/livewire/my-new-panel.blade.php`

4. Add to dashboard layout in `resources/views/layouts/app.blade.php`:
   ```blade
   <div class="lg:col-span-4">
       @livewire('my-new-panel')
   </div>
   ```

### API Service

The `OpenClawService` handles all Gateway communication:

```php
$service = app(OpenClawService::class);

// Chat completions (synchronous, for dashboard messenger)
$result = $service->sendMessage("Your message");

// List sessions
$sessions = $service->listSessions();

// Get cron jobs
$jobs = $service->listCronJobs();

// Get session history
$history = $service->getSessionHistory('agent:main:main');
```

## Roadmap

- [ ] History panel — past sessions & work logs
- [ ] Memory panel — quick access to MEMORY.md snippets
- [ ] Alerts panel — cost thresholds, error tracking
- [ ] Model switcher — toggle Haiku/Opus per request
- [ ] Session switcher — browse sub-agent sessions
- [ ] Dark/light mode toggle
- [ ] Docker containerization
- [ ] Metrics dashboard (charts, cost tracking)

## Troubleshooting

### "Connection refused" on startup

**Problem**: Dashboard can't reach OpenClaw Gateway

**Solution**:
```bash
# Verify gateway is running
openclaw status

# Verify port and token
OPENCLAW_HOST=127.0.0.1
OPENCLAW_PORT=18789
OPENCLAW_TOKEN=your_password

# Test with curl
curl -H 'Authorization: Bearer YOUR_TOKEN' \
  http://127.0.0.1:18789/health
```

### Livewire requests timing out

**Problem**: Chat responses take >30s

**Solution**: Already handled — the dashboard sets a 120s timeout for `sessions_send`. If it's still timing out, increase in `resources/views/layouts/app.blade.php`:

```javascript
Livewire.hook('request', ({ options }) => {
    options.timeout = 180000; // 180s
});
```

### High costs on dashboard

**Problem**: Unexpected token usage

**Solution**: 
- Messenger uses Haiku (cheap). Verify in `.env`: `OPENCLAW_TOKEN` points to correct gateway
- Check Status panel for actual session cost
- Reduce polling intervals if needed (modify `wire:poll.Xs` in views)

## Performance Tips

- **Polling intervals**: Currently optimized (Status 30s, Schedule 60s, Activity 15s)
- **Messenger**: Haiku is ~30x cheaper than Opus — no need to escalate unless stuck
- **Batch work**: Instead of iterating 10 times, bundle requests into 2-3 turns

## License

MIT

## Support

- 📖 [OpenClaw Docs](https://docs.openclaw.ai)
- 🐛 Found a bug? Check issues or create a new one
- 💡 Have ideas? Open a discussion

---

**Built with ⚙️ for controlling OpenClaw**
