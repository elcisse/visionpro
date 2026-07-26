<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher (numéro de contrat, chauffeur)...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('pointages.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau pointage
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
                        <th>Contrat</th>
                        <th>Chauffeur</th>
                        <th>Heures travaillées</th>
                        <th>Panne</th>
                        <th>Commentaire</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pointages as $pointage)
                        <tr wire:key="pointage-{{ $pointage->id }}">
                            <td>{{ $pointage->date->format('d/m/Y') }}</td>
                            <td>{{ $pointage->contrat->numero }} — {{ $pointage->contrat->engin->designation }}</td>
                            <td>{{ $pointage->chauffeur ? $pointage->chauffeur->prenom.' '.$pointage->chauffeur->nom : '—' }}</td>
                            <td>{{ number_format($pointage->heures_travaillees, 2, ',', ' ') }} h</td>
                            <td>
                                @if ($pointage->en_panne)
                                    <span class="badge badge-danger">{{ number_format($pointage->heures_panne, 2, ',', ' ') }} h</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($pointage->commentaire, 40) }}</td>
                            <td class="text-right">
                                @can('pointages.update')
                                    <button type="button" wire:click="edit({{ $pointage->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('pointages.delete')
                                    <button type="button" wire:click="delete({{ $pointage->id }})"
                                        wire:confirm="Supprimer ce pointage ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun pointage trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $pointages->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $pointageId ? 'Modifier le pointage' : 'Nouveau pointage' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Contrat</label>
                                <select wire:model="contrat_id" class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($contratsOptions as $contrat)
                                        <option value="{{ $contrat->id }}">
                                            {{ $contrat->numero }} — {{ $contrat->client->nom }} ({{ $contrat->engin->designation }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('contrat_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Chauffeur (du jour)</label>
                                <select wire:model="chauffeur_id" class="form-control">
                                    <option value="">-- Aucun --</option>
                                    @foreach ($chauffeursOptions as $chauffeur)
                                        <option value="{{ $chauffeur->id }}">{{ $chauffeur->prenom }} {{ $chauffeur->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Date</label>
                                    <input type="date" wire:model="date" class="form-control">
                                    @error('date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Heures travaillées</label>
                                    <input type="number" step="0.5" wire:model="heures_travaillees" class="form-control">
                                    @error('heures_travaillees') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="form-group form-check">
                                <input type="checkbox" wire:model.live="en_panne" class="form-check-input" id="en_panne">
                                <label class="form-check-label" for="en_panne">Panne ce jour-là</label>
                            </div>
                            @if ($en_panne)
                                <div class="form-group">
                                    <label>Heures de panne (non facturables)</label>
                                    <input type="number" step="0.5" wire:model="heures_panne" class="form-control">
                                    @error('heures_panne') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            @endif
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
