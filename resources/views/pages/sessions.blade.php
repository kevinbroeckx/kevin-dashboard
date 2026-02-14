@extends('layouts.sidebar')

@section('content')
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">Active Sessions</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Sessions List --}}
            <div class="lg:col-span-2">
                @livewire('sessions-list-panel')
            </div>

            {{-- Session Summary --}}
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Summary</h2>
                </div>
                <div class="space-y-4 text-sm">
                    <div class="py-2 border-b border-white/5">
                        <div class="text-gray-500 mb-1">Total Sessions</div>
                        <div class="text-2xl font-bold text-[var(--color-accent)]">4</div>
                    </div>
                    <div class="py-2 border-b border-white/5">
                        <div class="text-gray-500 mb-1">Avg Token Usage</div>
                        <div class="text-lg font-mono text-gray-300">45.5k</div>
                    </div>
                    <div class="py-2">
                        <div class="text-gray-500 mb-1">Most Active</div>
                        <div class="text-sm text-gray-300 font-medium">main session</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
