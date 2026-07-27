<?php

namespace App\Livewire\Audit;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;

#[Layout('layouts.admin', ['title' => "Journal d'audit"])]
class Manager extends Component
{
    use WithPagination;

    public string $logName = '';

    public array $logNames = [
        'contrat' => 'Contrats',
        'facture' => 'Factures',
        'paiement' => 'Paiements',
    ];

    public function mount(): void
    {
        $this->authorize('audit.view');
    }

    public function updatingLogName(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.audit.manager', [
            'activites' => Activity::query()
                ->with('causer')
                ->when($this->logName, fn ($query) => $query->where('log_name', $this->logName))
                ->orderByDesc('id')
                ->paginate(20),
        ]);
    }
}
