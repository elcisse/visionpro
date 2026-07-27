<?php

namespace App\Livewire\Charges;

use App\Models\Charge;
use App\Models\Engin;
use App\Models\Maintenance;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Charges des engins'])]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $chargeId = null;

    public ?int $engin_id = null;

    public ?int $maintenance_id = null;

    public string $type = 'carburant';

    public string $date = '';

    public string $montant = '';

    public string $description = '';

    public array $types = [
        'carburant' => 'Carburant',
        'reparation' => 'Réparation',
        'entretien' => 'Entretien',
        'salaire_chauffeur' => 'Salaire chauffeur',
        'autre' => 'Autre',
    ];

    protected function rules(): array
    {
        return [
            'engin_id' => 'required|exists:engins,id',
            'maintenance_id' => 'nullable|exists:maintenances,id',
            'type' => 'required|in:'.implode(',', array_keys($this->types)),
            'date' => 'required|date',
            'montant' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEnginId(): void
    {
        $this->maintenance_id = null;
    }

    public function updatedMaintenanceId($value): void
    {
        if (! $value) {
            return;
        }

        $maintenance = Maintenance::find($value);

        if (! $maintenance) {
            return;
        }

        $this->type = $maintenance->type === 'panne' ? 'reparation' : 'entretien';

        if ($maintenance->cout) {
            $this->montant = (string) $maintenance->cout;
        }
    }

    public function create(): void
    {
        $this->authorize('charges.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('charges.update');

        $charge = Charge::findOrFail($id);
        $this->chargeId = $charge->id;
        $this->engin_id = $charge->engin_id;
        $this->maintenance_id = $charge->maintenance_id;
        $this->type = $charge->type;
        $this->date = $charge->date?->format('Y-m-d') ?? '';
        $this->montant = (string) $charge->montant;
        $this->description = (string) $charge->description;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->chargeId ? 'charges.update' : 'charges.create');

        $data = $this->validate();

        Charge::updateOrCreate(['id' => $this->chargeId], $data);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('charges.delete');

        Charge::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'chargeId', 'engin_id', 'maintenance_id', 'date', 'montant', 'description',
        ]);
        $this->type = 'carburant';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('charges.view');

        return view('livewire.charges.manager', [
            'charges' => Charge::query()
                ->with(['engin', 'maintenance'])
                ->when($this->search, fn ($query) => $query
                    ->whereHas('engin', fn ($q) => $q->where('designation', 'like', "%{$this->search}%")))
                ->orderByDesc('date')
                ->paginate(10),
            'enginsOptions' => Engin::orderBy('designation')->get(['id', 'designation']),
            'maintenancesOptions' => $this->engin_id
                ? Maintenance::where('engin_id', $this->engin_id)->orderByDesc('date_debut')->get()
                : collect(),
        ]);
    }
}
