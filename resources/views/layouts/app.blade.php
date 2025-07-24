<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SEO Audit Pro - Complete SEO Analysis Tool')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'sans': ['Inter', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            50: '#f8fafc',
                            100: '#f1f5f9',
                            200: '#e2e8f0',
                            300: '#cbd5e1',
                            400: '#94a3b8',
                            500: '#64748b',
                            600: '#475569',
                            700: '#334155',
                            800: '#1e293b',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: false }">
        <!-- Sidebar -->
        <div class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0" 
             :class="{ '-translate-x-full': !sidebarOpen, 'translate-x-0': sidebarOpen }">
            
            <!-- Logo -->
            <div class="flex items-center justify-center h-16 bg-gradient-to-r from-primary-600 to-primary-700">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-search-plus text-white text-xl"></i>
                    <span class="text-white text-xl font-bold">SEO Audit Pro</span>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="mt-6 px-4 pb-6 overflow-y-auto">
                @auth
                <!-- User Info Section for Authenticated Users -->
                <div class="mb-6 px-3 py-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-primary-500 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            @if(auth()->user()->company)
                                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->company }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @else
                <!-- Demo Mode Indicator for Guests -->
                <div class="mb-6 px-3 py-3 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-eye text-amber-600"></i>
                        <div>
                            <p class="text-sm font-medium text-amber-800">Demo Mode</p>
                            <p class="text-xs text-amber-600">Sign up for real data tracking</p>
                        </div>
                    </div>
                    <div class="mt-3 space-y-2">
                        <a href="{{ route('register') }}" class="block w-full text-center py-2 px-3 bg-primary-600 text-white text-xs font-medium rounded hover:bg-primary-700 transition-colors">
                            Sign Up Free
                        </a>
                        <a href="{{ route('login') }}" class="block w-full text-center py-1 px-3 text-amber-700 text-xs hover:text-amber-800 transition-colors">
                            Already have an account?
                        </a>
                    </div>
                </div>
                @endauth
                
                <!-- Dashboard Section -->
                <div class="mb-8">
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">Dashboard</p>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="nav-link group {{ request()->routeIs('dashboard*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('dashboard*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-home text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Overview</span>
                            </div>
                        </a>
                    </div>
                </div>
                
                <!-- SEO Tools Section -->
                <div>
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">SEO Tools</p>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route('tools.site-audit') }}" class="nav-link group {{ request()->routeIs('tools.site-audit') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.site-audit') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-search text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Site Audit</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('tools.meta-analyzer') }}" class="nav-link group {{ request()->routeIs('tools.meta-analyzer') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.meta-analyzer') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-tags text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Meta Analyzer</span>
                            </div>
                        </a>

                         <a href="{{ route('tools.keyword-tracker') }}" class="nav-link group {{ request()->routeIs('tools.keyword-tracker') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.keyword-tracker') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-chart-line text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Keyword Tracker</span>
                            </div>
                        </a>

                        <!-- <a href="{{ route('tools.web-builder') }}" class="nav-link group {{ request()->routeIs('tools.web-builder') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.web-builder') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-link text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Web Builder</span>
                            </div>
                        </a> -->
                        
                        <a href="{{ route('tools.page-speed') }}" class="nav-link group {{ request()->routeIs('tools.page-speed') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.page-speed') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-tachometer-alt text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Page Speed</span>
                            </div>
                        </a>
                        
                     
                        
                        <a href="{{ route('tools.serp-preview') }}" class="nav-link group {{ request()->routeIs('tools.serp-preview') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.serp-preview') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-eye text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">SERP Preview</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('tools.lead-magnet-builder') }}" class="nav-link group {{ request()->routeIs('tools.lead-magnet-builder') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.lead-magnet-builder') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-magnet text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Lead Magnet Builder</span>
                            </div>
                        </a>
                        
                        <!-- <a href="{{ route('tools.sitemap-checker') }}" class="nav-link group {{ request()->routeIs('tools.sitemap-checker') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.sitemap-checker') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-sitemap text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Sitemap Checker</span>
                            </div>
                        </a> -->
                    </div>
                </div>
                
                <!-- Reports Section -->
                <!-- <div class="mt-8">
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">Reports</p>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route('tools.reports') }}" class="nav-link group {{ request()->routeIs('tools.reports') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('tools.reports') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-file-alt text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Reports</span>
                            </div>
                        </a>
                    </div>
                </div>
                 -->
                <!-- Competitor Analysis Section -->
                <div class="mt-8">
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">Competitor Analysis</p>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route('competitor-analysis.index') }}" class="nav-link group {{ request()->routeIs('competitor-analysis.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('competitor-analysis.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-chess text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Competitor Analysis</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Landing Pages Section -->
                <div class="mt-8">
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">Landing Pages</p>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route('landing-pages.index') }}" class="nav-link group {{ request()->routeIs('landing-pages.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('landing-pages.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-pager text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Page Builder</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('analytics.dashboard') }}" class="nav-link group {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('analytics.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-chart-line text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Analytics</span>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Business Promotions Section -->
                <div class="mt-8">
                    <div class="mb-3">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider px-2">Marketing</p>
                    </div>
                    
                    <div class="space-y-1">
                        <a href="{{ route('business-promotions.index') }}" class="nav-link group {{ request()->routeIs('business-promotions.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('business-promotions.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-bullhorn text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Promotions</span>
                            </div>
                        </a>
                        
                        <a href="{{ route('campaigns.index') }}" class="nav-link group {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg {{ request()->routeIs('campaigns.*') ? 'bg-primary-100 text-primary-600' : 'text-gray-400 group-hover:bg-gray-100 group-hover:text-gray-600' }} transition-all duration-200">
                                    <i class="fas fa-share-alt text-sm"></i>
                                </div>
                                <span class="ml-3 text-sm font-medium">Campaigns</span>
                            </div>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        
        <!-- Mobile sidebar overlay -->
        <div class="fixed inset-0 z-40 lg:hidden" x-show="sidebarOpen" x-cloak>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
        </div>
        
        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 flex-shrink-0">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                        <i class="fas fa-bars text-lg"></i>
                    </button>
                    
                    <!-- Page title -->
                    <div class="flex-1 lg:flex-none">
                        <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                    </div>
                    
                    <!-- Right side -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <!-- Authenticated User Menu -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100">
                                    <div class="w-8 h-8 bg-primary-500 rounded-full flex items-center justify-center">
                                        <span class="text-white text-sm font-semibold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                    <span class="hidden sm:block text-sm font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>
                                
                                <!-- Dropdown -->
                                <div x-show="open" x-cloak @click.away="open = false" 
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-50 border border-gray-200">
                                    <div class="px-4 py-2 border-b border-gray-100">
                                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                                        <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                                    </div>
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    <a href="{{ route('profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i> Settings
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}" class="block">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <!-- Guest User Menu -->
                            <div class="flex items-center space-x-3">
                                <!-- Demo User Indicator -->
                                <div class="flex items-center space-x-2 px-3 py-1 bg-amber-50 text-amber-700 rounded-full border border-amber-200">
                                    <i class="fas fa-eye text-xs"></i>
                                    <span class="text-xs font-medium">Demo Mode</span>
                                </div>
                                
                                <!-- Auth Buttons -->
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                                        Sign In
                                    </a>
                                    <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                                        Sign Up Free
                                    </a>
                                </div>
                            </div>
                        @endauth
                    </div>
                </div>
            </header>
            
            <!-- Page content -->
            <main class="flex-1 overflow-auto bg-gray-50">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <!-- Global Scripts -->
    <script>
        // CSRF Token setup for AJAX requests
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Global API helper function
        async function apiRequest(url, options = {}) {
            const defaultOptions = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin' // This ensures cookies/session are sent with the request
            };
            
            const mergedOptions = { ...defaultOptions, ...options };
            
            // If body is provided, default to POST method
            if (mergedOptions.body && typeof mergedOptions.body === 'object') {
                mergedOptions.body = JSON.stringify(mergedOptions.body);
                if (!options.method) {
                    mergedOptions.method = 'POST';
                }
            }
            
            try {
                const response = await fetch(url, mergedOptions);
                const data = await response.json();
                
                if (!response.ok) {
                    throw new Error(data.message || 'Request failed');
                }
                
                return data;
            } catch (error) {
                console.error('API Request Error:', error);
                throw error;
            }
        }
        
        // Notification system
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            } text-white fade-in`;
            
            notification.innerHTML = `
                <div class="flex items-center justify-between">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 5000);
        }
    </script>
    
    @stack('scripts')
    
    <style>
        .nav-link {
            @apply block px-4 py-3 mx-2 text-sm font-medium text-gray-600 rounded-lg transition-all duration-200 hover:bg-gray-50;
        }
        
        .nav-link.active {
            @apply bg-primary-50 text-primary-700 border-primary-500;
            box-shadow: inset 3px 0 0 theme('colors.primary.500');
        }
        
        .nav-link:hover:not(.active) {
            @apply bg-gray-50 text-gray-900;
        }
    </style>
</body>
</html>
