@extends('layouts.sidebar')

@section('content')
    <div class="w-full">
        <h1 class="text-3xl font-bold text-white mb-6">Task Board</h1>
        
        <div class="rounded-xl border border-white/5 bg-[var(--color-dark-800)] p-6 overflow-hidden" style="height: 70vh; min-height: 600px;">
            @livewire('kanban-panel')
        </div>
    </div>
@endsection
