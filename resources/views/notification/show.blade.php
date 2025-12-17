@extends('layouts.app')
@section('pageTitle', 'Détail de la notification')

@section('content')
    <div class=" mx-auto">
        <div class="mb-6">
            <a href="{{ route('notifications.index') }}"
                class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 mb-4">
                <i class="ti ti-arrow-left"></i>
                Retour aux notifications
            </a>
            <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">
                {{ $notification->data['title'] ?? 'Détail de la notification' }}
            </h1>
            <p class="text-sm theme-muted-text">
                Reçu {{ $notification->created_at->diffForHumans() }}
            </p>
        </div>

        <div class="theme-surface border border-gray-200 rounded-xl shadow-sm p-6">
            <div class="prose prose-sm max-w-none text-gray-600">
                <p>{{ $notification->data['message'] ?? 'Aucun contenu disponible.' }}</p>

                @if (isset($notification->data['action_url']))
                    <div class="mt-6">
                        <a href="{{ $notification->data['action_url'] }}"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ $notification->data['action_text'] ?? 'Voir l\'action' }}
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between items-center text-xs text-gray-500">
                <span>ID: {{ $notification->id }}</span>
                <span>{{ $notification->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
@endsection
