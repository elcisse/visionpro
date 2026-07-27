<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Facture {{ $facture->numero }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #222; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .header-table td { vertical-align: top; }
        .entreprise-nom { font-size: 16px; font-weight: bold; }
        .facture-titre { font-size: 20px; font-weight: bold; text-align: right; color: #0d6efd; }
        .facture-numero { text-align: right; }
        .muted { color: #666; }
        table.details { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.details th, table.details td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        table.details th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; font-size: 14px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; color: #fff; font-size: 11px; }
        .bg-emise { background-color: #17a2b8; }
        .bg-payee { background-color: #28a745; }
        .bg-partiellement_payee { background-color: #ffc107; color: #000; }
        .bg-en_retard { background-color: #dc3545; }
        .bg-brouillon { background-color: #6c757d; }
        .clients-box { margin-top: 10px; padding: 10px; border: 1px solid #ccc; width: 45%; }
        .footer { margin-top: 40px; font-size: 10px; color: #888; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                <div class="entreprise-nom">{{ $entreprise->nom ?? config('app.name') }}</div>
                @if ($entreprise?->adresse)<div>{{ $entreprise->adresse }}</div>@endif
                @if ($entreprise?->ninea)<div class="muted">NINEA : {{ $entreprise->ninea }}</div>@endif
                @if ($entreprise?->telephone)<div class="muted">Tél : {{ $entreprise->telephone }}</div>@endif
                @if ($entreprise?->email)<div class="muted">{{ $entreprise->email }}</div>@endif
            </td>
            <td style="width: 40%;">
                <div class="facture-titre">FACTURE</div>
                <div class="facture-numero">N° {{ $facture->numero }}</div>
                <div class="facture-numero muted">Émise le {{ $facture->created_at->format('d/m/Y') }}</div>
                @if ($facture->date_echeance)
                    <div class="facture-numero muted">Échéance : {{ $facture->date_echeance->format('d/m/Y') }}</div>
                @endif
                <div class="facture-numero">
                    <span class="badge bg-{{ $facture->statut }}">{{ $statuts[$facture->statut] ?? $facture->statut }}</span>
                </div>
            </td>
        </tr>
    </table>

    <div class="clients-box">
        <strong>Client</strong><br>
        {{ $facture->contrat->client->nom }}<br>
        @if ($facture->contrat->client->adresse){{ $facture->contrat->client->adresse }}<br>@endif
        @if ($facture->contrat->client->ninea)NINEA : {{ $facture->contrat->client->ninea }}<br>@endif
    </div>

    <table class="details">
        <thead>
            <tr>
                <th>Contrat</th>
                <th>Engin</th>
                <th>Période</th>
                <th class="text-right">Heures facturées</th>
                <th class="text-right">Tarif horaire</th>
                <th class="text-right">Montant</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $facture->contrat->numero }}</td>
                <td>{{ $facture->contrat->engin->designation }}</td>
                <td>{{ $facture->periode_debut->format('d/m/Y') }} au {{ $facture->periode_fin->format('d/m/Y') }}</td>
                <td class="text-right">{{ number_format($facture->heures_facturees, 2, ',', ' ') }} h</td>
                <td class="text-right">{{ number_format($facture->contrat->tarif_horaire, 0, ',', ' ') }} {{ $entreprise->devise ?? 'FCFA' }}</td>
                <td class="text-right">{{ number_format($facture->montant, 0, ',', ' ') }} {{ $entreprise->devise ?? 'FCFA' }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="5" class="text-right">Total</td>
                <td class="text-right">{{ number_format($facture->montant, 0, ',', ' ') }} {{ $entreprise->devise ?? 'FCFA' }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        {{ $entreprise->nom ?? config('app.name') }}
        @if ($entreprise?->site_web) — {{ $entreprise->site_web }}@endif
    </div>
</body>
</html>
