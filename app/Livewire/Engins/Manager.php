<?php

namespace App\Livewire\Engins;

use App\Models\Engin;
use App\Services\EnginStatutService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

#[Layout('layouts.admin', ['title' => 'Gestion des engins'])]
class Manager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $enginId = null;

    public string $designation = '';

    public string $categorie = '';

    public string $marque = '';

    public string $modele = '';

    public string $numero_serie = '';

    public string $tarif_horaire = '';

    public string $statut = 'disponible';

    public string $compteur_horaire = '0';

    public array $photos = [];

    public array $statuts = [
        'disponible' => 'Disponible',
        'en_location' => 'En location',
        'en_panne' => 'En panne',
        'en_entretien' => 'En entretien',
        'hors_service' => 'Hors service',
    ];

    protected function rules(): array
    {
        return [
            'designation' => 'required|string|max:255',
            'categorie' => 'required|string|max:255',
            'marque' => 'nullable|string|max:255',
            'modele' => 'nullable|string|max:255',
            'numero_serie' => 'nullable|string|max:255',
            'tarif_horaire' => 'required|numeric|min:0',
            'statut' => 'required|in:'.implode(',', array_keys($this->statuts)),
            'compteur_horaire' => 'required|numeric|min:0',
            'photos.*' => 'nullable|image|max:2048',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('engins.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('engins.update');

        $engin = Engin::findOrFail($id);
        $this->enginId = $engin->id;
        $this->designation = $engin->designation;
        $this->categorie = $engin->categorie;
        $this->marque = (string) $engin->marque;
        $this->modele = (string) $engin->modele;
        $this->numero_serie = (string) $engin->numero_serie;
        $this->tarif_horaire = (string) $engin->tarif_horaire;
        $this->statut = $engin->statut;
        $this->compteur_horaire = (string) $engin->compteur_horaire;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->enginId ? 'engins.update' : 'engins.create');

        $data = $this->validate();
        unset($data['photos']);

        $engin = Engin::updateOrCreate(['id' => $this->enginId], $data);

        // Un statut choisi manuellement (autre que hors_service) est immédiatement
        // recorrigé s'il ne correspond pas aux contrats/maintenances réellement en cours.
        EnginStatutService::synchroniser($engin);

        foreach ($this->photos as $photo) {
            $engin->addMedia($photo->getRealPath())
                ->usingFileName($photo->getClientOriginalName())
                ->toMediaCollection('photos');
        }

        $this->showModal = false;
        $this->resetFields();
    }

    public function deletePhoto(int $mediaId): void
    {
        $this->authorize('engins.update');

        $media = Media::findOrFail($mediaId);

        abort_unless(
            $media->model_type === Engin::class && $media->model_id === $this->enginId,
            403
        );

        $media->delete();
    }

    public function delete(int $id): void
    {
        $this->authorize('engins.delete');

        Engin::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'enginId', 'designation', 'categorie', 'marque', 'modele',
            'numero_serie', 'tarif_horaire', 'compteur_horaire', 'photos',
        ]);
        $this->statut = 'disponible';
        $this->compteur_horaire = '0';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('engins.view');

        return view('livewire.engins.manager', [
            'engins' => Engin::query()
                ->with('media')
                ->withSum('charges as charges_total', 'montant')
                ->withSum('factures as recettes_total', 'montant')
                ->when($this->search, fn ($query) => $query
                    ->where('designation', 'like', "%{$this->search}%")
                    ->orWhere('categorie', 'like', "%{$this->search}%")
                    ->orWhere('modele', 'like', "%{$this->search}%"))
                ->orderBy('designation')
                ->paginate(10),
            'enginEnEdition' => $this->enginId ? Engin::with('media')->find($this->enginId) : null,
        ]);
    }
}
