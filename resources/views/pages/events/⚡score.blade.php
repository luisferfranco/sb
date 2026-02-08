<?php

use App\Models\Event;
use Livewire\Component;

new class extends Component
{
  public Event $event;
  public $headers;

  public function mount(Event $event): void
  {
    $this->event = $event;
    $this->headers = [
      ['key' => 'description', 'label' => 'Descripción'],
    ];
  }
};
?>

<div>
  <h1 class="text-2xl font-bold">Calificando</h1>
  <x-button
    link="{{ route('events.show', $event) }}"
    label="Volver al Evento"
    class="btn-ghost btn-sm mb-4"
    />


  <x-table
    :headers="$headers"
    :rows="$event->props"
    class="mb-4"
    >
    @scope('actions', $row)
      <div class="flex grow items-center gap-1">
        <livewire:props-score :prop="$row" :wire:key="'prop-score-'.$row->id" />
      </div>
    @endscope
  </x-table>
</div>