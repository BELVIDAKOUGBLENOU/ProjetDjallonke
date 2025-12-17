
@extends('layouts.app-d')
@section('pageTitle', 'Modifier la/le Sub District')

@section('content')
    <div class="active-nav-custom-url" url="{{ route('sub-districts.index') }}"></div>

    <div class="">
        {{-- Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-xl font-semibold tracking-tight theme-title">Modifier la/le  Sub District</h1>
                <p class="mt-0 text-sm theme-muted-text">Mettez à jour les informations de la/le  Sub District.</p>

            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('sub-districts.index') }}"
                    class="flex items-center gap-2 rounded-md border border-gray-300 theme-surface theme-title px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-500">
                    <span class="ti ti-arrow-left text-base"></span>
                    Retour
                </a>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="theme-surface theme-title border border-gray-200/70 rounded-xl shadow-sm px-6 py-4">
            <form method="POST" action="{{ route('sub-districts.update', $subDistrict->id) }}" enctype="multipart/form-data" class="">
                {{ method_field('PATCH') }}
                @csrf

                @include('sub-district.form')

                <div class="flex items-center justify-start gap-3 mt-3 pt-4 border-t border-dashed border-gray-200">
                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-md bg-slate-500 px-5 py-2 text-sm font-medium text-white shadow-sm  hover:bg-slate-500 focus:outline-none focus:ring-2 focus:ring-slate-500">
                        <span class="ti ti-device-floppy text-base"></span>
                        Enregistrer
                    </button>
                    <a href="{{ route('sub-districts.index') }}"
                        class="inline-flex items-center gap-2 rounded-md px-4 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 border">
                        Annuler
                    </a>

                </div>
            </form>
        </div>
    </div>
@endsection
