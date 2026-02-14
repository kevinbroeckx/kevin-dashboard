# Kevin Dashboard — Roadmap

## Phase 1: Foundation ✅
- [x] Laravel 12 project setup
- [x] PHP 8.4.16 portable install
- [x] Livewire 4 + Tailwind 4 + Vite
- [x] Dark ops-theme layout
- [x] 5 panel components (status, schedule, messenger, activity, kanban)
- [x] Kanban with full CRUD + file persistence

## Phase 2: OpenClaw API Integration 🔧
- [ ] Create OpenClaw API service class
- [ ] Status Panel → real data from gateway API (model, uptime, session info, usage/cost)
- [ ] Schedule Panel → real cron jobs from gateway (list, next fire, run history)
- [ ] Activity Feed → session history / tool call logs
- [ ] Messenger → send messages via sessions API, receive responses
- [ ] Auth middleware (simple token or local-only)

## Phase 3: Real-Time & Polish
- [ ] WebSocket or SSE for live activity feed
- [ ] Notifications panel (urgent items, errors)
- [ ] Cost/usage graphs (daily/weekly)
- [ ] Memory viewer (browse memory/*.md files)
- [ ] Session list panel (see all active sessions)
- [ ] Responsive mobile layout
- [ ] Keyboard shortcuts

## Phase 4: Advanced
- [ ] Multi-agent support (if multiple agents)
- [ ] Log viewer with filtering
- [ ] Config editor (edit HEARTBEAT.md, etc.)
- [ ] Deployment mode (serve externally with auth)

## Architecture Decisions
- **Livewire** over SPA: Less JS overhead, reactive without a separate frontend framework
- **SQLite**: Kanban state + cached data, zero config
- **File-based tasks**: tasks.json so Kevin (the agent) can also read/write to it
- **Polling over WebSocket**: Simpler for v1, upgrade later if needed
