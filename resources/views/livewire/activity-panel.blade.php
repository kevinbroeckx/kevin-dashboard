<div class="rounded-xl border border-white/5 bg-[var(--color-dark-800)] p-5" wire:poll.15s="loadActivities">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Activity Feed</h2>
        <span class="text-xs text-gray-600 font-mono">Live</span>
    </div>

    <div class="space-y-1">
        @forelse($activities as $activity)
            <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-[var(--color-dark-700)] transition group">
                <span class="text-lg mt-0.5">{{ $activity['icon'] }}</span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-200">{{ $activity['title'] }}</span>
                        <span class="text-xs font-mono text-gray-600">{{ $activity['time'] }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $activity['detail'] }}</p>
                </div>
                <span class="text-[10px] uppercase tracking-wider font-medium px-2 py-0.5 rounded
                    {{ $activity['type'] === 'tool' ? 'text-[var(--color-info)] bg-[var(--color-info)]/10' : '' }}
                    {{ $activity['type'] === 'message' ? 'text-[var(--color-success)] bg-[var(--color-success)]/10' : '' }}
                    {{ $activity['type'] === 'system' ? 'text-[var(--color-warning)] bg-[var(--color-warning)]/10' : '' }}
                ">{{ $activity['type'] }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-500 text-center py-8">No recent activity</p>
        @endforelse
    </div>
</div>
