@extends('layouts.sidebar')

@section('content')
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">System Status</h1>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="lg:col-span-2">
                @livewire('status-panel')
            </div>

            {{-- Detailed Info --}}
            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Gateway Info</h2>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <span class="text-gray-500">Status</span>
                        <span class="font-mono text-[var(--color-success)]">🟢 Online</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <span class="text-gray-500">Model</span>
                        <span class="font-mono text-gray-300">Haiku</span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-white/5">
                        <span class="text-gray-500">Version</span>
                        <span class="font-mono text-gray-300">v0.2</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span class="text-gray-500">Uptime</span>
                        <span class="font-mono text-gray-300">12h 45m</span>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h2 class="panel-title">Resources</h2>
                </div>
                <div class="space-y-3 text-sm">
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500">CPU</span>
                            <span class="text-gray-300">45%</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-2">
                            <div class="bg-[var(--color-warning)] h-2 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-500">Memory</span>
                            <span class="text-gray-300">62%</span>
                        </div>
                        <div class="w-full bg-white/5 rounded-full h-2">
                            <div class="bg-[var(--color-info)] h-2 rounded-full" style="width: 62%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
