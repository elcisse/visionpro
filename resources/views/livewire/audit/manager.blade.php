<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <div class="row w-100 align-items-center">
                <div class="col-md-4">
                    <select wire:model.live="logName" class="form-control">
                        <option value="">Tous les modules</option>
                        @foreach ($logNames as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Sujet</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($activites as $activite)
                        @php
                            $old = $activite->attribute_changes['old'] ?? [];
                            $new = $activite->attribute_changes['attributes'] ?? [];
                            $numero = $activite->subject?->numero
                                ?? $new['numero']
                                ?? $old['numero']
                                ?? ('#'.$activite->subject_id);
                        @endphp
                        <tr wire:key="activite-{{ $activite->id }}">
                            <td>{{ $activite->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $activite->causer?->name ?? 'Système' }}</td>
                            <td>{{ $logNames[$activite->log_name] ?? $activite->log_name }}</td>
                            <td>
                                <span @class([
                                    'badge',
                                    'badge-success' => $activite->event === 'created',
                                    'badge-info' => $activite->event === 'updated',
                                    'badge-danger' => $activite->event === 'deleted',
                                ])>
                                    {{ ['created' => 'Création', 'updated' => 'Modification', 'deleted' => 'Suppression'][$activite->event] ?? $activite->event }}
                                </span>
                            </td>
                            <td>{{ $numero }}</td>
                            <td>
                                @if ($activite->event === 'updated' && $new)
                                    <ul class="mb-0 pl-3">
                                        @foreach ($new as $champ => $valeur)
                                            @continue($champ === 'updated_at')
                                            <li><strong>{{ $champ }}</strong> : {{ $old[$champ] ?? '—' }} → {{ $valeur }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Aucune activité enregistrée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $activites->links() }}
        </div>
    </div>
</div>
