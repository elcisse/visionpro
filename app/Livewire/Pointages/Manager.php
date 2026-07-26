<?php

namespace App\Livewire\Pointages;

use App\Models\Chauffeur;
use App\Models\Contrat;
use App\Models\Pointage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Pointage journalier'])]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $pointageId = null;

    public ?int $contrat_id = null;

    public ?int $chauffeur_id = null;

    public string $date = '';

    public string $heures_travaillees = '';

    public bool $en_panne = false;

    public string $heures_panne = '0';

    public string $commentaire = '';

    protected function rules(): array
    {
        return [
            'contrat_id' => 'required|exists:contrats,id',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'date' => [
                'required',
                'date',
                Rule::unique('pointages', 'date')
                    ->where(fn ($query) => $query->where('contrat_id', $this->contrat_id))
                    ->ignore($this->pointageId),
            ],
            'heures_travaillees' => 'required|numeric|min:0|max:24',
            'en_panne' => 'boolean',
            'heures_panne' => 'nullable|numeric|min:0|max:24',
            'commentaire' => 'nullable|string|max:1000',
        ];
    }

    protected $messages = [
        'date.unique' => 'Un pointage existe déjà pour ce contrat à cette date.',
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('pointages.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('pointages.update');

        $pointage = Pointage::findOrFail($id);
        $this->pointageId = $pointage->id;
        $this->contrat_id = $pointage->contrat_id;
        $this->chauffeur_id = $pointage->chauffeur_id;
        $this->date = optional($pointage->date)->format('Y-m-d');
        $this->heures_travaillees = (string) $pointage->heures_travaillees;
        $this->en_panne = $pointage->en_panne;
        $this->heures_panne = (string) $pointage->heures_panne;
        $this->commentaire = (string) $pointage->commentaire;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->pointageId ? 'pointages.update' : 'pointages.create');

        $data = $this->validate();

        if (! $data['en_panne']) {
            $data['heures_panne'] = 0;
        }

        Pointage::updateOrCreate(['id' => $this->pointageId], $data);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('pointages.delete');

        Pointage::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'pointageId', 'contrat_id', 'chauffeur_id', 'date',
            'heures_travaillees', 'en_panne', 'commentaire',
        ]);
        $this->heures_panne = '0';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('pointages.view');

        return view('livewire.pointages.manager', [
            'pointages' => Pointage::query()
                ->with(['contrat.client', 'contrat.engin', 'chauffeur'])
                ->when($this->search, fn ($query) => $query
                    ->whereHas('contrat', fn ($q) => $q->where('numero', 'like', "%{$this->search}%"))
                    ->orWhereHas('chauffeur', fn ($q) => $q->where('nom', 'like', "%{$this->search}%")))
                ->orderByDesc('date')
                ->paginate(15),
            'contratsOptions' => Contrat::with(['client', 'engin'])->where('statut', 'en_cours')->get(),
            'chauffeursOptions' => Chauffeur::orderBy('nom')->get(['id', 'nom', 'prenom']),
        ]);
    }
}
