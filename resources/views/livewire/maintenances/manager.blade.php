<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher un engin...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('maintenances.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle maintenance
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Engin</th>
                        <th>Type</th>
                        <th>Début</th>
                        <th>Fin</th>
                        <th>Coût</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maintenances as $maintenance)
                        <tr wire:key="maintenance-{{ $maintenance->id }}">
                            <td>{{ $maintenance->engin->designation }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-danger' => $maintenance->type === 'panne',
                                    'badge-info' => $maintenance->type === 'entretien_preventif',
                                ])>
                                    {{ $types[$maintenance->type] ?? $maintenance->type }}
                                </span>
                            </td>
                            <td>{{ $maintenance->date_debut->format('d/m/Y') }}</td>
                            <td>{{ $maintenance->date_fin?->format('d/m/Y') ?? '—' }}</td>
                            <td>{{ $maintenance->cout ? number_format($maintenance->cout, 0, ',', ' ').' FCFA' : '—' }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-secondary' => $maintenance->statut === 'planifiee',
                                    'badge-warning' => $maintenance->statut === 'en_cours',
                                    'badge-success' => $maintenance->statut === 'terminee',
                                ])>
                                    {{ $statuts[$maintenance->statut] ?? $maintenance->statut }}
                                </span>
                            </td>
                            <td class="text-right">
                                @can('maintenances.update')
                                    <button type="button" wire:click="edit({{ $maintenance->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('maintenances.delete')
                                    <button type="button" wire:click="delete({{ $maintenance->id }})"
                                        wire:confirm="Supprimer cette maintenance ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucune maintenance trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $maintenances->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $maintenanceId ? 'Modifier la maintenance' : 'Nouvelle maintenance' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Engin</label>
                                <select wire:model="engin_id" class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($enginsOptions as $engin)
                                        <option value="{{ $engin->id }}">{{ $engin->designation }}</option>
                                    @endforeach
                                </select>
                                @error('engin_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Type</label>
                                    <select wire:model="type" class="form-control">
                                        @foreach ($types as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
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
                                <label>Coût (FCFA)</label>
                                <input type="number" step="0.01" wire:model="cout" class="form-control">
                                @error('cout') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea wire:model="description" class="form-control" rows="2"></textarea>
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
