<div class="rounded-xl border border-white/5 bg-[var(--color-dark-800)] p-5">
    <div class="mb-4">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider">Quick Actions</h2>
    </div>

    {{-- Action Status Feedback --}}
    @if($actionStatus === 'success' && !empty($lastAction))
        <div class="mb-4 p-3 rounded-lg bg-[var(--color-success)]/10 border border-[var(--color-success)]/30">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-[var(--color-success)]"></div>
                <p class="text-sm text-[var(--color-success)]">
                    @if($lastAction['type'] === 'job_executed')
                        ✓ Job executed: <strong>{{ $lastAction['jobName'] }}</strong>
                    @elseif($lastAction['type'] === 'gateway_restarted')
                        ✓ Gateway restart triggered
                    @endif
                </p>
            </div>
        </div>
    @elseif($actionStatus === 'executing' || $actionStatus === 'restarting')
        <div class="mb-4 p-3 rounded-lg bg-[var(--color-info)]/10 border border-[var(--color-info)]/30">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-[var(--color-info)] animate-pulse"></div>
                <p class="text-sm text-[var(--color-info)]">{{ $actionStatus === 'executing' ? 'Executing job...' : 'Restarting gateway...' }}</p>
            </div>
        </div>
    @elseif($actionError)
        <div class="mb-4 p-3 rounded-lg bg-[var(--color-danger)]/10 border border-[var(--color-danger)]/30">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-[var(--color-danger)]"></div>
                <p class="text-sm text-[var(--color-danger)]">{{ $actionError }}</p>
            </div>
        </div>
    @endif

    {{-- Actions Grid --}}
    <div class="space-y-3">
        {{-- Execute Cron Job --}}
        @if(!empty($jobs))
            <div class="flex gap-2">
                <select wire:model="selectedJobId" class="flex-1 px-3 py-2 text-sm rounded-lg bg-[var(--color-dark-700)] border border-white/10 text-gray-300 focus:outline-none focus:border-[var(--color-accent)]">
                    <option value="">Select a job...</option>
                    @foreach($jobs as $job)
                        @if($job['enabled'])
                            <option value="{{ $job['id'] }}">{{ $job['name'] }}</option>
                        @endif
                    @endforeach
                </select>
                <button wire:click="executeCronJob" wire:loading.attr="disabled" 
                    class="px-4 py-2 text-sm font-medium rounded-lg bg-[var(--color-accent)] text-white hover:bg-[var(--color-accent-dim)] transition-all duration-200 disabled:opacity-50">
                    Run Now
                </button>
            </div>
        @endif

        {{-- Restart Gateway --}}
        <button wire:click="restartGateway" wire:loading.attr="disabled"
            class="w-full px-4 py-2 text-sm font-medium rounded-lg border border-[var(--color-warning)]/30 text-[var(--color-warning)] hover:bg-[var(--color-warning)]/10 transition-all duration-200 disabled:opacity-50">
            🔄 Restart Gateway
        </button>

        {{-- View Session History --}}
        <button wire:click="viewSessionHistory"
            class="w-full px-4 py-2 text-sm font-medium rounded-lg border border-[var(--color-info)]/30 text-[var(--color-info)] hover:bg-[var(--color-info)]/10 transition-all duration-200">
            📋 Session History
        </button>

        {{-- Copy Session Key --}}
        <button wire:click="copyToClipboard('{{ $sessionKey }}', 'Session Key')"
            class="w-full px-4 py-2 text-sm font-medium rounded-lg border border-gray-600/30 text-gray-400 hover:bg-gray-500/10 transition-all duration-200 text-left flex items-center justify-between">
            <span>Session Key</span>
            <span class="font-mono text-xs">{{ $sessionKey }}</span>
        </button>

        {{-- Copy API Token --}}
        <button wire:click="copyToClipboard('{{ substr(config('services.openclaw.token', ''), 0, 20) }}...', 'API Token')"
            class="w-full px-4 py-2 text-sm font-medium rounded-lg border border-gray-600/30 text-gray-400 hover:bg-gray-500/10 transition-all duration-200 text-left flex items-center justify-between">
            <span>API Token</span>
            <span class="font-mono text-xs">{{ $authToken }}</span>
        </button>
    </div>
</div>

{{-- Session History Modal --}}
@if($showSessionHistory)
    <div class="fixed inset-0 bg-black/50 z-40 flex items-center justify-center p-4" wire:click="closeSessionHistory">
        <div class="bg-[var(--color-dark-800)] rounded-xl border border-white/10 max-w-2xl w-full max-h-[80vh] overflow-hidden flex flex-col" @click.stop>
            {{-- Header --}}
            <div class="flex items-center justify-between p-5 border-b border-white/5">
                <h3 class="text-lg font-semibold text-white">Session History</h3>
                <button wire:click="closeSessionHistory" class="text-gray-500 hover:text-gray-400">
                    ✕
                </button>
            </div>

            {{-- Content --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-2">
                @if(!empty($sessionHistory))
                    @foreach($sessionHistory as $entry)
                        <div class="p-3 rounded-lg bg-[var(--color-dark-700)] border border-white/5">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-xs font-mono text-gray-500">{{ $entry['timestamp'] ?? '-' }}</span>
                                @if(isset($entry['status']))
                                    <span class="text-xs px-2 py-0.5 rounded bg-{{ $entry['status'] === 'success' ? 'green' : 'red' }}-500/20 text-{{ $entry['status'] === 'success' ? 'green' : 'red' }}-400">
                                        {{ $entry['status'] }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-300">{{ $entry['message'] ?? $entry['action'] ?? '-' }}</p>
                            @if(isset($entry['details']) && !empty($entry['details']))
                                <details class="mt-2">
                                    <summary class="text-xs text-gray-500 cursor-pointer hover:text-gray-400">Details</summary>
                                    <pre class="mt-1 text-xs bg-black/30 p-2 rounded overflow-x-auto text-gray-400">{{ json_encode($entry['details'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                </details>
                            @endif
                        </div>
                    @endforeach
                @else
                    <p class="text-sm text-gray-500 text-center py-8">No history available</p>
                @endif
            </div>

            {{-- Footer --}}
            <div class="p-4 border-t border-white/5">
                <button wire:click="closeSessionHistory" class="w-full px-4 py-2 text-sm font-medium rounded-lg bg-[var(--color-dark-700)] text-gray-300 hover:bg-[var(--color-dark-600)] transition-all duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif

<script>
    document.addEventListener('livewire:navigated', () => {
        Livewire.on('copyToClipboard', ({ text, label }) => {
            navigator.clipboard.writeText(text).then(() => {
                // Show a brief toast notification
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 px-4 py-2 rounded-lg bg-green-500/20 border border-green-500/30 text-green-400 text-sm';
                toast.textContent = `Copied: ${label}`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 2000);
            });
        });

        Livewire.on('clearActionStatus', () => {
            setTimeout(() => {
                Livewire.dispatch('clearStatus');
            }, 3000);
        });
    });
</script>
