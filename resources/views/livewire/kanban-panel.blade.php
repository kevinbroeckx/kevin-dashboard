<div class="panel flex flex-col" style="min-height: 550px;">
    <div class="panel-header mb-2">
        <h2 class="panel-title">Kanban Board</h2>
    </div>

    {{-- Add Task Form --}}
    <form wire:submit="addTask" class="flex gap-2 mb-5">
        <input
            wire:model="newTask"
            type="text"
            placeholder="New task..."
            class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-[var(--color-accent)]/50 transition"
        />
        <select
            wire:model="newTaskColumn"
            class="bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-xs text-gray-400 focus:outline-none focus:border-[var(--color-accent)]/50 transition"
        >
            <option value="todo">To Do</option>
            <option value="doing">Doing</option>
            <option value="done">Done</option>
        </select>
        <button type="submit" class="px-4 py-2.5 bg-[var(--color-accent)] hover:bg-[var(--color-accent-light)] text-white text-sm font-semibold rounded-lg transition-all duration-200 transform hover:-translate-y-0.5">
            Add
        </button>
    </form>

    {{-- Kanban Columns --}}
    <div class="flex gap-4 flex-1 overflow-x-auto">
        @foreach(['todo' => 'To Do', 'doing' => 'In Progress', 'done' => 'Done'] as $key => $label)
            <div class="flex-1 min-w-[320px] flex flex-col">
                {{-- Column Header --}}
                <div class="flex items-center gap-3 mb-3 pb-3 border-b border-white/10">
                    <span class="w-3 h-3 rounded-full
                        {{ $key === 'todo' ? 'bg-[var(--color-info)]' : '' }}
                        {{ $key === 'doing' ? 'bg-[var(--color-warning)]' : '' }}
                        {{ $key === 'done' ? 'bg-[var(--color-success)]' : '' }}
                    "></span>
                    <div>
                        <div class="text-sm font-semibold text-gray-100">{{ $label }}</div>
                        <div class="text-xs text-gray-600 font-medium">{{ count($columns[$key] ?? []) }} tasks</div>
                    </div>
                </div>

                {{-- Column Cards --}}
                <div class="flex-1 space-y-2.5 overflow-y-auto pr-2">
                    @forelse(($columns[$key] ?? []) as $task)
                        <div class="p-3.5 rounded-lg bg-white/5 border border-white/10 hover:border-white/20 hover:bg-white/7.5 transition-all duration-200 group cursor-grab active:cursor-grabbing">
                            <div class="flex items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-200 font-medium leading-snug">{{ $task['title'] }}</p>
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    @if($key !== 'done')
                                        @php $nextCol = $key === 'todo' ? 'doing' : 'done'; @endphp
                                        <button
                                            wire:click="moveTask('{{ $task['id'] }}', '{{ $key }}', '{{ $nextCol }}')"
                                            class="p-1.5 rounded text-gray-500 hover:text-[var(--color-accent-light)] hover:bg-white/10 transition"
                                            title="Move right"
                                        >
                                            →
                                        </button>
                                    @endif
                                    <button
                                        wire:click="removeTask('{{ $task['id'] }}', '{{ $key }}')"
                                        class="p-1.5 rounded text-gray-500 hover:text-[var(--color-danger)] hover:bg-white/10 transition"
                                        title="Delete"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-600 text-xs font-medium">
                            No tasks yet
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
