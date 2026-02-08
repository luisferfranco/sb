<?php

use App\Models\Group;
use App\Models\Ticket;
use Mary\Traits\Toast;
use Livewire\Component;

new class extends Component
{
  use Toast;

  public Ticket $ticket;
  public $props;
  public $headers;

  public function mount(Ticket $ticket)
  {
    $this->ticket = $ticket;
    if (!$ticket->group->published) {
      $this->error('El grupo no está publicado');
      return $this->redirectRoute('groups.show', ['group' => $ticket->group]);
    }

    $this->headers = [
      ['key' => 'description', 'label' => 'Descripción'],
    ];
    $this->props = $ticket->props()
      ->orderBy('id', 'asc')
      ->get();
  }
};
?>

<div>
  <h1 class="text-3xl mb-4">{{ $ticket->name }}</h1>
  <x-button
    label="Volver al Grupo"
    class="btn-secondary mb-4"
    link="{{ route('groups.show', ['group' => $ticket->group]) }}"
    />

  <x-table
    :headers="$headers"
    :rows="$props"
    >
    @scope('actions', $prop)
      <livewire:prediction-row
        :prop="$prop"
        :ticket="$this->ticket"
        :key="'prediction-row-'.$prop->id"
        />
    @endscope
  </x-table>
</div>