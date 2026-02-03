<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'FusionPBX Laravel UI')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="text-xl font-bold">FusionPBX UI</a>
                    </div>
                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('dashboard') ? 'border-b-2 border-white' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('domains.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('domains.*') ? 'border-b-2 border-white' : '' }}">
                            Domains
                        </a>
                        <a href="{{ route('extensions.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('extensions.*') ? 'border-b-2 border-white' : '' }}">
                            Extensions
                        </a>
                        <a href="{{ route('dids.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('dids.*') ? 'border-b-2 border-white' : '' }}">
                            DIDs
                        </a>
                        <a href="{{ route('campaigns.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('campaigns.*') ? 'border-b-2 border-white' : '' }}">
                            Campaigns
                        </a>
                        <a href="{{ route('dialer.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('dialer.*') ? 'border-b-2 border-white' : '' }}">
                            Dialer
                        </a>
                        <a href="{{ route('billing.balances.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium {{ request()->routeIs('billing.*') ? 'border-b-2 border-white' : '' }}">
                            Billing
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Validation Errors!</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <footer class="bg-white shadow mt-10">
        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-gray-500 text-sm">
                &copy; {{ date('Y') }} FusionPBX Laravel UI. Powered by Laravel {{ Illuminate\Foundation\Application::VERSION }} & PHP {{ PHP_VERSION }}
            </p>
        </div>
    </footer>
</body>
</html>
