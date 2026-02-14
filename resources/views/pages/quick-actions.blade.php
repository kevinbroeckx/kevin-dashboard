@extends('layouts.sidebar')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">Quick Actions</h1>
        
        <div class="grid grid-cols-1 gap-6">
            @livewire('quick-actions-panel')
        </div>
    </div>
@endsection
