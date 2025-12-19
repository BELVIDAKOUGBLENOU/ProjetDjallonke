@extends('layouts.app')
@section('pageTitle', 'Gestion géographique')
@push('scripts')
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/app_geographie.js'])
@endpush
@section('content')

    <div class="" id="appGeo"></div>
@endsection
