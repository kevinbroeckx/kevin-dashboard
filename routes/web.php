<?php

use Illuminate\Support\Facades\Route;

// Dashboard pages
Route::get('/', function () {
    return view('pages.overview');
})->name('overview');

Route::get('/status', function () {
    return view('pages.status');
})->name('status');

Route::get('/schedule', function () {
    return view('pages.schedule');
})->name('schedule');

Route::get('/sessions', function () {
    return view('pages.sessions');
})->name('sessions');

Route::get('/memory', function () {
    return view('pages.memory');
})->name('memory');

Route::get('/kanban', function () {
    return view('pages.kanban');
})->name('kanban');

Route::get('/quick-actions', function () {
    return view('pages.quick-actions');
})->name('quick-actions');
