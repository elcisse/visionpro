<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher un contrat (numéro, client, engin, chantier)...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('contrats.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau contrat
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
                        <th>Client</th>
                        <th>Engin</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Tarif horaire</th>
                        <th>Statut</th>
                        <th>Document</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($contrats as $contrat)
                        <tr wire:key="contrat-{{ $contrat->id }}">
                            <td>{{ $contrat->numero }}</td>
                            <td>{{ $contrat->client->nom }}</td>
                            <td>{{ $contrat->engin->designation }}</td>
                            <td>{{ $contrat->date_debut->format('d/m/Y') }}</td>
                            <td>{{ $contrat->date_fin?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ number_format($contrat->tarif_horaire, 0, ',', ' ') }} FCFA</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-info' => $contrat->statut === 'en_cours',
                                    'badge-success' => $contrat->statut === 'termine',
                                    'badge-danger' => $contrat->statut === 'resilie',
                                ])>
                                    {{ $statuts[$contrat->statut] ?? $contrat->statut }}
                                </span>
                            </td>
                            <td>
                                @if ($contrat->document_pdf)
                                    <a href="{{ asset('storage/'.$contrat->document_pdf) }}" target="_blank"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-pdf"></i> Voir
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @can('contrats.update')
                                    <button type="button" wire:click="edit({{ $contrat->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('contrats.delete')
                                    <button type="button" wire:click="delete({{ $contrat->id }})"
                                        wire:confirm="Supprimer ce contrat ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Aucun contrat trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $contrats->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $contratId ? 'Modifier le contrat' : 'Nouveau contrat' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Client</label>
                                    <select wire:model="client_id" class="form-control">
                                        <option value="">-- Sélectionner --</option>
                                        @foreach ($clientsOptions as $client)
                                            <option value="{{ $client->id }}">{{ $client->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('client_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Engin</label>
                                    <select wire:model.live="engin_id" class="form-control">
                                        <option value="">-- Sélectionner --</option>
                                        @foreach ($enginsOptions as $engin)
                                            <option value="{{ $engin->id }}">{{ $engin->designation }}</option>
                                        @endforeach
                                    </select>
                                    @error('engin_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Date de début</label>
                                    <input type="date" wire:model="date_debut" class="form-control">
                                    @error('date_debut') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Date de fin</label>
                                    <input type="date" wire:model="date_fin" class="form-control">
                                    @error('date_fin') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Lieu du chantier</label>
                                <input type="text" wire:model="lieu_chantier" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Tarif horaire (FCFA)</label>
                                    <input type="number" step="0.01" wire:model="tarif_horaire" class="form-control">
                                    @error('tarif_horaire') <span class="text-danger">{{ $message }}</span> @enderror
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
                            <div class="form-group">
                                <label>Contrat (PDF uniquement)</label>
                                <input type="file" wire:model="documentPdf" accept="application/pdf" class="form-control-file">
                                <div wire:loading wire:target="documentPdf" class="text-muted small mt-1">
                                    Envoi du fichier en cours...
                                </div>
                                @error('documentPdf') <span class="text-danger d-block">{{ $message }}</span> @enderror
                                @if ($currentDocumentPath && ! $documentPdf)
                                    <div class="mt-1">
                                        <a href="{{ asset('storage/'.$currentDocumentPath) }}" target="_blank">
                                            <i class="fas fa-file-pdf"></i> Document actuel
                                        </a>
                                    </div>
                                @endif
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
