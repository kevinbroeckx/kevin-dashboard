@extends('layouts.sidebar')

@section('content')
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Status Mini --}}
            <div class="lg:col-span-1">
                @livewire('status-panel')
            </div>

            {{-- Schedule Mini --}}
            <div class="lg:col-span-1">
                @livewire('schedule-panel')
            </div>

            {{-- Quick Stats --}}
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Quick Stats</h2>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2">
                        <span class="text-sm text-gray-500">Active Sessions</span>
                        <span class="text-lg font-bold text-[var(--color-accent)]" wire:poll.20s="getSessions">-</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-t border-white/5">
                        <span class="text-sm text-gray-500">Token Usage</span>
                        <span class="text-lg font-bold text-[var(--color-warning)]">-</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-t border-white/5">
                        <span class="text-sm text-gray-500">Next Job</span>
                        <span class="text-sm font-mono text-gray-400">-</span>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="lg:col-span-2">
                @livewire('activity-panel')
            </div>

            {{-- Messenger --}}
            <div class="lg:col-span-1">
                @livewire('messenger-panel')
            </div>
        </div>
    </div>
@endsection
