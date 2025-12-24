<!-- Sidebar -->
<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 transform theme-surface  transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 flex flex-col"
    style="transform: translateX(-100%);">
    <!-- Sidebar Header -->
    <div class="flex h-16 items-center justify-between border-b theme-divider px-6">
        <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold theme-title">
            <img src="{{ asset('logo-dh.svg') }}" style="max-height: 20px;" alt="">
            <span class="text-nowrap">{{ config('app.name', 'D-HARVEST') }}</span>

        </a>
        <button onclick="window.__toggleSidebar()" class="theme-muted-text hover-theme-muted rounded-lg p-2 lg:hidden">
            <i class="ti ti-x text-2xl"></i>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">

        <a href="{{ route('home') }}"
            class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted"
            data-route="home">
            <i class="ti ti-dashboard text-xl"></i>
            <span>Tableau de bord</span>
        </a>


        <div class="pb-2 pt-4">
            <h3 class="px-3 text-xs font-semibold uppercase tracking-wider theme-muted-text">
                Parametrages
            </h3>
        </div>
        <a href="{{ route('geographic-management.index') }}"
            class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
            <i class="ti ti-map text-xl"></i>
            <span>Gestion Géographique</span>
        </a>
        @can('update ' . \App\Models\Country::getTableName())
            <a href="{{ route('countries.index') }}"
                class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
                <i class="ti ti-brand-google-maps text-xl"></i>
                <span>Gérer Pays Actifs</span>
            </a>
        @endcan

        @can('list ' . \App\Models\Community::getTableName())
            <a href="{{ route('communities.index') }}"
                class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
                <i class="ti ti-users text-xl"></i>
                <span>Communautés</span>
            </a>
        @endcan
        <a href="{{ route('my-communities') }}"
            class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
            <i class="ti ti-carousel-horizontal text-xl"></i>
            <span>Mes communautés</span>
        </a>

        @can('list ' . \App\Models\Premise::getTableName())
            <div class="pb-2 pt-4">
                <h3 class="px-3 text-xs font-semibold uppercase tracking-wider theme-muted-text">
                    Données
                </h3>
            </div>
            <a href="{{ route('premises.index') }}"
                class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
                <i class="ti ti-building-factory text-xl"></i>
                <span>Locaux</span>

            </a>
        @endcan
        @can('list ' . \App\Models\Person::getTableName())
            <a href="{{ route('people.index') }}"
                class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
                <i class="ti ti-user text-xl"></i>
                <span>Personnes</span>
            </a>
        @endcan
        @can('list ' . \App\Models\Animal::getTableName())
            <a href="{{ route('animals.index') }}"
                class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
                <i class="ti ti-deer text-xl"></i>
                <span>Animaux</span>
            </a>
        @endcan



        @if (auth()->user()?->hasRole('Super-admin') ?? false)
            <div class="pb-2 pt-4">
                <h3 class="px-3 text-xs font-semibold uppercase tracking-wider theme-muted-text">
                    Administrateur
                </h3>
            </div>


            <a href="{{ route('users.index') }}" data-route="users.index"
                class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
                <i class="ti ti-users text-xl"></i>
                <span>Utilisateurs</span>
            </a>
            <a href="{{ route('roles.index') }}" data-route="roles.index"
                class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
                <i class="ti ti-lock-access text-xl"></i>
                <span>Rôles</span>
            </a>
        @endif
        <div class="pb-2 pt-4">
            <h3 class="px-3 text-xs font-semibold uppercase tracking-wider theme-muted-text">
                Générale
            </h3>
        </div>
        {{-- //profile --}}
        <a href="{{ route('profile.edit') }}" data-route="profile.edit"
            class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
            <i class="ti ti-settings text-xl"></i>
            <span>Paramétrage du compte</span>
        </a>
        {{-- notifications --}}
        <a href="{{ route('notifications.index') }}" data-route="notifications.index"
            class="app-nav-item flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition theme-body hover-theme-muted">
            <i class="ti ti-bell text-xl"></i>
            <span class="flex-1">Notifications</span>
            @php
                $unreadCount = auth()->user()->unreadNotifications->count();
            @endphp
            @if ($unreadCount > 0)
                <span class="bg-red-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
            @endif
        </a>

    </nav>

    <!-- Sidebar Footer (sticky bottom) -->
    <div class="mt-auto border-t theme-divider p-4 relative group">
        <button type="button" onclick="document.getElementById('user-dropdown').classList.toggle('hidden')"
            class="flex w-full items-center gap-3 rounded-lg p-2  transition text-left hover:bg-gray-500/20">
            <div class="flex size-10 items-center justify-center rounded-full bg-primary-dark text-white shrink-0">
                <span class="font-semibold">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-medium theme-title">
                    {{ auth()->user()->name ?? 'Utilisateur' }}
                </p>
                <p class="truncate text-xs theme-muted-text">
                    {{ auth()->user()->email ?? 'user@example.com' }}
                </p>
            </div>
            <i class="ti ti-chevron-up text-gray-400"></i>
        </button>

        <!-- Dropup Menu -->
        <div id="user-dropdown"
            class="hidden absolute bottom-full left-0 w-[calc(100%-2rem)] mx-4 mb-2 rounded-xl border theme-divider theme-surface shadow-lg overflow-hidden z-50">
            <div class="py-1">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 px-4 py-2 text-sm theme-body hover:bg-gray-500/20">
                    <i class="ti ti-settings"></i>
                    Paramètres du compte
                </a>

                <form method="POST" action="{{ route('logout') }}"
                    onsubmit="return confirmDeletion(event, 'Déconnexion', 'Voulez-vous vraiment vous déconnecter ?')">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-2 px-4 py-2 text-sm text-red-600  text-left hover:bg-gray-500/20">
                        <i class="ti ti-logout"></i>
                        Déconnexion
                    </button>
                </form>
            </div>
        </div>

        <!-- Click outside to close -->
        <script>
            document.addEventListener('click', function(event) {
                const dropdown = document.getElementById('user-dropdown');
                const button = event.target.closest('button');
                const isDropdownButton = button && button.getAttribute('onclick') && button.getAttribute('onclick')
                    .includes('user-dropdown');

                if (!isDropdownButton && !dropdown.contains(event.target) && !dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            });
        </script>
    </div>
</aside>
