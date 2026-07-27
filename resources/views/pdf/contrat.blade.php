<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Contrat {{ $contrat->numero }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #222; line-height: 1.5; }
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .entreprise-nom { font-size: 16px; font-weight: bold; }
        .titre { font-size: 20px; font-weight: bold; text-align: center; margin: 20px 0; text-transform: uppercase; }
        .muted { color: #666; }
        .parties-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .parties-table td { width: 50%; vertical-align: top; padding: 10px; border: 1px solid #ccc; }
        table.details { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table.details th, table.details td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
        table.details th { background-color: #f2f2f2; width: 30%; }
        .clause { margin-top: 20px; padding: 10px; border: 1px solid #ccc; background-color: #fafafa; }
        .signatures { width: 100%; margin-top: 60px; }
        .signatures td { width: 50%; text-align: center; }
        .footer { margin-top: 40px; font-size: 10px; color: #888; border-top: 1px solid #ccc; padding-top: 10px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td>
                <div class="entreprise-nom">{{ $entreprise->nom ?? config('app.name') }}</div>
                @if ($entreprise?->adresse)<div>{{ $entreprise->adresse }}</div>@endif
                @if ($entreprise?->ninea)<div class="muted">NINEA : {{ $entreprise->ninea }}</div>@endif
            </td>
        </tr>
    </table>

    <div class="titre">Contrat de location d'engin N° {{ $contrat->numero }}</div>

    <table class="parties-table">
        <tr>
            <td>
                <strong>Le Bailleur</strong><br>
                {{ $entreprise->nom ?? config('app.name') }}<br>
                @if ($entreprise?->adresse){{ $entreprise->adresse }}<br>@endif
                @if ($entreprise?->ninea)NINEA : {{ $entreprise->ninea }}<br>@endif
                @if ($entreprise?->telephone)Tél : {{ $entreprise->telephone }}<br>@endif
            </td>
            <td>
                <strong>Le Contractant (locataire)</strong><br>
                {{ $contrat->client->nom }}<br>
                @if ($contrat->client->adresse){{ $contrat->client->adresse }}<br>@endif
                @if ($contrat->client->ninea)NINEA : {{ $contrat->client->ninea }}<br>@endif
                @if ($contrat->client->telephone)Tél : {{ $contrat->client->telephone }}<br>@endif
            </td>
        </tr>
    </table>

    <table class="details">
        <tr>
            <th>Engin loué</th>
            <td>{{ $contrat->engin->designation }} @if($contrat->engin->marque)({{ $contrat->engin->marque }} {{ $contrat->engin->modele }})@endif</td>
        </tr>
        <tr>
            <th>Tarif horaire</th>
            <td>{{ number_format($contrat->tarif_horaire, 0, ',', ' ') }} {{ $entreprise->devise ?? 'FCFA' }} / heure</td>
        </tr>
        <tr>
            <th>Lieu du chantier</th>
            <td>{{ $contrat->lieu_chantier ?? '—' }}</td>
        </tr>
        <tr>
            <th>Date de début</th>
            <td>{{ $contrat->date_debut->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <th>Date de fin</th>
            <td>{{ $contrat->date_fin?->format('d/m/Y') ?? 'Non déterminée' }}</td>
        </tr>
    </table>

    <div class="clause">
        <strong>Clause relative aux pannes :</strong> En cas de panne, le propriétaire de la machine reste seul
        responsable de celle-ci et de toute réparation nécessaire, y compris les frais de remise en état et de
        retour de l'engin. La période durant laquelle la machine est immobilisée pour cause de panne ne donnera
        lieu à aucune obligation de paiement ou indemnisation de la part du Contractant.
    </div>

    <table class="signatures">
        <tr>
            <td>Le Bailleur<br><br><br>_____________________</td>
            <td>Le Contractant<br><br><br>_____________________</td>
        </tr>
    </table>

    <div class="footer">
        {{ $entreprise->nom ?? config('app.name') }}
        @if ($entreprise?->site_web) — {{ $entreprise->site_web }}@endif
    </div>
</body>
</html>
