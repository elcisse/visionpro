<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher un engin...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('charges.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvelle charge
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
                        <th>Engin</th>
                        <th>Type</th>
                        <th>Montant</th>
                        <th>Maintenance liée</th>
                        <th>Description</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($charges as $charge)
                        <tr wire:key="charge-{{ $charge->id }}">
                            <td>{{ $charge->date->format('d/m/Y') }}</td>
                            <td>{{ $charge->engin->designation }}</td>
                            <td>
                                <span class="badge badge-secondary">{{ $types[$charge->type] ?? $charge->type }}</span>
                            </td>
                            <td>{{ number_format($charge->montant, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if ($charge->maintenance)
                                    <span class="text-muted">{{ $charge->maintenance->date_debut->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ \Illuminate\Support\Str::limit($charge->description, 40) }}</td>
                            <td class="text-right">
                                @can('charges.update')
                                    <button type="button" wire:click="edit({{ $charge->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('charges.delete')
                                    <button type="button" wire:click="delete({{ $charge->id }})"
                                        wire:confirm="Supprimer cette charge ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucune charge trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $charges->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $chargeId ? 'Modifier la charge' : 'Nouvelle charge' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Engin</label>
                                <select wire:model.live="engin_id" class="form-control">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach ($enginsOptions as $engin)
                                        <option value="{{ $engin->id }}">{{ $engin->designation }}</option>
                                    @endforeach
                                </select>
                                @error('engin_id') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            @if ($engin_id)
                                <div class="form-group">
                                    <label>Maintenance liée (optionnel)</label>
                                    <select wire:model.live="maintenance_id" class="form-control">
                                        <option value="">-- Aucune --</option>
                                        @foreach ($maintenancesOptions as $maintenance)
                                            <option value="{{ $maintenance->id }}">
                                                {{ $maintenance->date_debut->format('d/m/Y') }} — {{ $types[$maintenance->type === 'panne' ? 'reparation' : 'entretien'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
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
                                    <label>Date</label>
                                    <input type="date" wire:model="date" class="form-control">
                                    @error('date') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Montant (FCFA)</label>
                                <input type="number" step="0.01" wire:model="montant" class="form-control">
                                @error('montant') <span class="text-danger">{{ $message }}</span> @enderror
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
