@extends('layouts.app')
@section('pageTitle', 'Notifications')

@section('content')
    <div class=" mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">Notifications</h1>
                <p class="text-sm theme-muted-text">Restez informé des dernières activités.</p>
            </div>
            @if (auth()->user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-medium text-indigo-600 hover:text-indigo-700 hover:underline">
                        Tout marquer comme lu
                    </button>
                </form>
            @endif
        </div>

        <div class="theme-surface border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse ($notifications as $notification)
                    <div
                        class="p-4 hover:bg-gray-50 transition-colors {{ $notification->read_at ? 'opacity-75' : 'bg-indigo-50/30' }}">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                @if ($notification->read_at)
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-500">
                                        <i class="ti ti-mail-opened"></i>
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-sm font-medium text-gray-900 truncate">
                                        {{ $notification->data['title'] ?? 'Notification' }}
                                    </p>
                                    <span class="text-xs text-gray-500 whitespace-nowrap ml-2">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mb-2 line-clamp-2">
                                    {{ $notification->data['message'] ?? '' }}
                                </p>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('notifications.show', $notification->id) }}"
                                        class="text-xs font-medium text-indigo-600 hover:text-indigo-700">
                                        Voir les détails
                                    </a>
                                    @if (!$notification->read_at)
                                        <form action="{{ route('notifications.read', $notification->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-gray-500 hover:text-gray-700">
                                                Marquer comme lu
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <span
                            class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-4">
                            <i class="ti ti-bell-off text-xl"></i>
                        </span>
                        <h3 class="text-sm font-medium text-gray-900">Aucune notification</h3>
                        <p class="mt-1 text-sm text-gray-500">Vous n'avez pas de nouvelles notifications pour le moment.</p>
                    </div>
                @endforelse
            </div>

            @if ($notifications->hasPages())
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
