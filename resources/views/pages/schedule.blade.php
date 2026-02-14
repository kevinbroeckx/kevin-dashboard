@extends('layouts.sidebar')

@section('content')
    <div class="max-w-5xl mx-auto">
        <h1 class="text-3xl font-bold text-white mb-8">Job Schedule</h1>
        
        @livewire('schedule-panel')
    </div>
@endsection
