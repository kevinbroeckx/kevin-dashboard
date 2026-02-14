<!DOCTYPE html>
<html lang="en" class="dark" id="html-root">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kevin Dashboard</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400,500" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <script>
        const darkMode = localStorage.getItem('darkMode') !== 'false';
        const htmlElement = document.documentElement;
        if (darkMode) {
            htmlElement.classList.add('dark');
        } else {
            htmlElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-[var(--color-dark-900)] text-gray-200 min-h-screen antialiased">
    <div class="flex h-screen">
        {{-- Sidebar --}}
        <aside class="w-64 border-r border-white/5 bg-[var(--color-dark-800)] flex flex-col">
            {{-- Logo --}}
            <div class="p-6 border-b border-white/5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-[var(--color-accent)] flex items-center justify-center text-white font-bold text-lg">K</div>
                    <div>
                        <div class="text-base font-semibold text-white">Kevin</div>
                        <div class="text-xs text-gray-500">Dashboard v0.2</div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('overview') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('overview') ? 'bg-[var(--color-accent)] text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }} transition-all duration-200">
                    <span class="text-lg">📊</span>
                    <span class="text-sm font-medium">Overview</span>
                </a>

                <a href="{{ route('status') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('status') ? 'bg-[var(--color-accent)] text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }} transition-all duration-200">
                    <span class="text-lg">🔋</span>
                    <span class="text-sm font-medium">Status</span>
                </a>

                <a href="{{ route('schedule') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('schedule') ? 'bg-[var(--color-accent)] text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }} transition-all duration-200">
                    <span class="text-lg">⏱️</span>
                    <span class="text-sm font-medium">Schedule</span>
                </a>

                <a href="{{ route('sessions') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('sessions') ? 'bg-[var(--color-accent)] text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }} transition-all duration-200">
                    <span class="text-lg">💬</span>
                    <span class="text-sm font-medium">Sessions</span>
                </a>

                <a href="{{ route('memory') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('memory') ? 'bg-[var(--color-accent)] text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }} transition-all duration-200">
                    <span class="text-lg">🧠</span>
                    <span class="text-sm font-medium">Memory</span>
                </a>

                <a href="{{ route('kanban') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('kanban') ? 'bg-[var(--color-accent)] text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }} transition-all duration-200">
                    <span class="text-lg">📋</span>
                    <span class="text-sm font-medium">Kanban</span>
                </a>

                <a href="{{ route('quick-actions') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('quick-actions') ? 'bg-[var(--color-accent)] text-white' : 'text-gray-400 hover:text-gray-200 hover:bg-white/5' }} transition-all duration-200">
                    <span class="text-lg">⚡</span>
                    <span class="text-sm font-medium">Quick Actions</span>
                </a>
            </nav>

            {{-- Footer --}}
            <div class="p-4 border-t border-white/5 space-y-2">
                <button id="darkModeToggle" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-gray-400 hover:text-gray-200 hover:bg-white/5 transition-all duration-200 text-sm font-medium">
                    <span id="darkModeIcon">🌙</span>
                    <span>Dark Mode</span>
                </button>
                <div class="flex items-center gap-2 px-4 py-2.5 text-xs">
                    <div class="w-2 h-2 rounded-full bg-[var(--color-success)] animate-pulse"></div>
                    <span class="text-gray-400">Online</span>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col">
            {{-- Top Bar --}}
            <header class="sticky top-0 z-40 border-b border-white/5 bg-[var(--color-dark-800)]/80 backdrop-blur-xl">
                <div class="flex items-center justify-between px-8 py-4">
                    <div id="page-title" class="text-xl font-semibold text-white">Dashboard</div>
                    <div id="clock" class="text-sm font-mono text-gray-400"></div>
                </div>
            </header>

            {{-- Content Area --}}
            <main class="flex-1 overflow-y-auto p-8">
                @yield('content')
            </main>
        </div>
    </div>

    @livewireScripts
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.hook('request', ({ options }) => {
                options.timeout = 120000;
            });
        });

        // Dark mode toggle
        const darkModeToggle = document.getElementById('darkModeToggle');
        const darkModeIcon = document.getElementById('darkModeIcon');
        const htmlElement = document.getElementById('html-root');

        function updateDarkModeIcon() {
            const isDark = htmlElement.classList.contains('dark');
            darkModeIcon.textContent = isDark ? '☀️' : '🌙';
        }

        darkModeToggle?.addEventListener('click', () => {
            const isDark = htmlElement.classList.contains('dark');
            if (isDark) {
                htmlElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            } else {
                htmlElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            }
            updateDarkModeIcon();
        });

        updateDarkModeIcon();

        // Clock update
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
