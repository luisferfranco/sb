<?php

use Livewire\Component;

new class extends Component
{
    public function logout() {
        auth()->logout();
        return redirect()->route('login');
    }
};
?>

<div>
  <x-button
    wire:click="logout"
    label="Cerrar Sesión"
    class="btn-ghost btn-sm btn-accent"
    />
</div>