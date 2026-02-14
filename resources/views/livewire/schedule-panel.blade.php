<div class="rounded-xl border border-white/5 bg-[var(--color-dark-800)] p-5" wire:poll.60s="loadJobs">
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Schedule</h2>
        @if($nextAction)
            <span class="text-xs font-mono text-[var(--color-warning)] bg-[var(--color-warning)]/10 px-2 py-1 rounded-md">
                Next: {{ $nextAction }}
            </span>
        @endif
    </div>

    <div class="space-y-3">
        @forelse($jobs as $job)
            <div class="flex items-start gap-3 p-3 rounded-lg bg-[var(--color-dark-700)] border border-white/5">
                <div class="w-2 h-2 rounded-full mt-1.5 {{ $job['status'] === 'active' ? 'bg-[var(--color-success)]' : 'bg-gray-600' }}"></div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-200">{{ $job['name'] }}</span>
                        <span class="text-xs font-mono text-gray-500">{{ $job['next'] }}</span>
                    </div>
                    <span class="text-xs text-gray-500">{{ $job['schedule'] }}</span>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 text-center py-4">No scheduled jobs</p>
        @endforelse
    </div>
</div>
