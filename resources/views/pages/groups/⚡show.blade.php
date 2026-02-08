<?php

use App\Models\Event;
use App\Models\Group;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Enums\EventType;
use Livewire\Attributes\On;
use App\Enums\GroupMemberStatus;

new class extends Component
{
  use Toast;

  public Group $group;
  public $member;
  public $headers;
  public $events;
  public $event_id;
  public $published;
  public $global=false;
  public $eventCount=0;
  public $accepting = true;
  public $section = 'home';

  public function mount(Group $group): void
  {
    $this->updateData();
    $this->group = $group;
    $this->headers = [
      ['key' => 'user', 'label' => 'Miembro'],
      ['key' => 'pivot.status', 'label' => 'Estado'],
      ['key' => 'puntos', 'label' => 'Puntos'],
    ];
    // Eventos Globales
    $this->events = Event::where('group_id', null)
      ->orWhere('owner_id', auth()->id())
      ->orderBy('name', 'asc')
      ->get();
    $this->published  = $group->published;
    $this->accepting  = $group->accepting;
    $this->event_id   = $group->event_id;
    $this->eventCount = Event::where('group_id', $group->id)->count();
  }

  public function updateData(): void
  {
    $this->group->load('members');
    $this->member = $this->group->members->firstWhere('id', auth()->id());
    $this->eventCount = Event::where('group_id', $this->group->id)->count();
  }

  public function join(): void
  {
    $user = auth()->user();

    // create a join request (pending by default)
    $this->group->members()
      ->syncWithoutDetaching([$user->id => ['status' => GroupMemberStatus::PENDING->value]]);

    // Recargamos datos
    $this->updateData();
    $this->success('Solicitud de unión enviada');
  }

  public function approve(int $userId): void
  {
    $this->group->members()
      ->updateExistingPivot($userId, ['status' => GroupMemberStatus::APPROVED->value]);

    // Recargamos datos
    $this->updateData();
    $this->success('Solicitud aprobada');
  }

  public function reject(int $userId): void
  {
    $this->group->members()
      ->updateExistingPivot($userId, ['status' => GroupMemberStatus::REJECTED->value]);

    $this->updateData();
    $this->success('Solicitud rechazada');
  }

  public function leave(): void
  {
    $user = auth()->user();
    if (! $user) {
      return;
    }

    $this->group->members()->detach($user->id);

    $this->updateData();
    $this->success('Has abandonado el grupo');
  }

  public function updatedPublished($value): void
  {
    $this->group->published = $value;
    $this->group->save();

    $this->success('Estado de publicación del grupo actualizado');
  }

  public function updatedEventId($value): void
  {
    $value = $value === "" ? null : $value;
    $this->group->event_id = $value;
    $this->group->save();
    $this->global = $this->group->event?->type === EventType::GLOBAL;

    $this->success('Evento del grupo actualizado');
  }

  public function updatedAccepting($value): void
  {
    $this->group->accepting = $value;
    $this->group->save();

    $message = $value ? 'El grupo ahora está aceptando inscripciones' : 'El grupo ha cerrado las inscripciones';

    $this->dispatch('accepting-updated', ['accepting' => $value]);
    $this->success($message);
  }
};
?>

<div>
  <h1 class="text-xl">{{ $group->name }}</h1>

  @if (!$member)
    <div class="flex gap-1 items-center">
      <x-button
        wire:click="join"
        class="btn-primary"
        label="Solicitud de Ingreso"
        />
      <x-button
        label="Visualizar los Props"
        class="btn-secondary my-6 btn-ghost"
        link="{{ route('events.show', ['event' => $group->event_id]) }}"
        />
    </div>
  @else
    @if ($group->owner_id === $member->id)
      <p class="text-sm text-base-content/70">Eres el propietario del grupo.</p>
    @else
      <div class="flex flex-col gap-2">
        <x-badge
          value="{{ $member->pivot->status->label() }}"
          class="badge-{{ $member->pivot->status->color() }}"
        />
        @if ($accepting)
          <x-button
            wire:click="leave"
            class="btn-error btn-sm"
            label="Abandonar el Grupo"
            />
        @endif
      </div>
    @endif
  @endif

  {{--
  Selecciona el evento que se usará
  Abre y cierra las inscripciones al grupo
  --}}
  @can('manage', $group)
    <x-card>
      <div class="space-y-2">
        @if ($published)
          <x-toggle
            wire:model.live='accepting'
            label='Aceptando Inscripciones'
            hint="Cerrar las inscripciones e iniciar el juego"
            />
        @else
          <p>Selecciona un evento de la lista de eventos globales.</p>
          <p>Cuando estés listo, publica el grupo para que puedan iniciar las inscripciones.</p>
          <x-select
            wire:model.live="event_id"
            :options="$events"
            option-value="id"
            option-label="name"
            placeholder="Selecciona un evento"
            class="w-full max-w-xs outline-none!"
            />
          <x-toggle
            wire:model.live="published"
            label="Grupo Publicado"
            />
        @endif
      </div>
    </x-card>
  @endcan

  @if ($published && $member)
    <div class="mt-6">
      <livewire:members-card :group="$group" class="mt-6" />
    </div>
  @endif

  @can('manage', $group)
    <div class="flex gap-1 items-center my-4">
      <x-button
        label="Home"
        class="{{ $section == 'home' ? 'btn-warning' : 'btn-info btn-ghost' }}"
        wire:click="$set('section', 'home')"
        />
      <x-button
        label="Miembros"
        class="{{ $section == 'members' ? 'btn-warning' : 'btn-info btn-ghost' }}"
        wire:click="$set('section', 'members')"
        />
      @if (!$accepting)
        <x-button
          label="Califica"
          class="btn-ghost btn-info"
          link="{{ route('events.score', ['event' => $group->event]) }}"
          />
      @endif
    </div>
  @endcan

  @if ($section == 'home')
    <livewire:leaderboard :group="$group" />
  @elseif ($section == 'members')
    <x-table
      :headers="$headers"
      :rows="$group->members"
      >
      @scope('cell_user', $r)
        <p>{{ $r->name }}</p>
        <p class="text-sm text-base-content/50">{{ $r->email }}</p>
      @endscope

      @scope('cell_pivot.status', $r)
        <x-badge
          value="{{ $r->pivot->status->label() }}"
          class="badge-{{ $r->pivot->status->color() }}"
        />
      @endscope

      @scope('actions', $r)
        @can('approve', $this->group)
          <div class="flex gap-2">
            @if ($r->pivot->status === GroupMemberStatus::PENDING)
              <x-button
                wire:click="approve({{ $r->id }})"
                class="btn-success btn-sm"
                icon="s-check-circle"
                />
            @endif
            @if ($r->id !== $this->group->owner_id)
              <x-button
                wire:click="reject({{ $r->id }})"
                wire:confirm="¿Estás seguro de que deseas rechazar a este usuario? - Si existe, se borrarán todos sus datos"
                class="btn-error btn-sm"
                icon="s-x-circle"
                />
            @endif
          </div>
        @endcan
      @endscope
    </x-table>
  @endif

</div>