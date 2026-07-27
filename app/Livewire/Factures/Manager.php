<?php

namespace App\Livewire\Factures;

use App\Models\Contrat;
use App\Models\Facture;
use App\Models\Pointage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Facturation'])]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $factureId = null;

    public ?int $contrat_id = null;

    public string $type = 'periodique';

    public string $periode_debut = '';

    public string $periode_fin = '';

    public string $heures_facturees = '0';

    public string $montant = '0';

    public string $date_echeance = '';

    public string $statut = 'brouillon';

    public array $types = [
        'periodique' => 'Périodique',
        'cloture' => 'Clôture',
    ];

    public array $statuts = [
        'brouillon' => 'Brouillon',
        'emise' => 'Émise',
        'partiellement_payee' => 'Partiellement payée',
        'payee' => 'Payée',
        'en_retard' => 'En retard',
    ];

    protected function rules(): array
    {
        return [
            'contrat_id' => 'required|exists:contrats,id',
            'type' => 'required|in:'.implode(',', array_keys($this->types)),
            'periode_debut' => 'required|date',
            'periode_fin' => 'required|date|after_or_equal:periode_debut',
            'heures_facturees' => 'required|numeric|min:0',
            'montant' => 'required|numeric|min:0',
            'date_echeance' => 'nullable|date',
            'statut' => 'required|in:'.implode(',', array_keys($this->statuts)),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedContratId(): void
    {
        $this->recalculerDepuisPointages();
    }

    public function updatedPeriodeDebut(): void
    {
        $this->recalculerDepuisPointages();
    }

    public function updatedPeriodeFin(): void
    {
        $this->recalculerDepuisPointages();
    }

    private function recalculerDepuisPointages(): void
    {
        if (! $this->contrat_id || ! $this->periode_debut || ! $this->periode_fin) {
            return;
        }

        $contrat = Contrat::find($this->contrat_id);

        if (! $contrat) {
            return;
        }

        $heures = Pointage::where('contrat_id', $this->contrat_id)
            ->whereBetween('date', [$this->periode_debut, $this->periode_fin])
            ->sum('heures_travaillees');

        $this->heures_facturees = (string) $heures;
        $this->montant = (string) round($heures * (float) $contrat->tarif_horaire, 2);
    }

    public function create(): void
    {
        $this->authorize('factures.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('factures.update');

        $facture = Facture::findOrFail($id);
        $this->factureId = $facture->id;
        $this->contrat_id = $facture->contrat_id;
        $this->type = $facture->type;
        $this->periode_debut = $facture->periode_debut?->format('Y-m-d') ?? '';
        $this->periode_fin = $facture->periode_fin?->format('Y-m-d') ?? '';
        $this->heures_facturees = (string) $facture->heures_facturees;
        $this->montant = (string) $facture->montant;
        $this->date_echeance = $facture->date_echeance?->format('Y-m-d') ?? '';
        $this->statut = $facture->statut;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->factureId ? 'factures.update' : 'factures.create');

        $data = $this->validate();
        $data['date_echeance'] = $this->date_echeance ?: null;

        Facture::updateOrCreate(['id' => $this->factureId], $data);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('factures.delete');

        Facture::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'factureId', 'contrat_id', 'periode_debut', 'periode_fin', 'date_echeance',
        ]);
        $this->type = 'periodique';
        $this->heures_facturees = '0';
        $this->montant = '0';
        $this->statut = 'brouillon';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('factures.view');

        return view('livewire.factures.manager', [
            'factures' => Facture::query()
                ->with(['contrat.client'])
                ->when($this->search, fn ($query) => $query
                    ->where('numero', 'like', "%{$this->search}%")
                    ->orWhereHas('contrat', fn ($q) => $q->where('numero', 'like', "%{$this->search}%"))
                    ->orWhereHas('contrat.client', fn ($q) => $q->where('nom', 'like', "%{$this->search}%")))
                ->orderByDesc('id')
                ->paginate(10),
            'contratsOptions' => Contrat::with('client')->orderByDesc('id')->get(),
        ]);
    }
}
