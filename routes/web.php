<?php

Route::livewire('/register', 'pages::auth.register')->name('register');
Route::livewire('/login', 'pages::auth.login')->name('login');
Route::livewire('/dashboard', 'pages::dashboard')->name('dashboard');

Route::livewire('/', 'pages::users.index');

