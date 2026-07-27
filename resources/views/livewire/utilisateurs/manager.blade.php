<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-6">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Rechercher un utilisateur (nom, email)...">
                </div>
                <div class="col-md-6 text-md-right mt-2 mt-md-0">
                    @can('utilisateurs.create')
                        <button type="button" wire:click="create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nouvel utilisateur
                        </button>
                    @endcan
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @error('delete')
                <div class="alert alert-danger m-3 mb-0">{{ $message }}</div>
            @enderror
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôles</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr wire:key="user-{{ $user->id }}">
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted">Aucun rôle</span>
                                @endforelse
                            </td>
                            <td class="text-right">
                                @can('utilisateurs.update')
                                    <button type="button" wire:click="edit({{ $user->id }})"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                @endcan
                                @can('utilisateurs.delete')
                                    <button type="button" wire:click="delete({{ $user->id }})"
                                        wire:confirm="Supprimer cet utilisateur ?"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>

    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form wire:submit="save">
                        <div class="modal-header">
                            <h4 class="modal-title">{{ $userId ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}</h4>
                            <button type="button" class="close" wire:click="closeModal">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Nom</label>
                                <input type="text" wire:model="name" class="form-control">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" wire:model="email" class="form-control">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Mot de passe {{ $userId ? '(laisser vide pour ne pas changer)' : '' }}</label>
                                    <input type="password" wire:model="password" class="form-control">
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Confirmation</label>
                                    <input type="password" wire:model="password_confirmation" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Rôles</label>
                                <div>
                                    @foreach ($rolesOptions as $role)
                                        <div class="form-check form-check-inline">
                                            <input type="checkbox" wire:model="selectedRoles" value="{{ $role }}"
                                                class="form-check-input" id="role-{{ $role }}">
                                            <label class="form-check-label" for="role-{{ $role }}">{{ $role }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('selectedRoles') <span class="text-danger">{{ $message }}</span> @enderror
                                @error('selectedRoles.*') <span class="text-danger">{{ $message }}</span> @enderror
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
