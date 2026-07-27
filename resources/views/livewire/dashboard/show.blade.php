<div>
    <div class="callout callout-info">
        Bienvenue {{ auth()->user()->name }}. Indicateurs du mois en cours ({{ now()->translatedFormat('F Y') }}).
    </div>

    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($recettesReellesMois, 0, ',', ' ') }}</h3>
                    <p>Recettes réelles (FCFA, mois)</p>
                </div>
                <div class="icon"><i class="fas fa-coins"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ number_format($recettesPrevisionnellesMois, 0, ',', ' ') }}</h3>
                    <p>Recettes prévisionnelles (FCFA, mois)</p>
                </div>
                <div class="icon"><i class="fas fa-chart-line"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $tauxUtilisation }}%</h3>
                    <p>Taux d'utilisation du parc ({{ $enginsEnLocation }}/{{ $totalEngins }} en location)</p>
                </div>
                <div class="icon"><i class="fas fa-truck-monster"></i></div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $enginsEnPanne }} / {{ $enginsEnEntretien }}</h3>
                    <p>Engins en panne / en entretien</p>
                </div>
                <div class="icon"><i class="fas fa-tools"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 col-12">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $nombreFacturesImpayees }}</h3>
                    <p>Factures impayées ({{ number_format($montantRestantDu, 0, ',', ' ') }} FCFA restant dû)</p>
                </div>
                <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 col-12">
            <div class="small-box bg-orange">
                <div class="inner">
                    <h3>{{ $facturesEnRetard }}</h3>
                    <p>Factures en retard (échéance dépassée)</p>
                </div>
                <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
        </div>
    </div>
</div>
