<?php

use App\Models\Event;
use App\Models\Group;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Enums\EventType;
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

  public function mount(Group $group): void
  {
    $this->updateData();
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
    $this->event_id   = $group->event_id;
    $this->eventCount = Event::where('group_id', $group->id)->count();
    info("Event ID: " . $this->eventCount);
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

  public function createEvent(): void
  {
    $event = Event::create([
      'name'     => 'Evento del Grupo: ' . $this->group->name,
      'type'     => \App\Enums\EventType::LOCAL,
      'owner_id' => auth()->id(),
      'group_id' => $this->group->id,
      'status'   => \App\Enums\EventStatus::INACTIVE,
    ]);

    $this->group->event_id = $event->id;
    $this->group->save();

    $this->event_id = $event->id;
    $this->eventCount = Event::where('group_id', $this->group->id)->count();

    $this->success('Evento creado exitosamente. Ahora puedes agregar props al evento desde la sección de eventos.');

    $this->redirectRoute('events.show', ['event' => $event->id]);
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
};
?>

<div>
  <h1 class="text-xl">{{ $group->name }}</h1>

  @if (!$member)
    <x-button
      wire:click="join"
      class="btn-primary"
      label="Unirse"
      />
  @else
    @if ($group->owner_id === $member->id)
      <p class="text-sm text-base-content/70">Eres el propietario del grupo.</p>
    @else
      <div class="flex flex-col gap-2">
        <x-badge
          value="{{ $member->pivot->status->label() }}"
          class="badge-{{ $member->pivot->status->color() }}"
        />
        <p><x-button
            wire:click="leave"
            class="btn-error btn-sm"
            label="Abandonar el Grupo"
            /></p>
      </div>
    @endif
  @endif

  <x-card>
    <x-toggle
      wire:model.live="published"
      label="Grupo Publicado"
      class="my-4"
      />
    <p class="text-info"></p><span class="font-bold">Nota:</span> Los grupos publicados no pueden modificar el evento que se seleccionó. Si el grupo tiene participantes aprobados, no se puede modificar el evento que se utilizará. Cerciorate de haber seleccionado el evento correcto antes de publicar el grupo.</p>
  </x-card>


  @if ($published)
    @if ($member)
      <x-button
        label="Pronosticar"
        class="btn-primary my-6"
        link="{{ route('groups.prediction', ['group' => $group]) }}"
        />
    @else
      <x-button
        label="Visualizar los Props del Evento"
        class="btn-secondary my-6"
        link="{{ route('events.show', ['event' => $group->event_id]) }}"
        />
    @endif
  @else
    <x-card class="my-6">
      <div class="space-y-2">
        <p>Selecciona un evento de la lista de eventos globales, o bien presiona el botón para crear o editar tu propio evento y tus props</p>
        <div class="flex justify-between items-center">
          <div class="w-full">
            <x-select
              wire:model.live="event_id"
              :options="$events"
              option-value="id"
              option-label="name"
              placeholder="Selecciona un evento"
              class="w-full max-w-xs outline-none!"
              />
          </div>

          @if (!$event_id && $eventCount==0)
            <x-button
              label="Crear Evento"
              icon="s-plus-circle"
              class="btn-primary"
              wire:click='createEvent'
              />
          @else
            @if ($event_id && !$global)
              <x-button
                label="Editar Evento"
                icon="s-pencil"
                class="btn-secondary"
                link="{{ route('events.show', ['event' => $group->event_id]) }}"
                />
            @endif
          @endif
        </div>
      </div>
    </x-card>
  @endif


  <div class="mt-4">Leaderboard del grupo</div>

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

</div>