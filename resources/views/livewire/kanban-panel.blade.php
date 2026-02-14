<div class="rounded-xl border border-white/5 bg-[var(--color-dark-800)] p-5">
    <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Tasks</h2>

    {{-- Add Task --}}
    <form wire:submit="addTask" class="flex gap-2 mb-4">
        <input
            wire:model="newTask"
            type="text"
            placeholder="New task..."
            class="flex-1 bg-[var(--color-dark-700)] border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-[var(--color-accent)]/50 transition"
        />
        <select
            wire:model="newTaskColumn"
            class="bg-[var(--color-dark-700)] border border-white/10 rounded-lg px-2 py-2 text-xs text-gray-400 focus:outline-none"
        >
            <option value="todo">To Do</option>
            <option value="doing">Doing</option>
            <option value="done">Done</option>
        </select>
        <button type="submit" class="px-3 py-2 bg-[var(--color-accent)] hover:bg-[var(--color-accent-dim)] text-white text-sm rounded-lg transition">+</button>
    </form>

    {{-- Columns --}}
    <div class="space-y-4">
        @foreach(['todo' => 'To Do', 'doing' => 'In Progress', 'done' => 'Done'] as $key => $label)
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="w-2 h-2 rounded-full
                        {{ $key === 'todo' ? 'bg-[var(--color-info)]' : '' }}
                        {{ $key === 'doing' ? 'bg-[var(--color-warning)]' : '' }}
                        {{ $key === 'done' ? 'bg-[var(--color-success)]' : '' }}
                    "></span>
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $label }}</span>
                    <span class="text-xs text-gray-600">({{ count($columns[$key] ?? []) }})</span>
                </div>
                <div class="space-y-1.5">
                    @foreach(($columns[$key] ?? []) as $task)
                        <div class="flex items-center gap-2 p-2.5 rounded-lg bg-[var(--color-dark-700)] border border-white/5 group">
                            <span class="flex-1 text-sm text-gray-300">{{ $task['title'] }}</span>
                            <div class="hidden group-hover:flex items-center gap-1">
                                @if($key !== 'done')
                                    @php $nextCol = $key === 'todo' ? 'doing' : 'done'; @endphp
                                    <button
                                        wire:click="moveTask('{{ $task['id'] }}', '{{ $key }}', '{{ $nextCol }}')"
                                        class="text-xs text-gray-500 hover:text-[var(--color-accent-light)] transition"
                                        title="Move to {{ $nextCol }}"
                                    >→</button>
                                @endif
                                <button
                                    wire:click="removeTask('{{ $task['id'] }}', '{{ $key }}')"
                                    class="text-xs text-gray-500 hover:text-[var(--color-danger)] transition"
                                    title="Remove"
                                >×</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
