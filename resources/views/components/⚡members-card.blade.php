<?php

use App\Models\Group;
use Mary\Traits\Toast;
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component
{
  use Toast;

  public Group $group;
  public $tickets;
  public $headers;

  public function mount(Group $group)
  {
    $this->group    = $group;
    $this->tickets  = $group->tickets()
      ->where('user_id', auth()->id())
      ->with('user')
      ->get();
    $this->headers = [
      ['key' => 'id',   'label' => 'ID', 'class' => 'w-1'],
      ['key' => 'name', 'label' => 'Nombre del Boleto'],
    ];
  }

  #[On('accepting-updated')]
  public function handleAcceptingUpdated($accepting)
  {
    $this->group->refresh();
  }

  public function buy()
  {
    // Checar si el grupo está en estado accepting
    // Recargar el grupo en el momento del intento de compra
    $this->group->refresh();
    if (!$this->group->accepting) {
      $this->error('Las inscripciones para este grupo están cerradas');
      return;
    }

    $ticket = $this->group
      ->tickets()
      ->create([
        'user_id' => auth()->id(),
        'name'    => auth()->user()->name . ' - ' . ($this->tickets->count() + 1),
      ]);

    $this->tickets->push($ticket);
    $this->success('Boleto comprado exitosamente.');
  }

  public function deleteTicket($ticketId)
  {
    $ticket = $this->group->tickets()->where('id', $ticketId)->first();

    if ($ticket) {
      $ticket->delete();
      $this->tickets = $this->tickets->reject(fn($t) => $t->id === $ticketId);
      $this->success('Boleto eliminado exitosamente.');
    } else {
      $this->error('Boleto no encontrado o no tienes permiso para eliminarlo.');
    }
  }

};
?>

<x-card>
  @if ($group->accepting)
    <x-button
      label="Comprar Boleto"
      class="btn-primary"
      wire:click='buy'
      />
    <p class="text-xs text-base-content/50 mt-1">
      Se pueden comprar boletos mientras el grupo no haya cerrado inscripciones. Los boletos que hayas comprado se pueden borrar siempre y cuando el grupo no haya cerrado inscripciones.
    </p>
  @else
    <x-alert
      class="alert-warning alert-soft"
      icon="s-exclamation-triangle"
      title="Inscripciones Cerradas"
      message="Este grupo ha cerrado las inscripciones. No se pueden comprar nuevos boletos ni eliminar los existentes."
      />
  @endif

  @if ($tickets->isEmpty())
    <p class="text-sm text-base-content/50 mt-4">Aún no has comprado ningún boleto para este grupo.</p>
  @else
    <x-table
      :headers="$headers"
      :rows="$tickets"
      >
      @scope('actions', $r)
        @if ($this->group->accepting)
          <x-button
            icon="s-trash"
            class="btn-error btn-sm"
            wire:confirm="¿Estás seguro de que deseas eliminar este boleto?"
            wire:click='deleteTicket({{ $r->id }})'
            />
        @else
          <x-button
            icon="s-clipboard-document-check"
            class="btn-success btn-sm"
            />
        @endif
      @endscope
    </x-table>
  @endif
</x-card>