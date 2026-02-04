<?php

use App\Models\Group;
use Mary\Traits\Toast;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Enums\GroupMemberStatus;

new class extends Component
{
  use Toast;

  public $groups;
  public $openModal = false;
  public $name;
  public $description;
  public $headers;

  public function mount() {
    $this->groups = Group::orderBy('id', 'asc')->get();
    $this->headers = [
      ['key' => 'id', 'label' => 'Grupo']
    ];
  }

  public function createGroup() {
    $this->validate([
      'name'        => 'required|string|max:255',
      'description' => 'nullable|string|max:1000',
    ]);

    $group = Group::create([
      'name'        => $this->name,
      'description' => $this->description,
      'owner_id'    => auth()->id(),
    ]);
    $group->members()->attach(auth()->id(), ['status' => GroupMemberStatus::approved->value]);

    $this->groups->push($group);
    $this->reset(['name', 'description', 'openModal']);
    $this->success('Grupo creado exitosamente.');
  }
};
?>

<div>
  <x-modal wire:model="openModal" class="backdrop-blur">
    <x-card title="Crear Nuevo Grupo">
      <form wire:submit.prevent="createGroup" class="space-y-4">
        <x-input
          wire:model="name"
          required
          autofocus
          label="Nombre del Grupo"
          inline
          class="w-full outline-none!"
          />
        <x-textarea
          wire:model="description"
          label="Descripción del Grupo"
          inline
          class="w-full outline-none!"
          placeholder="Descripción del grupo (opcional)"
          />
        <div class="flex justify-end">
          <x-button type="submit" label="Crear Grupo" class="btn-primary" />
        </div>
      </form>
    </x-card>
  </x-modal>


  <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

  <div class="flex justify-between items-center">
    <x-button
      label="Nuevo Grupo"
      class="btn-success"
      @click="$wire.openModal=true"
      />
    <x-button
      label="Eventos"
      class="btn-secondary"
      link="{{ route('events.index') }}"
      />
  </div>

  <x-table
    :headers="$headers"
    :rows="$groups"
    striped
    class="mt-4"
    >
    @scope('cell_id', $r)
      <p class="font-bold">{{ $r->name }}</p>
      <p class="text-sm text-base-content/50">Inscritos {{ $r->members->count() }}</p>
      <p class="text-sm text-base-content/50">{{ $r->description }}</p>
      <p class="text-sm text-base-content/50">Propietario: {{ $r->owner->name }}</p>
    @endscope

    @scope('actions', $r)
      <a
        href="{{ route('groups.show', $r) }}"
        class="btn btn-secondary btn-sm whitespace-nowrap"
        />Ver Grupo</a>
    @endscope
  </x-table>
</div>