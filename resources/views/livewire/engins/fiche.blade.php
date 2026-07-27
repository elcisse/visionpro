<div x-data="{ tab: 'identification' }">
    <div class="mb-3">
        <a href="{{ route('engins.index') }}" wire:navigate class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="d-flex align-items-center mb-3">
        <h3 class="mb-0 mr-3">{{ $engin->designation }}</h3>
        <span @class([
            'badge',
            'badge-success' => $engin->statut === 'disponible',
            'badge-info' => $engin->statut === 'en_location',
            'badge-danger' => $engin->statut === 'en_panne',
            'badge-warning' => $engin->statut === 'en_entretien',
            'badge-secondary' => $engin->statut === 'hors_service',
        ])>
            {{ [
                'disponible' => 'Disponible',
                'en_location' => 'En location',
                'en_panne' => 'En panne',
                'en_entretien' => 'En entretien',
                'hors_service' => 'Hors service',
            ][$engin->statut] ?? $engin->statut }}
        </span>
    </div>

    <div class="card card-primary card-outline card-tabs">
        <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a href="#" @click.prevent="tab = 'identification'" :class="{ 'active': tab === 'identification' }" class="nav-link active">
                        <i class="fas fa-id-card"></i> Identification
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" @click.prevent="tab = 'location'" :class="{ 'active': tab === 'location' }" class="nav-link">
                        <i class="fas fa-file-contract"></i> Location
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" @click.prevent="tab = 'entretien'" :class="{ 'active': tab === 'entretien' }" class="nav-link">
                        <i class="fas fa-tools"></i> Entretien
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" @click.prevent="tab = 'rendement'" :class="{ 'active': tab === 'rendement' }" class="nav-link">
                        <i class="fas fa-chart-line"></i> Rendement
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" @click.prevent="tab = 'terrain'" :class="{ 'active': tab === 'terrain' }" class="nav-link">
                        <i class="fas fa-clipboard-check"></i> Suivi terrain
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">

            {{-- Identification --}}
            <div x-show="tab === 'identification'">
                <div class="row">
                    <div class="col-md-4">
                        @php ($photos = $engin->getMedia('photos'))
                        @if ($photos->isNotEmpty())
                            <img src="{{ $photos->first()->getUrl('thumb') }}" class="img-fluid rounded mb-2" style="width: 100%; object-fit: cover;">
                            <div class="d-flex flex-wrap" style="gap: 6px;">
                                @foreach ($photos->skip(1) as $photo)
                                    <img src="{{ $photo->getUrl('thumb') }}" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px;">
                                @endforeach
                            </div>
                        @else
                            <div class="text-muted text-center border rounded p-5">
                                <i class="fas fa-image fa-2x"></i>
                                <p class="mb-0 mt-2">Aucune photo</p>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <table class="table table-sm">
                            <tr><th style="width: 220px;">Catégorie</th><td>{{ $engin->categorie }}</td></tr>
                            <tr><th>Marque / Modèle</th><td>{{ $engin->marque }} {{ $engin->modele }}</td></tr>
                            <tr><th>Numéro de série</th><td>{{ $engin->numero_serie ?? '—' }}</td></tr>
                            <tr><th>Tarif horaire</th><td>{{ number_format($engin->tarif_horaire, 0, ',', ' ') }} FCFA/h</td></tr>
                            <tr><th>Compteur horaire</th><td>{{ number_format($engin->compteur_horaire, 2, ',', ' ') }} h</td></tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Location --}}
            <div x-show="tab === 'location'" style="display: none;">
                <h5>Contrat en cours</h5>
                @if ($contratEnCours)
                    <div class="callout callout-info">
                        <strong>{{ $contratEnCours->numero }}</strong> — {{ $contratEnCours->client->nom }}<br>
                        Du {{ $contratEnCours->date_debut->format('d/m/Y') }}
                        @if ($contratEnCours->date_fin) au {{ $contratEnCours->date_fin->format('d/m/Y') }} @endif
                        — {{ $contratEnCours->lieu_chantier ?? 'Chantier non renseigné' }}
                    </div>
                @else
                    <p class="text-muted">Aucun contrat en cours.</p>
                @endif

                <h5>Chauffeur affecté actuellement</h5>
                @if ($affectationActuelle)
                    <p>{{ $affectationActuelle->chauffeur->prenom }} {{ $affectationActuelle->chauffeur->nom }}
                        <span class="text-muted">(depuis le {{ $affectationActuelle->date_debut->format('d/m/Y') }})</span></p>
                @else
                    <p class="text-muted">Aucun chauffeur affecté actuellement.</p>
                @endif

                <h5>Historique des contrats</h5>
                <table class="table table-sm table-striped">
                    <thead><tr><th>Numéro</th><th>Client</th><th>Début</th><th>Fin</th><th>Statut</th></tr></thead>
                    <tbody>
                        @forelse ($contratsHistorique as $contrat)
                            <tr>
                                <td>{{ $contrat->numero }}</td>
                                <td>{{ $contrat->client->nom }}</td>
                                <td>{{ $contrat->date_debut->format('d/m/Y') }}</td>
                                <td>{{ $contrat->date_fin?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $contrat->statut }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Aucun contrat.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h5>Historique des affectations chauffeur</h5>
                <table class="table table-sm table-striped">
                    <thead><tr><th>Chauffeur</th><th>Début</th><th>Fin</th></tr></thead>
                    <tbody>
                        @forelse ($affectationsHistorique as $affectation)
                            <tr>
                                <td>{{ $affectation->chauffeur->prenom }} {{ $affectation->chauffeur->nom }}</td>
                                <td>{{ $affectation->date_debut->format('d/m/Y') }}</td>
                                <td>{{ $affectation->date_fin?->format('d/m/Y') ?? 'En cours' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted py-3">Aucune affectation.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Entretien --}}
            <div x-show="tab === 'entretien'" style="display: none;">
                <h5>Pannes</h5>
                <table class="table table-sm table-striped">
                    <thead><tr><th>Début</th><th>Fin</th><th>Coût</th><th>Statut</th><th>Description</th></tr></thead>
                    <tbody>
                        @forelse ($pannes as $panne)
                            <tr>
                                <td>{{ $panne->date_debut->format('d/m/Y') }}</td>
                                <td>{{ $panne->date_fin?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $panne->cout ? number_format($panne->cout, 0, ',', ' ').' FCFA' : '—' }}</td>
                                <td>{{ $panne->statut }}</td>
                                <td>{{ $panne->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Aucune panne enregistrée.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                <h5>Entretien préventif</h5>
                <table class="table table-sm table-striped">
                    <thead><tr><th>Début</th><th>Fin</th><th>Coût</th><th>Statut</th><th>Description</th></tr></thead>
                    <tbody>
                        @forelse ($entretiens as $entretien)
                            <tr>
                                <td>{{ $entretien->date_debut->format('d/m/Y') }}</td>
                                <td>{{ $entretien->date_fin?->format('d/m/Y') ?? '—' }}</td>
                                <td>{{ $entretien->cout ? number_format($entretien->cout, 0, ',', ' ').' FCFA' : '—' }}</td>
                                <td>{{ $entretien->statut }}</td>
                                <td>{{ $entretien->description }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">Aucun entretien enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Rendement --}}
            <div x-show="tab === 'rendement'" style="display: none;">
                <div class="row">
                    <div class="col-md-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ number_format($recettesTotales, 0, ',', ' ') }}</h3>
                                <p>Recettes totales (FCFA)</p>
                            </div>
                            <div class="icon"><i class="fas fa-coins"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{ number_format($chargesTotales, 0, ',', ' ') }}</h3>
                                <p>Charges totales (FCFA)</p>
                            </div>
                            <div class="icon"><i class="fas fa-gas-pump"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="small-box {{ $rentabilite >= 0 ? 'bg-info' : 'bg-warning' }}">
                            <div class="inner">
                                <h3>{{ number_format($rentabilite, 0, ',', ' ') }}</h3>
                                <p>Rentabilité nette (FCFA)</p>
                            </div>
                            <div class="icon"><i class="fas fa-balance-scale"></i></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="small-box bg-secondary">
                            <div class="inner">
                                <h3>{{ number_format($heuresTravaillees, 0, ',', ' ') }}</h3>
                                <p>Heures travaillées cumulées</p>
                            </div>
                            <div class="icon"><i class="fas fa-hourglass-half"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Suivi terrain --}}
            <div x-show="tab === 'terrain'" style="display: none;">
                <h5>Derniers pointages</h5>
                <table class="table table-sm table-striped">
                    <thead><tr><th>Date</th><th>Contrat</th><th>Chauffeur</th><th>Heures</th><th>Panne</th><th>Commentaire</th></tr></thead>
                    <tbody>
                        @forelse ($pointagesRecents as $pointage)
                            <tr>
                                <td>{{ $pointage->date->format('d/m/Y') }}</td>
                                <td>{{ $pointage->contrat->numero }} ({{ $pointage->contrat->client->nom }})</td>
                                <td>{{ $pointage->chauffeur ? $pointage->chauffeur->prenom.' '.$pointage->chauffeur->nom : '—' }}</td>
                                <td>{{ number_format($pointage->heures_travaillees, 2, ',', ' ') }} h</td>
                                <td>
                                    @if ($pointage->en_panne)
                                        <span class="badge badge-danger">{{ number_format($pointage->heures_panne, 2, ',', ' ') }} h</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $pointage->commentaire }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Aucun pointage enregistré.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
