<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kevin Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-[var(--color-dark-900)] text-gray-200 min-h-screen antialiased">
    {{-- Top Bar --}}
    <header class="sticky top-0 z-50 border-b border-white/5 bg-[var(--color-dark-800)]/80 backdrop-blur-xl">
        <div class="flex items-center justify-between px-6 py-3">
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-[var(--color-accent)] flex items-center justify-center text-white font-bold text-sm">K</div>
                    <span class="text-lg font-semibold text-white tracking-tight">Kevin</span>
                </div>
                <span class="text-xs text-gray-500 font-mono">Dashboard</span>
            </div>
            <div class="flex items-center gap-4">
                <div id="clock" class="text-sm font-mono text-gray-400"></div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-[var(--color-success)] animate-pulse"></div>
                    <span class="text-xs text-gray-400">Online</span>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Grid --}}
    <main class="p-4 lg:p-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 lg:gap-5 max-w-[1800px] mx-auto">
            {{-- Status Panel - 4 cols --}}
            <div class="lg:col-span-4">
                @livewire('status-panel')
            </div>

            {{-- Schedule Panel - 4 cols --}}
            <div class="lg:col-span-4">
                @livewire('schedule-panel')
            </div>

            {{-- Quick Messenger - 4 cols --}}
            <div class="lg:col-span-4">
                @livewire('messenger-panel')
            </div>

            {{-- Activity Feed - 8 cols --}}
            <div class="lg:col-span-8">
                @livewire('activity-panel')
            </div>

            {{-- Kanban Board - 4 cols --}}
            <div class="lg:col-span-4">
                @livewire('kanban-panel')
            </div>
        </div>
    </main>

    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ options }) => {
                options.timeout = 120000; // 120s for agent responses
            });
        });
    </script>
    <script>
        function updateClock() {
            const now = new Date();
            const opts = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Europe/Brussels' };
            document.getElementById('clock').textContent = now.toLocaleTimeString('en-GB', opts) + ' CET';
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
