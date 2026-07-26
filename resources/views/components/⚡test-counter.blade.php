<?php

use Livewire\Component;

new class extends Component
{
    public int $count = 0;

    public function increment(): void
    {
        $this->count++;
    }
};
?>

<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title">Test Livewire</h3>
    </div>
    <div class="card-body text-center">
        <p class="mb-3">Compteur (mis à jour sans rechargement de page) :</p>
        <h1 class="display-4">{{ $count }}</h1>
        <button type="button" class="btn btn-primary mt-3" wire:click="increment">
            Incrémenter
        </button>
    </div>
</div>