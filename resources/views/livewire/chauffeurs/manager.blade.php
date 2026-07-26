<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher un chauffeur (nom, prénom, permis)...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('chauffeurs.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau chauffeur
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Téléphone</th>
                        <th>Permis</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($chauffeurs as $chauffeur)
                        <tr wire:key="chauffeur-{{ $chauffeur->id }}">
                            <td>{{ $chauffeur->nom }}</td>
                            <td>{{ $chauffeur->prenom }}</td>
                            <td>{{ $chauffeur->telephone }}</td>
                            <td>{{ $chauffeur->numero_permis }} {{ $chauffeur->categorie_permis ? '('.$chauffeur->categorie_permis.')' : '' }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-success' => $chauffeur->statut === 'actif',
                                    'badge-secondary' => $chauffeur->statut === 'inactif',
                                ])>
                                    {{ $statuts[$chauffeur->statut] ?? $chauffeur->statut }}
                                </span>
                            </td>
                            <td class="text-right">
                                @can('chauffeurs.update')
                                    <button type="button" wire:click="edit({{ $chauffeur->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('chauffeurs.delete')
                                    <button type="button" wire:click="delete({{ $chauffeur->id }})"
                                        wire:confirm="Supprimer ce chauffeur ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucun chauffeur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $chauffeurs->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $chauffeurId ? 'Modifier le chauffeur' : 'Nouveau chauffeur' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Nom</label>
                                    <input type="text" wire:model="nom" class="form-control">
                                    @error('nom') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Prénom</label>
                                    <input type="text" wire:model="prenom" class="form-control">
                                    @error('prenom') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="text" wire:model="telephone" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Numéro de permis</label>
                                    <input type="text" wire:model="numero_permis" class="form-control">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Catégorie de permis</label>
                                    <input type="text" wire:model="categorie_permis" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Statut</label>
                                <select wire:model="statut" class="form-control">
                                    @foreach ($statuts as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
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
