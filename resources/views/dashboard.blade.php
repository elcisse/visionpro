@extends('adminlte::page')

@section('title', 'Tableau de bord')

@section('content_header')
    <h1>Tableau de bord</h1>
@stop

@section('content')
    <div class="callout callout-info">
        Bienvenue {{ auth()->user()->name }}. Ce tableau de bord sera enrichi avec les indicateurs clés (recettes, taux d'utilisation du parc, factures impayées...).
    </div>
@stop
