<?php

use App\Models\Prop;
use App\Models\Event;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Enums\EventStatus;

new class extends Component
{
  use Toast;

  public Event $event;
  public $props;
  public $propModal = false;
  public $description;
  public $opca = 'Over';
  public $opcb = 'Under';
  public $headers;
  public $published;
  public $isEditing = false;
  public $prop;

  public function mount(Event $event): void
  {
    $this->event = $event;
    if ($this->event->props()->count() === 0) {
      $this->event->status = EventStatus::INACTIVE;
      $this->event->save();
    }

    $this->published = $this->event->status === EventStatus::ACTIVE;

    $this->props = Prop::where('event_id', $event->id)
      ->orderBy('id', 'asc')
      ->get();
    $this->headers = [
      ['key' => 'description', 'label' => 'Descripción'],
      ['key' => 'opca', 'label' => 'Opción A'],
      ['key' => 'opcb', 'label' => 'Opción B'],
    ];
  }

  public function save(): void
  {
    $this->validate([
      'description' => 'required|string|max:255',
      'opca'        => 'required|string|max:255',
      'opcb'        => 'required|string|max:255',
    ]);

    $this->prop->description = $this->description;
    $this->prop->opca = $this->opca;
    $this->prop->opcb = $this->opcb;
    $this->prop->event_id = $this->event->id;
    $this->prop->save();

    if ($this->isEditing) {
      // actualizar en la colección
      $this->props = $this->props->map(function ($p) {
        return $p->id === $this->prop->id ? $this->prop : $p;
      });
      $this->success('Prop actualizado exitosamente');
      $this->isEditing = false;
      $this->propModal = false;
      return;
    }

    $this->props->push($this->prop);
    $this->reset(['description', 'opca', 'opcb', 'propModal']);
    $this->success('Prop creado exitosamente');
  }

  public function updatedPublished($value): void
  {
    $isActive = (bool) $value;

    info("Cambiando a [".($isActive ? 'true' : 'false')."]");

    // asegurar que tenemos un modelo Eloquent (tipado lo garantiza)
    $this->event->status = $isActive ? EventStatus::ACTIVE : EventStatus::INACTIVE;
    $this->event->save();

    // refrescar modelo y estado publicado para mantener la UI consistente
    $this->event->refresh();
    $this->published = $isActive;

    info($this->event);
  }

  public function delete(int $propId): void
  {
    $prop = Prop::find($propId);
    if (! $prop) {
      $this->error('Prop no encontrado');
      return;
    }

    $prop->delete();
    $this->props = $this->props->filter(fn ($p) => $p->id !== $propId);
    $this->success('Prop eliminado exitosamente');
  }

  public function editProp(int $propId): void
  {
    $prop = Prop::find($propId);
    if (! $prop) {
      $this->error('Prop no encontrado');
      return;
    }

    $this->prop = $prop;
    $this->description = $prop->description;
    $this->opca = $prop->opca;
    $this->opcb = $prop->opcb;
    $this->isEditing = true;
    $this->propModal = true;
  }

  public function createProp(): void
  {
    $this->reset(['description', 'opca', 'opcb', 'prop', 'isEditing']);
    $this->prop = new Prop();
    $this->propModal = true;
  }
};
?>

<div>
  <x-modal
    wire:model='propModal'
    class="backdrop-blur"
    >
    <x-card title="{{ $isEditing ? 'Editar Prop' : 'Nuevo Prop' }}">
      <x-form wire:submit='save'>
        <x-input
          wire:model='description'
          label="Descripción"
          class="w-full outline-none!"
          placeholder="Descripción"
          inline
          required
          />

        <div class="flex justify-between">
          <x-input
            wire:model='opca'
            label="Opción A"
            class="w-full outline-none!"
            placeholder="Opción A"
            inline
            required
            />
          <x-input
            wire:model='opcb'
            label="Opción B"
            class="w-full outline-none!"
            placeholder="Opción B"
            inline
            required
            />
        </div>

        <x-button
          type="submit"
          label="{{ $isEditing ? 'Guardar Cambios' : 'Crear Prop' }}"
          icon="s-plus-circle"
          class="btn-primary mt-4"
          />
      </x-form>
    </x-card>
  </x-modal>

  <div class="py-1 px-2 bg-base-100 rounded mb-6 shadow-md">
    <h1 class="text-2xl font-bold mb-4">{{ $event->name }}</h1>
    @if ($event->description)
      <p>{{ $event->description }}</p>
    @endif
    <x-toggle wire:model.live='published' label="Evento Publicado" />
    <p class="text-base-content/50 text-xs"><span class="font-bold">Nota:</span>Los eventos que están publicados, o que ya tienen pronósticos, no pueden ser modificados</p>
  </div>

  <div class="border-b border-gray-500 my-2"></div>

  @if (!$published)
    <x-button
      label="Nuevo Prop"
      icon="s-plus-circle"
      class="btn-primary"
      wire:click="createProp"
      />
  @endif

  <div class="mt-4">Props del evento</div>
  <x-table
    :rows="$props"
    :headers="$headers"
    hover
    >
    @scope('actions', $r)
      @if (!$this->published)
        <div class="flex gap-1 justify-end">
          <x-button
            icon="s-pencil"
            class="btn-primary btn-ghost btn-xs"
            wire:click='editProp({{ $r->id }})'
            />
          <x-button
            icon="s-trash"
            class="btn-error btn-ghost btn-xs"
            wire:confirm="¿Estás seguro de que deseas eliminar este prop?"
            wire:click="delete({{ $r->id }})"
            />
        </div>
      @endif
    @endscope
  </x-table>
</div>