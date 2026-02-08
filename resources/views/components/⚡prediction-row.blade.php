<?php

use App\Models\Prop;
use App\Models\Ticket;
use Mary\Traits\Toast;
use Livewire\Component;

new class extends Component
{
  use Toast;

  public Prop $prop;
  public Ticket $ticket;
  public $opcion;
  public $points;

  public function mount(Prop $prop, Ticket $ticket) {
    $this->prop   = $prop;
    $this->ticket = $ticket;
    $this->opcion = $ticket->predictions()
      ->where('prop_id', $prop->id)
      ->where('ticket_id', $ticket->id)
      ->first()?->option ?? null;
    $this->points = $ticket->predictions()
      ->where('prop_id', $prop->id)
      ->where('ticket_id', $ticket->id)
      ->first()?->points ?? null;
  }

  public function save($opcion)
  {
    if ($this->ticket->group->accepting === false) {
      $this->error('El grupo no está aceptando predicciones');
      return;
    }


    $this->opcion = $opcion;
    $prediction = $this->ticket->predictions()
      ->updateOrCreate(
        ['prop_id' => $this->prop->id, 'user_id' => auth()->id()],
        ['option' => $opcion]
      );
  }
};
?>

<div class="flex justify-end items-center grow gap-1">
  <x-button
    wire:click="save('{{ $prop->opca }}')"
    label="{{ $prop->opca }}"
    class="w-16 {{ $opcion == $prop->opca ? 'btn-error' : 'btn-soft' }}"
    spinner
    />
  <x-button
    wire:click="save('{{ $prop->opcb }}')"
    label="{{ $prop->opcb }}"
    class="w-16 {{ $opcion == $prop->opcb ? 'btn-error' : 'btn-soft' }}"
    spinner
    />
  @if ($points === null)
    <x-icon name="s-question-mark-circle" class="w-8 h-8 text-base-content/50" />
  @elseif ($points > 0)
    <x-icon name="s-check" class="w-8 h-8 text-success" />
  @else
    <x-icon name="s-x-circle" class="w-8 h-8 text-error" />
  @endif
</div>