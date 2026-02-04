<?php

use Livewire\Component;

new class extends Component
{
  public $description;
};
?>

<div>
  <h1 class="text-2xl font-bold mb-4">Crear Evento</h1>

  <div class="flex gap-2 w-full">
    <div class="grow">
    </div>
    <x-button
      label="Guardar Nombre"
      class="btn-primary shrink-0"
      wire:click="saveName"
      />
  </div>


</div>