<?php

namespace App\Livewire\Paiements;

use App\Models\Facture;
use App\Models\Paiement;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Paiements'])]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $paiementId = null;

    public ?int $facture_id = null;

    public string $date_paiement = '';

    public string $montant = '';

    public string $mode_paiement = 'virement';

    public string $reference = '';

    public string $commentaire = '';

    public array $modesPaiement = [
        'especes' => 'Espèces',
        'cheque' => 'Chèque',
        'virement' => 'Virement',
        'mobile_money' => 'Mobile Money',
    ];

    protected function rules(): array
    {
        return [
            'facture_id' => 'required|exists:factures,id',
            'date_paiement' => 'required|date',
            'montant' => 'required|numeric|min:0.01',
            'mode_paiement' => 'required|in:'.implode(',', array_keys($this->modesPaiement)),
            'reference' => 'nullable|string|max:255',
            'commentaire' => 'nullable|string|max:1000',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function soldeRestant(): float
    {
        if (! $this->facture_id) {
            return 0;
        }

        $facture = Facture::find($this->facture_id);

        if (! $facture) {
            return 0;
        }

        $dejaPaye = $facture->paiements()
            ->when($this->paiementId, fn ($query) => $query->where('id', '!=', $this->paiementId))
            ->sum('montant');

        return round((float) $facture->montant - (float) $dejaPaye, 2);
    }

    public function create(): void
    {
        $this->authorize('paiements.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('paiements.update');

        $paiement = Paiement::findOrFail($id);
        $this->paiementId = $paiement->id;
        $this->facture_id = $paiement->facture_id;
        $this->date_paiement = $paiement->date_paiement?->format('Y-m-d') ?? '';
        $this->montant = (string) $paiement->montant;
        $this->mode_paiement = $paiement->mode_paiement;
        $this->reference = (string) $paiement->reference;
        $this->commentaire = (string) $paiement->commentaire;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->paiementId ? 'paiements.update' : 'paiements.create');

        $data = $this->validate();

        Paiement::updateOrCreate(['id' => $this->paiementId], $data);

        $this->synchroniserStatutFacture($data['facture_id']);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('paiements.delete');

        $paiement = Paiement::findOrFail($id);
        $factureId = $paiement->facture_id;
        $paiement->delete();

        $this->synchroniserStatutFacture($factureId);
    }

    private function synchroniserStatutFacture(int $factureId): void
    {
        $facture = Facture::find($factureId);

        if (! $facture || in_array($facture->statut, ['brouillon'], true)) {
            return;
        }

        $totalPaye = $facture->paiements()->sum('montant');

        if ($totalPaye >= (float) $facture->montant) {
            $facture->statut = 'payee';
        } elseif ($totalPaye > 0) {
            $facture->statut = 'partiellement_payee';
        } else {
            $facture->statut = 'emise';
        }

        $facture->save();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'paiementId', 'facture_id', 'date_paiement', 'montant', 'reference', 'commentaire',
        ]);
        $this->mode_paiement = 'virement';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('paiements.view');

        return view('livewire.paiements.manager', [
            'paiements' => Paiement::query()
                ->with(['facture.contrat.client'])
                ->when($this->search, fn ($query) => $query
                    ->whereHas('facture', fn ($q) => $q->where('numero', 'like', "%{$this->search}%"))
                    ->orWhereHas('facture.contrat.client', fn ($q) => $q->where('nom', 'like', "%{$this->search}%")))
                ->orderByDesc('date_paiement')
                ->paginate(10),
            'facturesOptions' => Facture::with('contrat.client')->orderByDesc('id')->get(),
        ]);
    }
}
