<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
  </head>
  <body class="bg-base-200 min-h-screen">
    <div class="relative">
      <div class="absolute top-16 left-1/2 transform -translate-x-1/2 -translate-y-1/2">
        <img src="/img/sblogo.svg" alt="SB Logo" class="h-24 opacity-50">
      </div>
    </div>
    <div class="pt-36 px-4">
      {{ $slot }}
    </div>

    @livewireScripts
    <x-toast />
  </body>
</html>
