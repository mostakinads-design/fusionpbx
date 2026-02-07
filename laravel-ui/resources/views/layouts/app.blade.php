<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'FusionPBX') }} - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="bg-gray-900 text-white w-64 min-h-screen fixed lg:relative transition-transform duration-300 transform lg:transform-none z-50">
            <div class="p-6">
                <h1 class="text-2xl font-bold">{{ config('app.name', 'FusionPBX') }}</h1>
            </div>
            <nav class="mt-6">
                <a href="{{ route('dashboard') }}" class="block py-3 px-6 hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-tachometer-alt mr-3"></i> Dashboard
                </a>
                <a href="{{ route('users.index') }}" class="block py-3 px-6 hover:bg-gray-700 {{ request()->routeIs('users.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-users mr-3"></i> Users
                </a>
                <a href="{{ route('extensions.index') }}" class="block py-3 px-6 hover:bg-gray-700 {{ request()->routeIs('extensions.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-phone mr-3"></i> Extensions
                </a>
                <a href="{{ route('domains.index') }}" class="block py-3 px-6 hover:bg-gray-700 {{ request()->routeIs('domains.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-globe mr-3"></i> Domains
                </a>
                
                <div class="mt-4">
                    <p class="px-6 text-xs font-semibold text-gray-400 uppercase tracking-wider">Call Center</p>
                    <a href="{{ route('call-center-queues.index') }}" class="block py-3 px-6 hover:bg-gray-700 {{ request()->routeIs('call-center-queues.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-list mr-3"></i> Queues
                    </a>
                    <a href="{{ route('call-center-agents.index') }}" class="block py-3 px-6 hover:bg-gray-700 {{ request()->routeIs('call-center-agents.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                        <i class="fas fa-headset mr-3"></i> Agents
                    </a>
                </div>

                <a href="{{ route('campaigns.index') }}" class="block py-3 px-6 hover:bg-gray-700 {{ request()->routeIs('campaigns.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-bullhorn mr-3"></i> Campaigns
                </a>
                <a href="{{ route('cdr.index') }}" class="block py-3 px-6 hover:bg-gray-700 {{ request()->routeIs('cdr.*') ? 'bg-gray-700 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-phone-volume mr-3"></i> CDR
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm">
                <div class="flex items-center justify-between p-4">
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                    <h2 class="text-2xl font-semibold text-gray-800">@yield('title')</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-gray-600">{{ auth()->user()->name ?? 'Guest' }}</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-gray-900">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
            <div class="m-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p>{{ session('success') }}</p>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="m-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p>{{ session('error') }}</p>
                </div>
            </div>
            @endif

            @if($errors->any())
            <div class="m-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p class="font-semibold">Please fix the following errors:</p>
                </div>
                <ul class="list-disc list-inside ml-4">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Main Content Area -->
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        
        sidebarToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
        });

        // Close sidebar on mobile when clicking outside
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024 && 
                !sidebar.contains(e.target) && 
                !sidebarToggle.contains(e.target) && 
                !sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // Auto-hide flash messages after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
            alerts.forEach(alert => {
                if (!alert.classList.contains('border-red-500') || alert.querySelector('ul') === null) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);
    </script>
</body>
</html>
