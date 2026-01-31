<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Business Manager')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('auto-style.png') }}">
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

<body class="bg-gray-50">
    <!-- Mobile Header -->
    <header class="md:hidden fixed top-0 left-0 right-0 bg-white border-b border-gray-200 z-40 px-4 py-3 flex items-center justify-between">
        <button id="mobile-menu-btn" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <div class="flex items-center gap-2">
            <img src="{{ asset('auto-style.png') }}" alt="Auto Style" class="w-8 h-8">
            <span class="font-bold text-gray-700">AUTO STYLE</span>
        </div>
        <div class="w-10"></div> <!-- Spacer for centering -->
    </header>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="mobile-overlay md:hidden fixed inset-0 bg-black/50 z-30"></div>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar bg-white border-r border-gray-200 flex flex-col fixed h-full z-40 md:z-30">
            <!-- Close button for mobile -->
            <button id="mobile-close-btn" class="md:hidden absolute top-4 right-4 p-2 rounded-lg text-gray-600 hover:bg-gray-100">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Logo/Title -->
            <div class="p-4 border-b border-gray-200 flex items-center justify-center">
                <img src="{{ asset('auto-style.png') }}" alt="Auto Style" class="w-10 h-10 flex-shrink-0">
                <h1 class="text-xl font-bold text-gray-700 ml-3 sidebar-title">AUTO STYLE</h1>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-2 space-y-1 overflow-y-auto">
                @if(auth()->user()->hasPageAccess('dashboard'))
                    <x-nav-button label="Home" route="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('products.index'))
                    <x-nav-button label="Liste des produits" route="/products" :active="request()->is('products')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('stock.index'))
                    <x-nav-button label="Gestion du stock" route="/stock" :active="request()->is('stock')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('stock.movements'))
                    <x-nav-button label="Mouvements du stock" route="/stock/movements"
                        :active="request()->is('stock/movements')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('facturation.index'))
                    <x-nav-button label="Facturation" route="/facturation" :active="request()->is('facturation')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('ventes.index'))
                    <x-nav-button label="Ventes" route="/ventes" :active="request()->is('ventes')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('retours.index'))
                    <x-nav-button label="Retours" route="/retours" :active="request()->is('retours')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('clients.index'))
                    <x-nav-button label="Clients" route="/clients" :active="request()->is('clients')">
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
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('charges.index'))
                    <x-nav-button label="Charges" route="/charges" :active="request()->is('charges')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                @if(auth()->user()->hasPageAccess('users.index'))
                    <x-nav-button label="Users" route="/users" :active="request()->is('users')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                    </x-nav-button>
                @endif

                <!-- Dynamic Pages -->
                @php
                    $excludedRoutes = [
                        'dashboard',
                        'products.index',
                        'stock.index',
                        'stock.movements',
                        'facturation.index',
                        'ventes.index',
                        'retours.index',
                        'clients.index',
                        'employees.index',
                        'charges.index',
                        'users.index',
                        'products.create',
                        'dashboard.total_sales'
                    ];
                @endphp

                @foreach(auth()->user()->pages as $page)
                    @if(!in_array($page->route, $excludedRoutes) && Route::has($page->route))
                        <x-nav-button :label="$page->name" :route="route($page->route)"
                            :active="request()->routeIs($page->route)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </x-nav-button>
                    @endif
                @endforeach
            </nav>

            <!-- Logout Button -->
            <div class="p-2 border-t border-gray-200">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-3 w-full px-4 py-3 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition-all font-medium"
                        title="Logout">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span class="logout-text whitespace-nowrap">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-0 md:ml-20 pt-16 md:pt-0 transition-all duration-300">
            @yield('content')
        </main>
    </div>

    <!-- Mobile Menu JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('mobile-overlay');
            const menuBtn = document.getElementById('mobile-menu-btn');
            const closeBtn = document.getElementById('mobile-close-btn');

            function openMenu() {
                sidebar.classList.add('open');
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
            }

            function closeMenu() {
                sidebar.classList.remove('open');
                overlay.classList.remove('open');
                document.body.style.overflow = '';
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
            const navLinks = sidebar.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        closeMenu();
                    }
                });
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeMenu();
                }
            });
        });
    </script>
    @yield('scripts')
</body>

</html>