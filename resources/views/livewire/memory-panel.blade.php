<div class="panel flex flex-col" style="min-height: 450px;">
    <div class="panel-header">
        <div class="flex items-center justify-between">
            <h2 class="panel-title">Memory Vault</h2>
            <span class="panel-subtitle">{{ count($availableFiles) }} files</span>
        </div>
    </div>

    {{-- File Selector --}}
    @if(!empty($availableFiles))
        <div class="flex gap-2 mb-4 flex-wrap">
            @foreach($availableFiles as $file)
                <button
                    wire:click="selectFile('{{ $file['path'] }}')"
                    class="text-xs px-3 py-2 rounded-lg font-medium transition-all duration-200 {{ $selectedFile === $file['path'] ? 'bg-[var(--color-accent)] text-white shadow-lg' : 'bg-white/5 text-gray-400 border border-white/10 hover:border-white/20 hover:text-gray-200' }}"
                >
                    {{ $file['name'] }}
                </button>
            @endforeach
        </div>
    @endif

    {{-- Content --}}
    <div class="flex-1 overflow-y-auto">
        @if($fileNotFound)
            <div class="text-sm text-gray-500 text-center py-8">
                <p>File not found</p>
            </div>
        @elseif(empty($content))
            <div class="text-sm text-gray-500 text-center py-8">
                <p>No memory files yet</p>
            </div>
        @else
            <div class="text-sm font-mono text-gray-300 whitespace-pre-wrap break-words leading-relaxed">{{ $content }}</div>
        @endif
    </div>
</div>
