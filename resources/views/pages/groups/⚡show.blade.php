<?php

use App\Models\Group;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Enums\GroupMemberStatus;

new class extends Component
{
  use Toast;

  public Group $group;
  public $member;
  public $headers;


  public function mount(Group $group): void
  {
    $this->updateData();
    $this->headers = [
      ['key' => 'user', 'label' => 'Miembro'],
      ['key' => 'pivot.status', 'label' => 'Estado'],
    ];
  }

  public function updateData(): void
  {
    $this->group->load('members');
    $this->member = $this->group->members->firstWhere('id', auth()->id());
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
};
?>

<div>
  @if (session()->has('success'))
    <p class="text-sm text-green-600">{{ session('success') }}</p>
  @endif

  <h1 class="text-xl">{{ $group->name }}</h1>

  @if (!$member)
    <x-button
      wire:click="join"
      class="btn-primary"
      label="Unirse"
      />
  @else
    <div class="flex flex-col gap-2">
      <x-badge
        value="{{ $member->pivot->status->label() }}"
        class="badge-{{ $member->pivot->status->color() }}"
      />
    <p>
      <x-button
        wire:click="leave"
        class="btn-error btn-sm"
        label="Salir"
        />
    </p>
    </div>
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
        @if ($r->pivot->status === GroupMemberStatus::PENDING)
          <div class="flex gap-2">
            <x-button
              wire:click="approve({{ $r->id }})"
              class="btn-success btn-sm"
              icon="s-check-circle"
              />
            <x-button
              wire:click="reject({{ $r->id }})"
              class="btn-error btn-sm"
              icon="s-x-circle"
              />
          </div>
        @endif
      @endcan
    @endscope
  </x-table>

</div>