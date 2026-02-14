<div class="panel" wire:poll.20s="loadSessions">
    <div class="panel-header">
        <div class="flex items-center justify-between">
            <h2 class="panel-title">Sessions</h2>
            <span class="panel-subtitle">{{ count($sessions) }} active</span>
        </div>
    </div>

    <div class="space-y-2">
        @forelse($sessions as $session)
            <div class="p-3.5 rounded-lg bg-white/5 border border-white/10 hover:border-white/20 hover:bg-white/7.5 transition-all duration-200">
                <div class="flex items-start gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-semibold text-gray-100 truncate">{{ $session['displayName'] }}</span>
                            <span class="text-xs font-mono text-gray-500 ml-2 flex-shrink-0">{{ $session['updatedAt'] }}</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 truncate">{{ $session['lastMsg'] }}</p>
                        <div class="flex items-center gap-2.5 mt-2.5 text-xs">
                            <span class="text-gray-600">{{ $session['model'] }}</span>
                            <span class="text-gray-600">•</span>
                            <span class="text-gray-600 font-mono">{{ number_format($session['tokens']) }} tokens</span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-500 text-center py-6">No sessions</p>
        @endforelse
    </div>
</div>
