<?php

use App\Models\Group;
use Mary\Traits\Toast;
use Livewire\Component;

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
      ->syncWithoutDetaching([$user->id => ['status' => 'pending']]);

    // Recargamos datos
    $this->updateData();
    $this->success('Solicitud de unión enviada');
  }

  public function approve(int $userId): void
  {
    $this->group->members()
      ->updateExistingPivot($userId, ['status' => 'approved']);

    // Recargamos datos
    $this->updateData();
    $this->success('Solicitud aprobada');
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
    <span class="inline-flex items-center px-2 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
      {{ ucfirst($member->pivot->status ?? 'miembro') }}
    </span>
    <x-button
      wire:click="leave"
      class="btn-error"
      label="Salir"
      />
  @endif

  <div class="mt-4">Leaderboard del grupo</div>

  <x-table
    :headers="$headers"
    :rows="$group->members"
    >
    @scope('cell_user', $r)
      {{ $r->name }}
    @endscope

    @scope('actions', $r)
      @can('approve', $this->group)
        @if ($r->pivot->status === 'pending')
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