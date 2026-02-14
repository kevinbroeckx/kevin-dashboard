<div class="w-full h-full flex flex-col">
    {{-- Add Task Form --}}
    <form wire:submit="addTask" class="flex gap-2 mb-6">
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
    <div class="flex gap-4 flex-1 min-h-0 overflow-x-auto">
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
                        <button
                            wire:click="openTask('{{ $task['id'] }}', '{{ $key }}')"
                            class="w-full text-left p-3.5 rounded-lg bg-white/5 border border-white/10 hover:border-white/20 hover:bg-white/7.5 transition-all duration-200 group"
                        >
                            <div class="flex items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm text-gray-200 font-medium leading-snug">{{ $task['title'] }}</p>
                                    @if($task['description'] ?? null)
                                        <p class="text-xs text-gray-500 mt-1.5 line-clamp-2">{{ $task['description'] }}</p>
                                    @endif
                                    @if($task['priority'] ?? null)
                                        <div class="mt-2">
                                            <span class="text-xs px-2 py-1 rounded-full {{ 
                                                $task['priority'] === 'high' ? 'bg-[var(--color-danger)]/20 text-[var(--color-danger)]' : 
                                                ($task['priority'] === 'medium' ? 'bg-[var(--color-warning)]/20 text-[var(--color-warning)]' : 
                                                'bg-[var(--color-info)]/20 text-[var(--color-info)]')
                                            }}">{{ ucfirst($task['priority']) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                    @if($key !== 'done')
                                        @php $nextCol = $key === 'todo' ? 'doing' : 'done'; @endphp
                                        <button
                                            wire:click.stop="moveTask('{{ $task['id'] }}', '{{ $key }}', '{{ $nextCol }}')"
                                            class="p-1.5 rounded text-gray-500 hover:text-[var(--color-accent-light)] hover:bg-white/10 transition"
                                            title="Move right"
                                        >
                                            →
                                        </button>
                                    @endif
                                    <button
                                        wire:click.stop="removeTask('{{ $task['id'] }}', '{{ $key }}')"
                                        class="p-1.5 rounded text-gray-500 hover:text-[var(--color-danger)] hover:bg-white/10 transition"
                                        title="Delete"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="text-center py-8 text-gray-600 text-xs font-medium">
                            No tasks yet
                        </div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    {{-- Task Detail Modal --}}
    @if($selectedTaskId && $selectedTaskColumn)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4" wire:click="closeTask">
            <div class="bg-[var(--color-dark-800)] rounded-xl border border-white/10 w-full max-w-2xl max-h-[90vh] overflow-y-auto" wire:click.stop>
                {{-- Modal Header --}}
                <div class="sticky top-0 flex items-center justify-between p-6 border-b border-white/10 bg-[var(--color-dark-800)]/95 backdrop-blur">
                    <h3 class="text-xl font-semibold text-white">Task Details</h3>
                    <button wire:click="closeTask" class="p-2 hover:bg-white/10 rounded-lg text-gray-400 hover:text-gray-200 transition">
                        ✕
                    </button>
                </div>

                {{-- Modal Content --}}
                <div class="p-6 space-y-5">
                    {{-- Title --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Title</label>
                        <input
                            wire:model="editTitle"
                            type="text"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-[var(--color-accent)]/50 transition"
                            placeholder="Task title..."
                        />
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</label>
                        <textarea
                            wire:model="editDescription"
                            rows="3"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-[var(--color-accent)]/50 transition resize-none"
                            placeholder="What is this task about?"
                        ></textarea>
                    </div>

                    {{-- Ultimate Goal --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ultimate Goal</label>
                        <textarea
                            wire:model="editGoal"
                            rows="3"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-sm text-gray-200 placeholder-gray-600 focus:outline-none focus:border-[var(--color-accent)]/50 transition resize-none"
                            placeholder="What are we trying to achieve?"
                        ></textarea>
                    </div>

                    {{-- Priority --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Priority</label>
                        <select
                            wire:model="editPriority"
                            class="w-full bg-white/5 border border-white/10 rounded-lg px-3 py-2.5 text-sm text-gray-200 focus:outline-none focus:border-[var(--color-accent)]/50 transition"
                        >
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="sticky bottom-0 flex items-center gap-3 p-6 border-t border-white/10 bg-[var(--color-dark-800)]/95 backdrop-blur">
                    <button
                        wire:click="saveTask"
                        class="flex-1 px-4 py-2.5 bg-[var(--color-accent)] hover:bg-[var(--color-accent-light)] text-white font-semibold rounded-lg transition-all duration-200"
                    >
                        Save Changes
                    </button>
                    <button
                        wire:click="closeTask"
                        class="flex-1 px-4 py-2.5 bg-white/5 hover:bg-white/10 text-gray-200 font-semibold rounded-lg transition-all duration-200"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
