<div class="rounded-xl border border-white/5 bg-[var(--color-dark-800)] p-5 flex flex-col" style="min-height: 320px;">
    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Quick Message</h2>

    {{-- Messages --}}
    <div class="flex-1 overflow-y-auto space-y-2 mb-4 max-h-48" id="messenger-scroll" x-data x-init="
        $nextTick(() => $el.scrollTop = $el.scrollHeight);
        const observer = new MutationObserver(() => $el.scrollTop = $el.scrollHeight);
        observer.observe($el, { childList: true, subtree: true });
    ">
        @forelse($messages as $msg)
            <div class="flex {{ $msg['from'] === 'you' ? 'justify-end' : 'justify-start' }}">
                <div class="max-w-[80%] px-3 py-2 rounded-lg text-sm
                    {{ $msg['from'] === 'you'
                        ? 'bg-[var(--color-accent)] text-white'
                        : 'bg-[var(--color-dark-600)] text-gray-200' }}">
                    <p class="whitespace-pre-wrap">{{ $msg['text'] }}</p>
                    <span class="text-[10px] opacity-60 mt-1 block">{{ $msg['time'] }}</span>
                </div>
            </div>
        @empty
            <div class="flex items-center justify-center h-full">
                <p class="text-sm text-gray-600">Send Kevin a message...</p>
            </div>
        @endforelse

        @if($sending)
            <div class="flex justify-start">
                <div class="px-3 py-2 rounded-lg text-sm bg-[var(--color-dark-600)] text-gray-400">
                    <span class="animate-pulse">Kevin is thinking...</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Input --}}
    <form wire:submit="sendMessage" class="flex gap-2">
        <input
            wire:model="message"
            type="text"
            placeholder="Type a message..."
            @if($sending) disabled @endif
            class="flex-1 bg-[var(--color-dark-700)] border border-white/10 rounded-lg px-4 py-2.5 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-[var(--color-accent)]/50 focus:ring-1 focus:ring-[var(--color-accent)]/25 transition disabled:opacity-50"
        />
        <button
            type="submit"
            @if($sending) disabled @endif
            class="px-4 py-2.5 bg-[var(--color-accent)] hover:bg-[var(--color-accent-dim)] text-white text-sm font-medium rounded-lg transition disabled:opacity-50"
        >
            {{ $sending ? '...' : 'Send' }}
        </button>
    </form>
</div>
