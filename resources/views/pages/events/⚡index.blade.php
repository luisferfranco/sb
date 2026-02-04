<?php

use App\Models\Event;
use Livewire\Component;

new class extends Component
{
  public $events;
  public $createEventModal = false;
  public $name;
  public $type=false;
  public $description;

  public function mount(): void
  {
    $this->events = Event::orderBy('id', 'asc')->get();
  }

  public function save(): void
  {
    $this->validate([
      'name'        => 'required|string|max:255',
      'description' => 'nullable|string|max:1000',
    ]);

    $event = Event::create([
      'name'        => $this->name,
      'description' => $this->description,
      'type'        => $this->type ? 'global' : 'local',
      'owner_id'    => auth()->id(),
    ]);

    $this->events->push($event);
    $this->reset(['name', 'description', 'type', 'createEventModal']);
  }
};
?>

<div>
  <x-modal wire:model='createEventModal' class="backdrop-blur">
    <x-card title="Crear Nuevo Evento">
      <x-form wire:submit='save'>
        @can('createGlobal', App\Models\Event::class)
          <x-toggle
            wire:model='type'
            label="Evento Global"
            />
        @endcan
        <x-input
          label="Nombre del Evento"
          class="outline-none!"
          wire:model='name'
          placeholder="Nombre del Evento"
          inline
          />
        <x-textarea
          label="Descripción del Evento"
          class="outline-none!"
          rows="5"
          wire:model='description'
          placeholder="Descripción del Evento (opcional)"
          inline
          />
        <div class="flex justify-end">
          <x-button
            type="submit"
            label="Crear Evento"
            class="btn-primary"
            />
        </div>
      </x-form>
    </x-card>
  </x-modal>

  <h1 class="text-2xl font-bold mb-4">Eventos</h1>

  <x-button
    label="Crear Evento"
    icon="s-plus-circle"
    class="btn-primary mb-4"
    wire:click="$set('createEventModal', true)"
    />

  <ul>
    @foreach($events as $event)
      <li class="mb-2">
        <a href="{{ route('events.show', $event) }}" class="text-blue-600 hover:underline">
          {{ $event->name }}
        </a>
      </li>
    @endforeach
  </ul>
</div>