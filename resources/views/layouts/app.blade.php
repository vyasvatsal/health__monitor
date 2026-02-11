<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AI Store Monitor') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=outfit:300,400,500,600,700,800&family=jetbrains-mono:400,500&display=swap" rel="stylesheet" />
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Libraries -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            /* Theme Colors (Obsidian) */
            --bg-primary: 2, 6, 23;        /* #020617 */
            --text-primary: 226, 232, 240; /* #e2e8f0 */
            --sidebar-bg: 15, 23, 42;      /* #0f172a */
            --card-bg: 30, 41, 59;         /* #1e293b */
            --accent-emerald: 16, 185, 129;
            --accent-blue: 59, 130, 246;
        }

        body {
            background-color: rgb(var(--bg-primary));
            color: rgb(var(--text-primary));
            font-family: 'Outfit', sans-serif;
            overflow-x: hidden;
        }

        .font-mono { font-family: 'JetBrains Mono', monospace; }

        /* Sidebar Styling */
        .sidebar-modern {
            background: linear-gradient(180deg, rgb(var(--sidebar-bg)) 0%, rgb(2, 6, 23) 100%);
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }

        .sidebar-link {
            color: rgba(255, 255, 255, 0.6) !important;
            padding: 12px 16px;
            border-radius: 12px;
            transition: all 0.2s ease;
            margin: 4px 0;
            position: relative;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .sidebar-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white !important;
            transform: translateX(4px);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(6, 182, 212, 0.1));
            color: rgb(110, 231, 183) !important; /* Emerald 300 */
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .sidebar-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 10%; bottom: 10%;
            width: 3px;
            background: rgb(var(--accent-emerald));
            border-radius: 0 2px 2px 0;
        }

        /* Loading Spinner */
        .loading-spinner {
            width: 24px; height: 24px;
            border: 3px solid rgba(16, 185, 129, 0.1); /* Emerald tint */
            border-radius: 50%;
            border-top-color: rgb(var(--accent-emerald));
            animation: spin 1s ease-in-out infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        /* Scrollbar Hide */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>

<body class="antialiased">
    <div x-data="{
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
        isDesktop: window.innerWidth >= 1024,
        toggleSidebar() {
            if (this.isDesktop) {
                this.sidebarCollapsed = !this.sidebarCollapsed;
                localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed);
            } else {
                this.sidebarOpen = !this.sidebarOpen;
            }
            // Trigger resize for charts
            setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
        },
        handleResize() {
            this.isDesktop = window.innerWidth >= 1024;
            if (this.isDesktop) this.sidebarOpen = false;
        }
    }"
    @resize.window="handleResize()"
    @close-sidebar.window="sidebarOpen = false"
    class="flex h-screen w-full overflow-hidden">

        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-black/80 backdrop-blur-sm lg:hidden transition-opacity duration-300"
             x-transition:enter="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        <!-- SIDEBAR -->
        <aside x-show="sidebarOpen || isDesktop"
            :class="{
                'w-20': sidebarCollapsed && isDesktop,
                'w-72': !sidebarCollapsed || !isDesktop,
                'fixed z-50 inset-y-0 left-0 lg:relative': true,
                '-translate-x-full': !sidebarOpen && !isDesktop
            }"
            class="sidebar-modern flex flex-col h-full transition-all duration-300">
            
            <!-- Sidebar Header -->
            <div class="flex items-center h-16 px-6 transition-all duration-300" 
                 :class="sidebarCollapsed ? 'justify-center px-0' : 'justify-between'">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/20 flex items-center justify-center flex-shrink-0 shadow-lg shadow-emerald-500/5">
                        <i class="material-icons text-emerald-400 text-sm">health_and_safety</i>
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex flex-col overflow-hidden">
                        <span class="text-white font-bold whitespace-nowrap tracking-tight">Health Monitor</span>
                        <span class="text-[10px] text-emerald-400/80 uppercase tracking-wider font-semibold">System Optimal</span>
                    </div>
                </div>
                <!-- Mobile Close -->
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white transition-colors">
                    <i class="material-icons">close</i>
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto no-scrollbar">
                @foreach([
                        ['url' => route('dashboard'), 'icon' => 'dashboard', 'label' => 'Dashboard', 'route' => 'dashboard'],
                        ['url' => route('stores.index'), 'icon' => 'inventory_2', 'label' => 'Projects', 'route' => 'stores*'],
                        ['url' => route('benchmarks.index'), 'icon' => 'analytics', 'label' => 'Benchmarks', 'route' => 'benchmarks*'],
                        ['url' => route('incidents.index'), 'icon' => 'warning', 'label' => 'Incidents', 'route' => 'incidents*'],
                    ] as $item)
                    <a href="{{ $item['url'] }}" 
                       class="sidebar-link group {{ request()->is($item['route']) || request()->routeIs($item['route']) ? 'active' : '' }}"
                       :class="sidebarCollapsed && isDesktop ? 'justify-center px-2' : ''"
                       title="{{ $item['label'] }}">
                        <i class="material-icons text-[20px] transition-all duration-300" 
                           :class="sidebarCollapsed && isDesktop ? 'mr-0' : 'mr-3'">{{ $item['icon'] }}</i>
                        <span x-show="!sidebarCollapsed" 
                              class="whitespace-nowrap transition-opacity duration-200">{{ $item['label'] }}</span>

                        <!-- Tooltip (Collapsed) -->
                        <div x-show="sidebarCollapsed && isDesktop"
                            class="absolute left-full top-1/2 -translate-y-1/2 ml-3 px-2 py-1 bg-slate-800 text-white text-xs rounded border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50 whitespace-nowrap shadow-xl">
                            {{ $item['label'] }}
                        </div>
                    </a>
                @endforeach

                <div class="my-4 border-t border-white/5"></div>

                 <!-- Settings -->
                <a href="{{ route('settings.alerts') }}" 
                   class="sidebar-link group {{ request()->routeIs('settings.*') ? 'active' : '' }}"
                   :class="sidebarCollapsed && isDesktop ? 'justify-center px-2' : ''"
                   title="Settings">
                    <i class="material-icons text-[20px] transition-all duration-300" 
                       :class="sidebarCollapsed && isDesktop ? 'mr-0' : 'mr-3'">settings</i>
                    <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Settings</span>
                    
                    <div x-show="sidebarCollapsed && isDesktop"
                        class="absolute left-full top-1/2 -translate-y-1/2 ml-3 px-2 py-1 bg-slate-800 text-white text-xs rounded border border-white/10 opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50 whitespace-nowrap shadow-xl">
                        Settings
                    </div>
                </a>
            </nav>
            
            <!-- Footer (Profile) -->
            <div class="p-4 border-t border-white/5 bg-black/20">
                <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-9 h-9 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-sm shadow-lg shadow-indigo-500/20 flex-shrink-0">
                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div x-show="!sidebarCollapsed" class="flex flex-col min-w-0">
                            <span class="text-sm font-medium text-white truncate w-32">{{ Auth::user()->name ?? 'User' }}</span>
                            <span class="text-xs text-slate-400 truncate w-32">{{ Auth::user()->email ?? '' }}</span>
                        </div>
                    </div>
                    <form x-show="!sidebarCollapsed" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-white transition-colors p-1" title="Logout">
                            <i class="material-icons text-lg">logout</i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <div class="flex-1 flex flex-col min-w-0 h-full relative">
            <!-- Background Grid (Obsidian Theme) -->
            <div class="absolute inset-0 z-0 pointer-events-none"
                 style="background-image: linear-gradient(rgba(255,255,255,0.01) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.01) 1px, transparent 1px); background-size: 40px 40px;">
            </div>

            <!-- Top Header -->
            <header class="h-16 bg-[#020617]/80 backdrop-blur-md border-b border-white/5 flex items-center justify-between px-6 sticky top-0 z-30">
                <div class="flex items-center gap-4">
                    <button @click="toggleSidebar()" class="text-slate-400 hover:text-white transition-colors p-1 rounded-lg hover:bg-white/5">
                        <i class="material-icons" x-text="!isDesktop || sidebarCollapsed ? 'menu' : 'menu_open'"></i>
                    </button>
                    
                    <!-- Dynamic Breadcrumbs / Title Placeholder -->
                    <div class="hidden sm:block text-sm text-slate-400" id="header-title">
                        @if(request()->routeIs('dashboard')) <span class="text-emerald-400 font-medium">Dashboard</span>
                        @else <span class="text-slate-200">{{ $header ?? '' }}</span> @endif
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                     <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/10">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-xs font-mono text-emerald-400 font-medium">LIVE</span>
                    </div>
                </div>
            </header>

            <!-- Main Scrollable Area -->
            <main class="flex-1 overflow-y-auto relative z-10 p-6 scroll-smooth" id="app-main">
                <div id="main-content" class="max-w-[1600px] mx-auto fade-in">
                    <!-- Content Injection -->
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </main>
        </div>
    </div>

    <!-- AJAX Navigation Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Using delegation for Sidebar Links
            $(document).on('click', '.sidebar-link', function(e) {
                // Check if it's a real link
                const url = $(this).attr('href');
                if (!url || url === '#' || url.startsWith('javascript')) return;

                e.preventDefault();
                const $link = $(this);

                // UI Updates
                $('.sidebar-link').removeClass('active');
                $link.addClass('active'); // Note: Server-side rendering handles initial state, this handles immediate click.
                
                // Update Browser URL
                history.pushState(null, '', url);

                // Show Loading
                $('#main-content').html(`
                    <div class="flex flex-col items-center justify-center p-20 h-96">
                        <div class="loading-spinner mb-4"></div>
                        <span class="text-slate-400 text-sm font-medium animate-pulse">Loading data...</span>
                    </div>
                `);

                // Scroll to top
                document.getElementById('app-main').scrollTo(0,0);

                // AJAX Request
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        try {
                            // Extract content
                            const html = $('<div>').html(response);
                            const content = html.find('#main-content').html();
                            
                            // 1. Cleanup old charts to prevent memory leaks
                            if (window.myCharts) {
                                window.myCharts.forEach(c => {
                                    if (typeof c.destroy === 'function') c.destroy();
                                });
                                window.myCharts = [];
                            }
                            
                            // 2. Inject New Content
                            // If #main-content not found in response, fallback to full response
                            if (content) {
                                $('#main-content').html(content);
                            } else {
                                // This handles case where "extends" might not be wrapping #main-content correctly in the response 
                                // (shouldn't happen if we update dashboard correctly)
                                $('#main-content').html(response);
                            }

                            // 3. Close mobile sidebar
                            if (window.innerWidth < 1024) {
                                window.dispatchEvent(new CustomEvent('close-sidebar'));
                            }
                        } catch (err) {
                            console.error("Error parsing AJAX response:", err);
                            window.location.reload(); // Fallback
                        }
                    },
                    error: function(xhr) {
                        console.error("AJAX Error:", xhr);
                         $('#main-content').html(`
                            <div class="flex flex-col items-center justify-center p-20">
                                <i class="material-icons text-red-400 text-4xl mb-4">error_outline</i>
                                <h3 class="text-white text-lg font-medium">Connection Error</h3>
                                <button onclick="window.location.reload()" class="mt-4 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white rounded-lg transition-colors">
                                    Reload Page
                                </button>
                            </div>
                        `);
                    }
                });
            });

            // Handle Back/Forward buttons
            window.addEventListener('popstate', () => location.reload());
        });
    </script>
</body>
</html>