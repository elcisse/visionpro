@extends('adminlte::page')

@section('title', 'Test intégration')

@section('content_header')
    <h1>Test intégration Livewire + AdminLTE</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <livewire:test-counter />
        </div>
    </div>
@stop
