<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'FusionPBX Admin Panel')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
    
    <!-- Tailwind CSS CDN for quick setup -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js for charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-indigo-600 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-white text-xl font-bold">
                                🎯 FusionPBX Admin Panel
                            </a>
                        </div>
                        <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                            <a href="{{ route('dashboard') }}" class="text-white hover:bg-indigo-700 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center {{ request()->routeIs('dashboard') ? 'bg-indigo-700' : '' }}">
                                📊 Dashboard
                            </a>
                            <a href="{{ route('cdr.index') }}" class="text-white hover:bg-indigo-700 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center {{ request()->routeIs('cdr.*') ? 'bg-indigo-700' : '' }}">
                                📞 CDR Records
                            </a>
                            <a href="{{ route('ai.index') }}" class="text-white hover:bg-indigo-700 px-3 py-2 rounded-md text-sm font-medium inline-flex items-center {{ request()->routeIs('ai.*') ? 'bg-indigo-700' : '' }}">
                                🤖 AI Agent
                            </a>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <span class="text-white text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-500 text-white">
                                🟢 Online
                            </span>
                        </span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    &copy; {{ date('Y') }} FusionPBX Admin Panel with AI Integration. Powered by Laravel & OpenAI.
                </p>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
