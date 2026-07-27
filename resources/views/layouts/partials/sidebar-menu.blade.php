@php
    $menuSections = [
        [
            'header' => null,
            'items' => [
                ['text' => 'Tableau de bord', 'route' => 'dashboard', 'icon' => 'fa-tachometer-alt'],
            ],
        ],
        [
            'header' => 'Parc & personnel',
            'items' => [
                ['text' => 'Engins', 'route' => 'engins.index', 'icon' => 'fa-truck-monster', 'can' => 'engins.view'],
                ['text' => 'Chauffeurs', 'route' => 'chauffeurs.index', 'icon' => 'fa-id-card', 'can' => 'chauffeurs.view'],
                ['text' => 'Maintenance', 'route' => 'maintenances.index', 'icon' => 'fa-tools', 'can' => 'maintenances.view'],
                ['text' => 'Charges', 'route' => 'charges.index', 'icon' => 'fa-gas-pump', 'can' => 'charges.view'],
            ],
        ],
        [
            'header' => 'Commercial',
            'items' => [
                ['text' => 'Clients', 'route' => 'clients.index', 'icon' => 'fa-user-tie', 'can' => 'clients.view'],
                ['text' => 'Contrats', 'route' => 'contrats.index', 'icon' => 'fa-file-contract', 'can' => 'contrats.view'],
                ['text' => 'Pointage journalier', 'route' => 'pointages.index', 'icon' => 'fa-clipboard-check', 'can' => 'pointages.view'],
            ],
        ],
        [
            'header' => 'Facturation',
            'items' => [
                ['text' => 'Factures', 'route' => 'factures.index', 'icon' => 'fa-file-invoice-dollar', 'can' => 'factures.view'],
                ['text' => 'Paiements', 'route' => 'paiements.index', 'icon' => 'fa-money-bill-wave', 'can' => 'paiements.view'],
            ],
        ],
        [
            'header' => 'Administration',
            'items' => [
                ['text' => 'Entreprise', 'route' => 'entreprise.edit', 'icon' => 'fa-building', 'can' => 'entreprise.view'],
                ['text' => 'Utilisateurs & rôles', 'route' => 'utilisateurs.index', 'icon' => 'fa-users-cog', 'can' => 'utilisateurs.view'],
                ['text' => "Journal d'audit", 'route' => 'audit.index', 'icon' => 'fa-history', 'can' => 'audit.view'],
            ],
        ],
    ];
@endphp

<ul class="nav nav-pills nav-sidebar flex-column" role="menu">
    @foreach ($menuSections as $section)
        @php
            $visibleItems = collect($section['items'])->filter(fn ($item) => ! isset($item['can']) || auth()->user()->can($item['can']));
        @endphp
        @continue($visibleItems->isEmpty())

        @if ($section['header'])
            <li class="nav-header">{{ strtoupper($section['header']) }}</li>
        @endif

        @foreach ($visibleItems as $item)
            <li class="nav-item">
                <a href="{{ route($item['route']) }}" wire:navigate
                    class="nav-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-fw {{ $item['icon'] }}"></i>
                    <p>{{ $item['text'] }}</p>
                </a>
            </li>
        @endforeach
    @endforeach
</ul>
