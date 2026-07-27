<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher un engin (désignation, catégorie, modèle)...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('engins.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvel engin
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Désignation</th>
                        <th>Catégorie</th>
                        <th>Marque / Modèle</th>
                        <th>Tarif horaire</th>
                        <th>Compteur (h)</th>
                        <th>Statut</th>
                        <th>Recettes</th>
                        <th>Charges</th>
                        <th>Rentabilité</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($engins as $engin)
                        @php ($rentabilite = ($engin->recettes_total ?? 0) - ($engin->charges_total ?? 0))
                        <tr wire:key="engin-{{ $engin->id }}">
                            <td>
                                @if ($engin->getFirstMediaUrl('photos', 'thumb'))
                                    <img src="{{ $engin->getFirstMediaUrl('photos', 'thumb') }}" alt=""
                                        style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                @else
                                    <div class="text-muted"><i class="fas fa-image"></i></div>
                                @endif
                            </td>
                            <td>{{ $engin->designation }}</td>
                            <td>{{ $engin->categorie }}</td>
                            <td>{{ $engin->marque }} {{ $engin->modele }}</td>
                            <td>{{ number_format($engin->tarif_horaire, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($engin->compteur_horaire, 2, ',', ' ') }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-success' => $engin->statut === 'disponible',
                                    'badge-info' => $engin->statut === 'en_location',
                                    'badge-danger' => $engin->statut === 'en_panne',
                                    'badge-warning' => $engin->statut === 'en_entretien',
                                    'badge-secondary' => $engin->statut === 'hors_service',
                                ])>
                                    {{ $statuts[$engin->statut] ?? $engin->statut }}
                                </span>
                            </td>
                            <td>{{ number_format($engin->recettes_total ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($engin->charges_total ?? 0, 0, ',', ' ') }} FCFA</td>
                            <td class="{{ $rentabilite >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($rentabilite, 0, ',', ' ') }} FCFA
                            </td>
                            <td class="text-right">
                                @can('engins.update')
                                    <button type="button" wire:click="edit({{ $engin->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('engins.delete')
                                    <button type="button" wire:click="delete({{ $engin->id }})"
                                        wire:confirm="Supprimer cet engin ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center text-muted py-4">Aucun engin trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $engins->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $enginId ? 'Modifier l\'engin' : 'Nouvel engin' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Désignation</label>
                                <input type="text" wire:model="designation" class="form-control">
                                @error('designation') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Catégorie</label>
                                <input type="text" wire:model="categorie" class="form-control">
                                @error('categorie') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Marque</label>
                                    <input type="text" wire:model="marque" class="form-control">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Modèle</label>
                                    <input type="text" wire:model="modele" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Numéro de série</label>
                                <input type="text" wire:model="numero_serie" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Tarif horaire (FCFA)</label>
                                    <input type="number" step="0.01" wire:model="tarif_horaire" class="form-control">
                                    @error('tarif_horaire') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Compteur horaire</label>
                                    <input type="number" step="0.01" wire:model="compteur_horaire" class="form-control">
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

                            <div class="form-group">
                                <label>Photos</label>
                                <input type="file" wire:model="photos" accept="image/*" multiple class="form-control-file">
                                <div wire:loading wire:target="photos" class="text-muted small mt-1">Envoi en cours...</div>
                                @error('photos.*') <span class="text-danger d-block">{{ $message }}</span> @enderror

                                <div class="d-flex flex-wrap mt-2" style="gap: 8px;">
                                    @foreach ($photos as $photo)
                                        <img src="{{ $photo->temporaryUrl() }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
                                    @endforeach

                                    @if ($enginEnEdition)
                                        @foreach ($enginEnEdition->getMedia('photos') as $media)
                                            <div class="position-relative">
                                                <img src="{{ $media->getUrl('thumb') }}" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
                                                <button type="button" wire:click="deletePhoto({{ $media->id }})"
                                                    wire:confirm="Supprimer cette photo ?"
                                                    class="btn btn-sm btn-danger position-absolute"
                                                    style="top: -8px; right: -8px; padding: 0 6px; border-radius: 50%;">
                                                    &times;
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif
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
