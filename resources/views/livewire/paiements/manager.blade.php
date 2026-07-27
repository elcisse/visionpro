<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher (numéro de facture, client)...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('paiements.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau paiement
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Facture</th>
                        <th>Client</th>
                        <th>Montant</th>
                        <th>Mode</th>
                        <th>Référence</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($paiements as $paiement)
                        <tr wire:key="paiement-{{ $paiement->id }}">
                            <td>{{ $paiement->date_paiement->format('d/m/Y') }}</td>
                            <td>{{ $paiement->facture->numero }}</td>
                            <td>{{ $paiement->facture->contrat->client->nom }}</td>
                            <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $modesPaiement[$paiement->mode_paiement] ?? $paiement->mode_paiement }}</td>
                            <td>{{ $paiement->reference }}</td>
                            <td class="text-right">
                                @can('paiements.update')
                                    <button type="button" wire:click="edit({{ $paiement->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('paiements.delete')
                                    <button type="button" wire:click="delete({{ $paiement->id }})"
                                        wire:confirm="Supprimer ce paiement ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun paiement trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $paiements->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $paiementId ? 'Modifier le paiement' : 'Nouveau paiement' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Facture</label>
                                <select wire:model.live="facture_id" class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($facturesOptions as $facture)
                                        <option value="{{ $facture->id }}">
                                            {{ $facture->numero }} — {{ $facture->contrat->client->nom }} ({{ number_format($facture->montant, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                                @error('facture_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @if ($facture_id)
                                <div class="alert alert-info py-2">
                                    Solde restant dû sur cette facture : <strong>{{ number_format($this->soldeRestant(), 0, ',', ' ') }} FCFA</strong>
                                </div>
                            @endif
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Date du paiement</label>
                                    <input type="date" wire:model="date_paiement" class="form-control">
                                    @error('date_paiement') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Montant (FCFA)</label>
                                    <input type="number" step="0.01" wire:model="montant" class="form-control">
                                    @error('montant') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Mode de paiement</label>
                                    <select wire:model="mode_paiement" class="form-control">
                                        @foreach ($modesPaiement as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Référence</label>
                                    <input type="text" wire:model="reference" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Commentaire</label>
                                <textarea wire:model="commentaire" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Annuler</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
