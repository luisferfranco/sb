<?php

use App\Models\Group;
use App\Models\Ticket;
use Livewire\Component;

new class extends Component
{
  public Group $group;
  public $headers;
  public $table;

  public function mount(Group $group): void
  {
    $this->group = $group;
    $this->headers = [
      ['key' => 'name', 'label' => 'Nombre'],
      ['key' => 'points', 'label' => 'Puntos', 'class'=>"w-10 text-right"],
    ];
    $this->getData();
  }

  public function getData() {
    // Get tickets for this group, eager-load owner (user) and sum prediction points
    $this->table = Ticket::query()
      ->with('user')
      ->withSum('predictions as points', 'points')
      ->where('group_id', $this->group->id)
      ->orderByDesc('points')
      ->get();
  }
};
?>

<div wire:poll.5000ms="getData">
  <x-table
    :headers="$headers"
    :rows="$table"
    class="mb-4"
    >
    @scope('cell_name', $row)
      <x-button
        label="{{ $row->name }}"
        class="btn-ghost btn-sm"
        link="{{ route('tickets.prediction', ['ticket' => $row]) }}"
        />
    @endscope

    @scope('cell_points', $row)
      <div class="text-right">
        {{ $row->points ?? 0}}
      </div>
    @endscope
  </x-table>
</div>