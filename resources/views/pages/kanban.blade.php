@extends('layouts.sidebar')

@section('content')
    <div class="max-w-7xl mx-auto h-full flex flex-col">
        <h1 class="text-3xl font-bold text-white mb-6">Task Board</h1>
        
        <div class="flex-1 min-h-0 rounded-xl border border-white/5 bg-[var(--color-dark-800)] p-6">
            @livewire('kanban-panel')
        </div>
    </div>
@endsection
