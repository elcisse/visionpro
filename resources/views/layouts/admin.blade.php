<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }} — {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    @livewireStyles
</head>
<body class="hold-transition sidebar-mini layout-fixed" x-data="{ sidebarCollapsed: false }"
    :class="{ 'sidebar-collapse': sidebarCollapsed }">
    <div class="wrapper">

        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#" role="button" @click.prevent="sidebarCollapsed = !sidebarCollapsed">
                        <i class="fas fa-bars"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                @auth
                    <li class="nav-item d-flex align-items-center mr-2">
                        <span class="text-muted">{{ auth()->user()->name }}</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-power-off text-red"></i>
                        </a>
                        <form id="logout-form" action="{{ url('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                @endauth
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('dashboard') }}" wire:navigate class="brand-link">
                <span class="brand-text font-weight-light ml-2">{{ config('app.name') }}</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    @include('layouts.partials.sidebar-menu')
                </nav>
            </div>
        </aside>

        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <h1>{{ $title ?? '' }}</h1>
                </div>
            </div>

            <div class="content">
                <div class="container-fluid">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <footer class="main-footer">
            <strong>&copy; {{ date('Y') }} {{ config('app.name') }}.</strong> Tous droits réservés.
        </footer>

    </div>

    @livewireScripts
</body>
</html>
