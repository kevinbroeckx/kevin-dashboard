<div class="panel" wire:poll.30s="loadStatus">
    <div class="panel-header">
        <div class="flex items-center justify-between">
            <h2 class="panel-title">Status</h2>
            <div class="flex items-center gap-2">
                @if(!$connected)
                    <div class="w-2.5 h-2.5 rounded-full bg-[var(--color-warning)] animate-pulse"></div>
                    <span class="text-xs font-medium text-[var(--color-warning)]">No API</span>
                @else
                    <div class="w-2.5 h-2.5 rounded-full {{ $status['state'] === 'online' ? 'bg-[var(--color-success)] animate-pulse' : 'bg-[var(--color-danger)]' }}"></div>
                    <span class="text-xs font-medium {{ $status['state'] === 'online' ? 'text-[var(--color-success)]' : 'text-[var(--color-danger)]' }}">
                        {{ ucfirst($status['state']) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <div class="flex items-center justify-between py-2.5 border-b border-white/5">
            <span class="text-sm text-gray-500 font-medium">Model</span>
            <span class="text-sm font-mono text-[var(--color-accent-light)] font-semibold">{{ $status['model'] }}</span>
        </div>
        <div class="flex items-center justify-between py-2.5 border-b border-white/5">
            <span class="text-sm text-gray-500 font-medium">Session</span>
            <span class="text-sm font-mono text-gray-300 truncate ml-2">{{ $status['session'] }}</span>
        </div>
        @if($status['uptime'] !== '-')
        <div class="flex items-center justify-between py-2.5 border-b border-white/5">
            <span class="text-sm text-gray-500 font-medium">Uptime</span>
            <span class="text-sm text-gray-300">{{ $status['uptime'] }}</span>
        </div>
        @endif
        @if($status['cost'] ?? null)
        <div class="flex items-center justify-between py-2.5 border-b border-white/5">
            <span class="text-sm text-gray-500 font-medium">Cost</span>
            <span class="text-sm font-mono text-[var(--color-warning)] font-semibold">${{ number_format($status['cost'], 4) }}</span>
        </div>
        @endif
        <div class="flex items-center justify-between py-2.5 border-b border-white/5">
            <span class="text-sm text-gray-500 font-medium">Host</span>
            <span class="text-sm font-mono text-gray-300">{{ $status['host'] }}</span>
        </div>
        <div class="flex items-center justify-between py-2.5">
            <span class="text-sm text-gray-500 font-medium">Stack</span>
            <span class="text-sm font-mono text-gray-300">PHP {{ $status['php'] }} / L{{ $status['laravel'] }}</span>
        </div>
    </div>
</div>
