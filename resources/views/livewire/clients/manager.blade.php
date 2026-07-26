<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher un client (nom, téléphone, email)...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('clients.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouveau client
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nom / Raison sociale</th>
                        <th>Type</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>NINEA</th>
                        <th>Statut</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clients as $client)
                        <tr wire:key="client-{{ $client->id }}">
                            <td>{{ $client->nom }}</td>
                            <td>{{ $typesClient[$client->type_client] ?? $client->type_client }}</td>
                            <td>{{ $client->telephone }}</td>
                            <td>{{ $client->email }}</td>
                            <td>{{ $client->ninea }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-success' => $client->statut === 'actif',
                                    'badge-secondary' => $client->statut === 'inactif',
                                ])>
                                    {{ $statuts[$client->statut] ?? $client->statut }}
                                </span>
                            </td>
                            <td class="text-right">
                                @can('clients.update')
                                    <button type="button" wire:click="edit({{ $client->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('clients.delete')
                                    <button type="button" wire:click="delete({{ $client->id }})"
                                        wire:confirm="Supprimer ce client ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun client trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $clients->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $clientId ? 'Modifier le client' : 'Nouveau client' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Type de client</label>
                                <select wire:model="type_client" class="form-control">
                                    @foreach ($typesClient as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nom / Raison sociale</label>
                                <input type="text" wire:model="nom" class="form-control">
                                @error('nom') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Téléphone</label>
                                    <input type="text" wire:model="telephone" class="form-control">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Email</label>
                                    <input type="email" wire:model="email" class="form-control">
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Adresse</label>
                                <input type="text" wire:model="adresse" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>NINEA</label>
                                <input type="text" wire:model="ninea" class="form-control">
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
