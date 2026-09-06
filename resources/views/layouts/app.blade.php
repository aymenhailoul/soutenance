<!DOCTYPE html>
<html lang="fr" class="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ISS Maroc - Gestion du Parc Informatique')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('auto-style.png') }}">
    <script>
        // Apply saved theme before page renders to prevent flash
        (function () {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
            }
        })();
    </script>
    <style>
        /* Desktop Sidebar collapse/expand styles */
        @media (min-width: 768px) {
            .sidebar {
                width: 5rem;
                transition: width 0.3s ease;
            }

            .sidebar:hover {
                width: 18rem;
            }

            .sidebar .sidebar-label {
                opacity: 0;
                width: 0;
                transition: opacity 0.2s ease, width 0.3s ease;
            }

            .sidebar:hover .sidebar-label {
                opacity: 1;
                width: auto;
            }

            .sidebar .sidebar-title {
                opacity: 0;
                width: 0;
                overflow: hidden;
                transition: opacity 0.2s ease;
            }

            .sidebar:hover .sidebar-title {
                opacity: 1;
                width: auto;
            }

            .sidebar .logout-text {
                opacity: 0;
                width: 0;
                overflow: hidden;
                transition: opacity 0.2s ease;
            }

            .sidebar:hover .logout-text {
                opacity: 1;
                width: auto;
            }
        }

        /* Mobile Sidebar styles */
        @media (max-width: 767px) {
            .sidebar {
                width: 18rem;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar .sidebar-label,
            .sidebar .sidebar-title,
            .sidebar .logout-text {
                opacity: 1;
                width: auto;
            }

            .mobile-overlay {
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.3s ease, visibility 0.3s ease;
            }

            .mobile-overlay.open {
                opacity: 1;
                visibility: visible;
            }
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-200" x-data>
    <!-- Mobile Header -->
    <header
        class="md:hidden fixed top-0 left-0 right-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 z-40 px-4 py-3 flex items-center justify-between">
        <button id="mobile-menu-btn"
            class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
        </button>
        <div class="flex items-center gap-2">
            <img src="{{ asset('auto-style.png') }}" alt="ISS Maroc" class="w-8 h-8">
            <span class="font-bold text-gray-700 dark:text-gray-200">ISS MAROC</span>
        </div>
        <div class="w-10"></div>
    </header>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="mobile-overlay md:hidden fixed inset-0 bg-black/50 z-30"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar"
            class="sidebar bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col fixed h-full z-40 md:z-30 transition-colors duration-200">
            <!-- Close button for mobile -->
            <button id="mobile-close-btn"
                class="md:hidden absolute top-4 right-4 p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>

            <!-- Logo/Title -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-center">
                <img src="{{ asset('auto-style.png') }}" alt="ISS Maroc" class="w-10 h-10 flex-shrink-0">
                <h1 class="text-xl font-bold text-gray-700 dark:text-gray-200 ml-3 sidebar-title">ISS MAROC</h1>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-2 space-y-1 overflow-y-auto">
                @if(auth()->user()->hasPageAccess('dashboard'))
                    <x-nav-button label="Tableau de bord" route="{{ route('dashboard') }}"
                        :active="request()->routeIs('dashboard')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('equipment.index'))
                    <x-nav-button label="Équipements" route="{{ route('equipment.index') }}"
                        :active="request()->routeIs('equipment.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('categories.index'))
                    <x-nav-button label="Catégories" route="{{ route('categories.index') }}"
                        :active="request()->routeIs('categories.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 11h.01M7 15h.01M11 7h8M11 11h8M11 15h8M4 5h16a1 1 0 011 1v12a1 1 0 01-1 1H4a1 1 0 01-1-1V6a1 1 0 011-1z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('sites.index'))
                    <x-nav-button label="Sites" route="{{ route('sites.index') }}" :active="request()->routeIs('sites.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h6m-6 0V11m6 0V11m-6 0h6">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('assignments.index'))
                    <x-nav-button label="Affectations" route="{{ route('assignments.index') }}"
                        :active="request()->routeIs('assignments.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('maintenances.index'))
                    <x-nav-button label="Maintenance" route="{{ route('maintenances.index') }}"
                        :active="request()->routeIs('maintenances.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('stock.index'))
                    <x-nav-button label="Stock & Consommables" route="{{ route('stock.index') }}"
                        :active="request()->routeIs('stock.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('reports.index'))
                    <x-nav-button label="Rapports & Exports" route="{{ route('reports.index') }}"
                        :active="request()->routeIs('reports.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('backups.index'))
                    <x-nav-button label="Sauvegardes & Azurite" route="{{ route('backups.index') }}"
                        :active="request()->routeIs('backups.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7H5a2 2 0 00-2 2v9a2 2 0 00-2 2h14a2 2 0 00-2-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('clients.index'))
                    <x-nav-button label="Clients" route="{{ route('clients.index') }}"
                        :active="request()->routeIs('clients.*')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('employees.index'))
                    <x-nav-button label="Employés" route="/employees" :active="request()->is('employees')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('users.index'))
                    <x-nav-button label="Utilisateurs" route="/users" :active="request()->is('users')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                <!-- Custom Dynamic Pages (excluding standard system routes) -->
                @if(auth()->check())
                    @php
                        $excludedRoutes = [
                            'dashboard',
                            'equipment.index',
                            'categories.index',
                            'sites.index',
                            'assignments.index',
                            'maintenances.index',
                            'stock.index',
                            'stock.movements',
                            'stock.store',
                            'reports.index',
                            'reports.export.equipment',
                            'reports.export.assignments',
                            'backups.index',
                            'backups.store',
                            'backups.download',
                            'backups.destroy',
                            'clients.index',
                            'clients.store',
                            'clients.update',
                            'clients.destroy',
                            'employees.index',
                            'employees.store',
                            'employees.update',
                            'employees.destroy',
                            'employees.export',
                            'users.index',
                            'users.edit',
                            'users.store',
                            'users.update',
                            'users.destroy',
                            'pages.store',
                            'pages.update',
                            'pages.destroy',
                            'products.index',
                            'facturation.services.index',
                            'facturation.produits.index',
                            'retours.index',
                            'charges.index',
                        ];
                        $userPages = auth()->user()->isAdmin() ? \App\Models\Page::all() : auth()->user()->pages;
                    @endphp

                    @foreach($userPages as $page)
                        @if(!in_array($page->route, $excludedRoutes))
                            @php
                                $pageUrl = Route::has($page->route) ? route($page->route) : route('pages.test', ['page' => $page->route]);
                                $isActive = request()->routeIs($page->route) || request()->is('pages/test/' . $page->route);
                            @endphp
                            <x-nav-button :label="$page->name" :route="$pageUrl" :active="$isActive">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </x-nav-button>
                        @endif
                    @endforeach
                @endif
            </nav>

            <!-- Theme Toggle & Logout -->
            <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                <!-- Dark / Light Mode Toggle -->
                <button id="theme-toggle"
                    class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all font-medium mb-1"
                    title="Changer le thème">
                    <!-- Sun icon (shown in dark mode) -->
                    <svg id="theme-icon-light" class="w-5 h-5 flex-shrink-0 hidden dark:block" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <!-- Moon icon (shown in light mode) -->
                    <svg id="theme-icon-dark" class="w-5 h-5 flex-shrink-0 block dark:hidden" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z">
                        </path>
                    </svg>
                    <span class="logout-text whitespace-nowrap">
                        <span class="dark:hidden">Mode Sombre</span>
                        <span class="hidden dark:inline">Mode Clair</span>
                    </span>
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 transition-all font-medium"
                        title="Déconnexion">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span class="logout-text whitespace-nowrap">Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-0 md:ml-20 pt-16 md:pt-0 transition-all duration-300">
            @if (isset($header))
                <header class="bg-white dark:bg-gray-800 shadow mb-6">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Global Alert Banners for Success & Errors -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6 space-y-3">
                @if(session('success'))
                    <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-500 dark:text-emerald-300 rounded-r-lg flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="font-medium text-sm">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-900/30 dark:border-rose-500 dark:text-rose-300 rounded-r-lg flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium text-sm">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 dark:bg-rose-900/30 dark:border-rose-500 dark:text-rose-300 rounded-r-lg shadow-sm">
                        <div class="flex items-center gap-3 mb-2 font-semibold text-sm">
                            <svg class="w-5 h-5 text-rose-600 dark:text-rose-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Erreur / Attention : Veuillez corriger les points suivants</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-sm pl-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            @yield('content')
            {{ $slot ?? '' }}
        </main>
    </div>

    <!-- Mobile Menu & Theme Toggle JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const menuBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('mobile-close-btn');

            function openMenu() {
                if (sidebar && overlay) {
                    sidebar.classList.add('open');
                    overlay.classList.add('open');
                    document.body.style.overflow = 'hidden';
                }
            }

            function closeMenu() {
                if (sidebar && overlay) {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                    document.body.style.overflow = '';
                }
            }

            if (menuBtn) {
                menuBtn.addEventListener('click', openMenu);
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', closeMenu);
            }

            if (overlay) {
                overlay.addEventListener('click', closeMenu);
            }

            // Close menu on navigation link click (for mobile)
            if (sidebar) {
                const navLinks = sidebar.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function () {
                        if (window.innerWidth < 768) {
                            closeMenu();
                        }
                    });
                });
            }

            // Close menu on escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMenu();
                }
            });

            // Dark Mode / Light Mode Toggle
            const themeToggle = document.getElementById('theme-toggle');
            if (themeToggle) {
                themeToggle.addEventListener('click', function () {
                    const html = document.documentElement;
                    if (html.classList.contains('dark')) {
                        html.classList.remove('dark');
                        html.classList.add('light');
                        localStorage.setItem('theme', 'light');
                    } else {
                        html.classList.add('dark');
                        html.classList.remove('light');
                        localStorage.setItem('theme', 'dark');
                    }
                });
            }

            // Prevent Back-Forward Cache (BF Cache) from showing logged-out session on back button
            window.addEventListener('pageshow', function (event) {
                if (event.persisted || (window.performance && window.performance.navigation && window.performance.navigation.type === 2)) {
                    window.location.reload();
                }
            });
        });
    </script>
    @yield('scripts')
</body>

</html>