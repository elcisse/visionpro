<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher (numéro de facture, contrat, client)...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('factures.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle facture
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Contrat / Client</th>
                        <th>Période</th>
                        <th>Heures facturées</th>
                        <th>Montant</th>
                        <th>Échéance</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($factures as $facture)
                        <tr wire:key="facture-{{ $facture->id }}">
                            <td>{{ $facture->numero }}</td>
                            <td>{{ $facture->contrat->numero }} — {{ $facture->contrat->client->nom }}</td>
                            <td>{{ $facture->periode_debut->format('d/m/Y') }} → {{ $facture->periode_fin->format('d/m/Y') }}</td>
                            <td>{{ number_format($facture->heures_facturees, 2, ',', ' ') }} h</td>
                            <td>{{ number_format($facture->montant, 0, ',', ' ') }} FCFA</td>
                            <td>{{ $facture->date_echeance?->format('d/m/Y') ?? '—' }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-secondary' => $facture->statut === 'brouillon',
                                    'badge-info' => $facture->statut === 'emise',
                                    'badge-warning' => $facture->statut === 'partiellement_payee',
                                    'badge-success' => $facture->statut === 'payee',
                                    'badge-danger' => $facture->statut === 'en_retard',
                                ])>
                                    {{ $statuts[$facture->statut] ?? $facture->statut }}
                                </span>
                            </td>
                            <td class="text-right">
                                @can('factures.view')
                                    <a href="{{ route('factures.pdf', $facture) }}" target="_blank"
                                        class="btn btn-sm btn-outline-dark" title="Générer le PDF de la facture">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @endcan
                                @can('factures.update')
                                    <button type="button" wire:click="edit({{ $facture->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('factures.delete')
                                    <button type="button" wire:click="delete({{ $facture->id }})"
                                        wire:confirm="Supprimer cette facture ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">Aucune facture trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $factures->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $factureId ? 'Modifier la facture' : 'Nouvelle facture' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8 form-group">
                                    <label>Contrat</label>
                                    <select wire:model.live="contrat_id" class="form-control">
                                        <option value="">-- Sélectionner --</option>
                                        @foreach ($contratsOptions as $contrat)
                                            <option value="{{ $contrat->id }}">
                                                {{ $contrat->numero }} — {{ $contrat->client->nom }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('contrat_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4 form-group">
                                    <label>Type</label>
                                    <select wire:model="type" class="form-control">
                                        @foreach ($types as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Début de période</label>
                                    <input type="date" wire:model.live="periode_debut" class="form-control">
                                    @error('periode_debut') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Fin de période</label>
                                    <input type="date" wire:model.live="periode_fin" class="form-control">
                                    @error('periode_fin') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="alert alert-info py-2">
                                <i class="fas fa-calculator"></i>
                                Heures et montant pré-calculés depuis les pointages du contrat sur la période choisie — modifiables si besoin.
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Heures facturées</label>
                                    <input type="number" step="0.01" wire:model="heures_facturees" class="form-control">
                                    @error('heures_facturees') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Montant (FCFA)</label>
                                    <input type="number" step="0.01" wire:model="montant" class="form-control">
                                    @error('montant') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Date d'échéance</label>
                                    <input type="date" wire:model="date_echeance" class="form-control">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Statut</label>
                                    <select wire:model="statut" class="form-control">
                                        @foreach ($statuts as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
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
