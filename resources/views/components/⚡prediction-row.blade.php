<?php

use App\Models\Prop;
use App\Models\Group;
use Livewire\Component;

new class extends Component
{
  public Prop $prop;
  public Group $group;
  public $opcion;

  public function mount(Prop $prop, Group $group) {
    $this->prop   = $prop;
    $this->group  = $group;
    $this->opcion = $group->predictions()
      ->where('prop_id', $prop->id)
      ->where('user_id', auth()->id())
      ->first()?->option ?? null;
  }


  public function save($opcion)
  {
    $this->opcion = $opcion;
    $prediction = $this->group->predictions()
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
    class="{{ $opcion == $prop->opca ? 'btn-error' : 'btn-soft' }}"
    spinner
    />
  <x-button
    wire:click="save('{{ $prop->opcb }}')"
    label="{{ $prop->opcb }}"
    class="{{ $opcion == $prop->opcb ? 'btn-error' : 'btn-soft' }}"
    spinner
    />
</div>