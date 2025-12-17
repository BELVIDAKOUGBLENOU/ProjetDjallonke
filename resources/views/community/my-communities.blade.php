@extends('layouts.app')
@section('pageTitle', 'My Communities')

@section('content')
    <div>
        <h1 class="text-lg font-semibold tracking-tight theme-title !mt-0">My Communities</h1>
        <p class="mb-6 text-sm theme-muted-text">Select a community to work in.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($communities as $community)
                <div class="theme-surface border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold theme-title">{{ $community->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $community->country?->name }}</p>
                        </div>
                        <span
                            class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-700/10">
                            {{ $community->pivot->role }}
                        </span>
                    </div>

                    <div class="mb-4">
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Created:</span> {{ $community->creation_date }}
                        </p>
                        <p class="text-sm text-gray-600">
                            <span class="font-medium">Members:</span>
                            {{ $community->members_count ?? $community->members()->count() }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <a href="{{ route('communities.show', $community->id) }}"
                            class="inline-flex justify-center items-center gap-2 rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                            Details
                        </a>
                        <form action="{{ route('select-community') }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="community_id" value="{{ $community->id }}">
                            <button type="submit"
                                class="w-full inline-flex justify-center items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                Select Workspace
                                @if (session('selected_community') == $community->id)
                                    <span class="ti ti-check"></span>
                                @endif
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="mx-auto h-12 w-12 text-gray-400">
                        <span class="ti ti-users text-4xl"></span>
                    </div>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900">No communities found</h3>
                    <p class="mt-1 text-sm text-gray-500">You are not a member of any community yet.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
