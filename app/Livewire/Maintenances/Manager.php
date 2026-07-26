<?php

namespace App\Livewire\Maintenances;

use App\Models\Engin;
use App\Models\Maintenance;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Maintenance des engins'])]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $maintenanceId = null;

    public ?int $engin_id = null;

    public string $type = 'panne';

    public string $date_debut = '';

    public string $date_fin = '';

    public string $cout = '';

    public string $description = '';

    public string $statut = 'planifiee';

    public array $types = [
        'panne' => 'Panne',
        'entretien_preventif' => 'Entretien préventif',
    ];

    public array $statuts = [
        'planifiee' => 'Planifiée',
        'en_cours' => 'En cours',
        'terminee' => 'Terminée',
    ];

    protected function rules(): array
    {
        return [
            'engin_id' => 'required|exists:engins,id',
            'type' => 'required|in:'.implode(',', array_keys($this->types)),
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'cout' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'statut' => 'required|in:'.implode(',', array_keys($this->statuts)),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('maintenances.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('maintenances.update');

        $maintenance = Maintenance::findOrFail($id);
        $this->maintenanceId = $maintenance->id;
        $this->engin_id = $maintenance->engin_id;
        $this->type = $maintenance->type;
        $this->date_debut = $maintenance->date_debut?->format('Y-m-d') ?? '';
        $this->date_fin = $maintenance->date_fin?->format('Y-m-d') ?? '';
        $this->cout = (string) $maintenance->cout;
        $this->description = (string) $maintenance->description;
        $this->statut = $maintenance->statut;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->maintenanceId ? 'maintenances.update' : 'maintenances.create');

        $data = $this->validate();
        $data['cout'] = $data['cout'] !== '' ? $data['cout'] : null;
        $data['date_fin'] = $this->date_fin ?: null;

        Maintenance::updateOrCreate(['id' => $this->maintenanceId], $data);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('maintenances.delete');

        Maintenance::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'maintenanceId', 'engin_id', 'date_debut', 'date_fin', 'cout', 'description',
        ]);
        $this->type = 'panne';
        $this->statut = 'planifiee';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('maintenances.view');

        return view('livewire.maintenances.manager', [
            'maintenances' => Maintenance::query()
                ->with('engin')
                ->when($this->search, fn ($query) => $query
                    ->whereHas('engin', fn ($q) => $q->where('designation', 'like', "%{$this->search}%")))
                ->orderByDesc('date_debut')
                ->paginate(10),
            'enginsOptions' => Engin::orderBy('designation')->get(['id', 'designation']),
        ]);
    }
}
