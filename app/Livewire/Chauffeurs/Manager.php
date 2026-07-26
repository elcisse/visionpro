<?php

namespace App\Livewire\Chauffeurs;

use App\Models\Chauffeur;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Gestion des chauffeurs'])]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $chauffeurId = null;

    public string $nom = '';

    public string $prenom = '';

    public string $telephone = '';

    public string $numero_permis = '';

    public string $categorie_permis = '';

    public string $statut = 'actif';

    public array $statuts = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
    ];

    protected function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'numero_permis' => 'nullable|string|max:255',
            'categorie_permis' => 'nullable|string|max:255',
            'statut' => 'required|in:'.implode(',', array_keys($this->statuts)),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('chauffeurs.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('chauffeurs.update');

        $chauffeur = Chauffeur::findOrFail($id);
        $this->chauffeurId = $chauffeur->id;
        $this->nom = $chauffeur->nom;
        $this->prenom = $chauffeur->prenom;
        $this->telephone = (string) $chauffeur->telephone;
        $this->numero_permis = (string) $chauffeur->numero_permis;
        $this->categorie_permis = (string) $chauffeur->categorie_permis;
        $this->statut = $chauffeur->statut;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->chauffeurId ? 'chauffeurs.update' : 'chauffeurs.create');

        $data = $this->validate();

        Chauffeur::updateOrCreate(['id' => $this->chauffeurId], $data);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('chauffeurs.delete');

        Chauffeur::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'chauffeurId', 'nom', 'prenom', 'telephone', 'numero_permis', 'categorie_permis',
        ]);
        $this->statut = 'actif';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('chauffeurs.view');

        return view('livewire.chauffeurs.manager', [
            'chauffeurs' => Chauffeur::query()
                ->when($this->search, fn ($query) => $query
                    ->where('nom', 'like', "%{$this->search}%")
                    ->orWhere('prenom', 'like', "%{$this->search}%")
                    ->orWhere('numero_permis', 'like', "%{$this->search}%"))
                ->orderBy('nom')
                ->paginate(10),
        ]);
    }
}
