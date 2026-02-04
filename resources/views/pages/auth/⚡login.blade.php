<?php

use App\Models\User;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new
#[Layout('layouts.empty')]
class extends Component
{
  use Toast;

  public $email;
  public $password;

  public function login() {
    $this->validate([
      'email' => 'required|email',
      'password' => 'required|string|min:8',
    ]);

    if (!auth()->attempt(['email' => $this->email, 'password' => $this->password], true)) {
      return $this->error('Credenciales inválidas.');
    }
    session()->regenerate();

    // Redirigir o mostrar un mensaje de éxito
    $this->success('Inicio de sesión exitoso.');
    return $this->redirectRoute('dashboard');
  }

};
?>

<div class="bg-base-100 md:rounded relative pt-16 pb-4 px-4 w-full md:max-w-3xl">
  <img src="/img/sblogo.svg" alt="SB Logo" class="absolute left-1/2 transform -translate-x-1/2 w-64 h-auto mx-auto mb-4 -top-32" />
  <x-form wire:submit='login'>
    <x-input label="Email" type="email" wire:model='email' class="outline-none!" inline placeholder="Email" />
    <x-input label="Contraseña" type="password" wire:model='password' class="outline-none!" inline placeholder="Contraseña" />
    <div class="flex gap-2 justify-between items-center">
      <x-button type="submit" class="btn-primary">Iniciar Sesión</x-button>
      <a href="{{ route('register') }}" class="text-sm text-base-content/70 hover:text-base-content transition">¿No tienes una cuenta? Regístrate</a>
    </div>
  </x-form>
</div>