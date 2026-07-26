<?php

namespace App\Livewire\Contrats;

use App\Models\Client;
use App\Models\Contrat;
use App\Models\Engin;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Gestion des contrats'])]
class Manager extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $contratId = null;

    public ?int $client_id = null;

    public ?int $engin_id = null;

    public string $date_debut = '';

    public string $date_fin = '';

    public string $lieu_chantier = '';

    public string $tarif_horaire = '';

    public string $statut = 'en_cours';

    public $documentPdf = null;

    public ?string $currentDocumentPath = null;

    public array $statuts = [
        'en_cours' => 'En cours',
        'termine' => 'Terminé',
        'resilie' => 'Résilié',
    ];

    protected function rules(): array
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'engin_id' => 'required|exists:engins,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut',
            'lieu_chantier' => 'nullable|string|max:255',
            'tarif_horaire' => 'required|numeric|min:0',
            'statut' => 'required|in:'.implode(',', array_keys($this->statuts)),
            'documentPdf' => 'nullable|file|mimes:pdf|max:10240',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedEnginId($value): void
    {
        if ($value) {
            $this->tarif_horaire = (string) Engin::find($value)?->tarif_horaire;
        }
    }

    public function create(): void
    {
        $this->authorize('contrats.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('contrats.update');

        $contrat = Contrat::findOrFail($id);
        $this->contratId = $contrat->id;
        $this->client_id = $contrat->client_id;
        $this->engin_id = $contrat->engin_id;
        $this->date_debut = $contrat->date_debut?->format('Y-m-d') ?? '';
        $this->date_fin = $contrat->date_fin?->format('Y-m-d') ?? '';
        $this->lieu_chantier = (string) $contrat->lieu_chantier;
        $this->tarif_horaire = (string) $contrat->tarif_horaire;
        $this->statut = $contrat->statut;
        $this->currentDocumentPath = $contrat->document_pdf;
        $this->documentPdf = null;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->contratId ? 'contrats.update' : 'contrats.create');

        $data = $this->validate();
        unset($data['documentPdf']);

        $data['date_fin'] = $this->date_fin ?: null;

        if ($this->documentPdf) {
            if ($this->currentDocumentPath) {
                Storage::disk('public')->delete($this->currentDocumentPath);
            }
            $data['document_pdf'] = $this->documentPdf->store('contrats', 'public');
        }

        Contrat::updateOrCreate(['id' => $this->contratId], $data);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('contrats.delete');

        $contrat = Contrat::findOrFail($id);

        if ($contrat->document_pdf) {
            Storage::disk('public')->delete($contrat->document_pdf);
        }

        $contrat->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'contratId', 'client_id', 'engin_id', 'date_debut', 'date_fin',
            'lieu_chantier', 'tarif_horaire', 'documentPdf', 'currentDocumentPath',
        ]);
        $this->statut = 'en_cours';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('contrats.view');

        return view('livewire.contrats.manager', [
            'contrats' => Contrat::query()
                ->with(['client', 'engin'])
                ->when($this->search, fn ($query) => $query
                    ->where('numero', 'like', "%{$this->search}%")
                    ->orWhere('lieu_chantier', 'like', "%{$this->search}%")
                    ->orWhereHas('client', fn ($q) => $q->where('nom', 'like', "%{$this->search}%"))
                    ->orWhereHas('engin', fn ($q) => $q->where('designation', 'like', "%{$this->search}%")))
                ->orderByDesc('id')
                ->paginate(10),
            'clientsOptions' => Client::orderBy('nom')->get(['id', 'nom']),
            'enginsOptions' => Engin::orderBy('designation')->get(['id', 'designation', 'tarif_horaire']),
        ]);
    }
}
