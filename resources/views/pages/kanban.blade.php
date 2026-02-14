@extends('layouts.sidebar')

@section('content')
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">Task Board</h1>
        
        @livewire('kanban-panel')
    </div>
@endsection
