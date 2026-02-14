<div class="rounded-xl border border-white/5 bg-[var(--color-dark-800)] p-5" wire:poll.30s="loadStatus">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Status</h2>
        <div class="flex items-center gap-2">
            @if(!$connected)
                <div class="w-2.5 h-2.5 rounded-full bg-[var(--color-warning)] animate-pulse"></div>
                <span class="text-sm font-medium text-[var(--color-warning)]">No API</span>
            @else
                <div class="w-2.5 h-2.5 rounded-full {{ $status['state'] === 'online' ? 'bg-[var(--color-success)] animate-pulse' : 'bg-[var(--color-danger)]' }}"></div>
                <span class="text-sm font-medium {{ $status['state'] === 'online' ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]' }}">
                    {{ ucfirst($status['state']) }}
                </span>
            @endif
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between py-2 border-b border-white/5">
            <span class="text-sm text-gray-500">Model</span>
            <span class="text-sm font-mono text-[var(--color-accent-light)]">{{ $status['model'] }}</span>
        </div>
        <div class="flex items-center justify-between py-2 border-b border-white/5">
            <span class="text-sm text-gray-500">Session</span>
            <span class="text-sm font-mono text-gray-300">{{ $status['session'] }}</span>
        </div>
        @if($status['uptime'] !== '-')
        <div class="flex items-center justify-between py-2 border-b border-white/5">
            <span class="text-sm text-gray-500">Uptime</span>
            <span class="text-sm text-gray-300">{{ $status['uptime'] }}</span>
        </div>
        @endif
        @if($status['cost'] ?? null)
        <div class="flex items-center justify-between py-2 border-b border-white/5">
            <span class="text-sm text-gray-500">Session Cost</span>
            <span class="text-sm font-mono text-[var(--color-warning)]">${{ number_format($status['cost'], 4) }}</span>
        </div>
        @endif
        <div class="flex items-center justify-between py-2 border-b border-white/5">
            <span class="text-sm text-gray-500">Host</span>
            <span class="text-sm font-mono text-gray-300">{{ $status['host'] }}</span>
        </div>
        <div class="flex items-center justify-between py-2">
            <span class="text-sm text-gray-500">Stack</span>
            <span class="text-sm font-mono text-gray-300">PHP {{ $status['php'] }} / L{{ $status['laravel'] }}</span>
        </div>
    </div>
</div>
