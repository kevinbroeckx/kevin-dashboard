<div class="panel" wire:poll.60s="loadJobs">
    <div class="panel-header">
        <div class="flex items-center justify-between">
            <h2 class="panel-title">Schedule</h2>
            @if($nextAction)
                <span class="panel-subtitle">Next: {{ $nextAction }}</span>
            @endif
        </div>
    </div>

    <div class="space-y-3">
        @forelse($jobs as $job)
            <div class="p-3.5 rounded-lg bg-white/5 border border-white/10 hover:border-white/20 hover:bg-white/7.5 transition-all duration-200">
                <div class="flex items-start gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 {{ $job['status'] === 'active' ? 'bg-[var(--color-success)]' : 'bg-gray-600' }}"></div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-semibold text-gray-100">{{ $job['name'] }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $job['schedule'] }}</div>
                    </div>
                </div>
                
                {{-- Status Badges --}}
                <div class="flex items-center gap-2 mb-3 flex-wrap">
                    @if($job['lastResult'] === 'success')
                        <span class="text-xs px-2.5 py-1 rounded-full bg-[var(--color-success)]/15 text-[var(--color-success)] font-medium">✓ Success</span>
                    @elseif($job['lastResult'] === 'error')
                        <span class="text-xs px-2.5 py-1 rounded-full bg-[var(--color-danger)]/15 text-[var(--color-danger)] font-medium">✗ Failed</span>
                    @else
                        <span class="text-xs px-2.5 py-1 rounded-full bg-gray-500/15 text-gray-400 font-medium">⊙ Pending</span>
                    @endif
                </div>

                {{-- Job Info --}}
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-500 mb-3">
                    <div>Last: <span class="text-gray-400 font-mono text-xs">{{ $job['last'] }}</span></div>
                    <div>Next: <span class="text-gray-400 font-mono text-xs">{{ $job['next'] }}</span></div>
                </div>

                {{-- Run Now Button --}}
                @if($job['status'] === 'active')
                    <button wire:click="executeJob('{{ $job['id'] }}')" 
                        wire:loading.attr="disabled"
                        wire:target="executeJob('{{ $job['id'] }}')"
                        class="w-full text-xs font-semibold px-3 py-2 rounded-lg bg-[var(--color-accent)] text-white hover:bg-[var(--color-accent-light)] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                        @if(isset($executingJobs[$job['id']]) && $executingJobs[$job['id']])
                            ⏳ Running...
                        @else
                            ▶ Run Now
                        @endif
                    </button>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-500 text-center py-6">No scheduled jobs</p>
        @endforelse
    </div>
</div>
