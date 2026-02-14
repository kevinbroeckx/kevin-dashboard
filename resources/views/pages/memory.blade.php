@extends('layouts.sidebar')

@section('content')
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">Memory Vault</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Memory Panel (Full Width) --}}
            <div class="lg:col-span-3">
                @livewire('memory-panel')
            </div>

            {{-- Memory Stats --}}
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Info</h2>
                </div>
                <div class="space-y-4 text-sm">
                    <div class="py-2 border-b border-white/5">
                        <div class="text-gray-500 mb-1">Total Files</div>
                        <div class="text-2xl font-bold text-[var(--color-accent)]">12</div>
                    </div>
                    <div class="py-2 border-b border-white/5">
                        <div class="text-gray-500 mb-1">Last Updated</div>
                        <div class="text-sm text-gray-300 font-mono">2026-02-14</div>
                    </div>
                    <div class="py-2">
                        <div class="text-gray-500 mb-1">Size</div>
                        <div class="text-sm text-gray-300 font-mono">324 KB</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
