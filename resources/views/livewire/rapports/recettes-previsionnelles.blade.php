<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Recettes prévisionnelles</h3>
            <div class="card-tools">
                <a href="{{ route('rapports.recettes-previsionnelles.export') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Exporter Excel
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle">Équipement</th>
                        <th colspan="4" class="text-center">Temps de travail (heures)</th>
                        <th rowspan="2" class="align-middle text-right">Taux (FCFA/h)</th>
                        <th rowspan="2" class="align-middle text-right">Total (FCFA/an)</th>
                    </tr>
                    <tr>
                        <th class="text-center">Jour</th>
                        <th class="text-center">Semaine</th>
                        <th class="text-center">Mensuel</th>
                        <th class="text-center">Année</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lignes as $ligne)
                        <tr wire:key="ligne-{{ $ligne['engin']->id }}">
                            <td>{{ $ligne['engin']->designation }}</td>
                            <td class="text-center">{{ $heures['jour'] }}</td>
                            <td class="text-center">{{ $heures['semaine'] }}</td>
                            <td class="text-center">{{ $heures['mensuel'] }}</td>
                            <td class="text-center">{{ $heures['annee'] }}</td>
                            <td class="text-right">{{ number_format($ligne['engin']->tarif_horaire, 0, ',', ' ') }}</td>
                            <td class="text-right">{{ number_format($ligne['total'], 0, ',', ' ') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Aucun engin enregistré.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($lignes->isNotEmpty())
                    <tfoot>
                        <tr>
                            <th colspan="6" class="text-right">Total recette</th>
                            <th class="text-right">{{ number_format($totalGeneral, 0, ',', ' ') }}</th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="text-muted small">
        Projection basée sur une utilisation continue de chaque engin (conventions : jour = 20h, semaine = 120h, mois = 480h, année = 5760h). Ne reflète pas les recettes réelles — voir le tableau de bord pour la comparaison réel/prévisionnel du mois en cours.
    </div>
</div>
