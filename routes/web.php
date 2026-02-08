<?php

Route::livewire('/register', 'pages::auth.register')->name('register');
Route::livewire('/login', 'pages::auth.login')->name('login');

Route::livewire('/', 'pages::auth.login')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');

    Route::livewire('/groups/{group}', 'pages::groups.show')
      ->name('groups.show');

    Route::livewire('/tickets/{ticket}/prediction', 'pages::tickets.prediction')
      ->name('tickets.prediction');

    Route::livewire('/events', 'pages::events.index')->name('events.index');
    Route::livewire('/events/{event}', 'pages::events.show')->name('events.show');
    Route::livewire('/events/{event}/score', 'pages::events.score')
      ->name('events.score');

});
