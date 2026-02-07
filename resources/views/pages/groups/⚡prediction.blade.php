<?php

use App\Models\Group;
use Mary\Traits\Toast;
use Livewire\Component;

new class extends Component
{
  use Toast;

  public Group $group;
  public $props;
  public $headers;

  public function mount(Group $group)
  {
    $this->group = $group;
    if (!$group->published) {
      $this->error('El grupo no está publicado');
      return $this->redirectRoute('groups.show', ['group' => $group]);
    }

    $this->headers = [
      ['key' => 'description', 'label' => 'Descripción'],
    ];
    $this->props = $group->event->props()
      ->orderBy('id', 'asc')
      ->get();
  }
};
?>

<div>

  <x-table
    :headers="$headers"
    :rows="$props"
    >
    @scope('actions', $r)
      <livewire:prediction-row :prop="$r" :group="$this->group" :key="'prediction-row-'.$r->id" />
    @endscope
  </x-table>
</div>