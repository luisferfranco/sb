<?php

Route::livewire('/register', 'pages::auth.register')->name('register');
Route::livewire('/login', 'pages::auth.login')->name('login');

Route::livewire('/', 'pages::users.index');

Route::middleware(['auth'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');
    Route::livewire('/groups/{group}', 'pages::groups.show')->name('groups.show');
    Route::livewire('/groups/{group}/events/', 'pages::events.index')->name('groups.events.index');

    Route::livewire('/events', 'pages::events.index')->name('events.index');
    Route::livewire('/events/{event}', 'pages::events.show')->name('events.show');
});
