<?php

use App\Models\Prop;
use Livewire\Component;
use App\Models\Prediction;

new class extends Component
{
  public Prop $prop;

  public function mount(Prop $prop): void
  {
    $this->prop = $prop;
  }

  public function propScore($option): void
  {
    $this->prop->option = $option;
    $this->prop->save();

    Prediction::where('prop_id', $this->prop->id)
      ->update(['points' => 0]);
    Prediction::where('prop_id', $this->prop->id)
      ->where('option', $option)
      ->update(['points' => 1]);
  }
};
?>

<div class="flex grow items-center gap-1">
  <x-button
    class="w-18 {{ $prop->option == $prop->opca ? 'btn-error' : 'btn-ghost btn-info'}}"
    wire:click="propScore('{{ $prop->opca }}')"
    label="{{ $prop->opca }}"
    />
  <x-button
    class="w-18 {{ $prop->option == $prop->opcb ? 'btn-error' : 'btn-ghost btn-info'}}"
    wire:click="propScore('{{ $prop->opcb }}')"
    label="{{ $prop->opcb }}"
    />
</div>