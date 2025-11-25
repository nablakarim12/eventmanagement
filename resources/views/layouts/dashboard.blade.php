<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - EventSphere</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left side -->
                <div class="flex items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <img src="https://via.placeholder.com/40x40/3B82F6/FFFFFF?text=E" alt="EventSphere" class="h-8 w-8 rounded-lg">
                            <span class="ml-2 text-xl font-bold text-gray-900">EventSphere</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:ml-8 md:flex md:space-x-8">
                        <a href="{{ route('dashboard') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('dashboard') ? 'border-blue-500 text-blue-600' : '' }}">
                            <i class="fas fa-tachometer-alt mr-1"></i>Dashboard
                        </a>
                        <a href="{{ route('dashboard.registrations') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('dashboard.registrations*') ? 'border-blue-500 text-blue-600' : '' }}">
                            <i class="fas fa-calendar-check mr-1"></i>My Events
                        </a>
                        <a href="{{ route('events.index') }}" class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            <i class="fas fa-search mr-1"></i>Browse Events
                        </a>
                    </div>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- Notifications -->
                    <button class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full">
                        <i class="fas fa-bell text-lg"></i>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative ml-3" x-data="{ open: false }">
                        <button @click="open = !open" class="max-w-xs bg-white flex items-center text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <img class="h-8 w-8 rounded-full bg-gray-300" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3B82F6&color=fff" alt="{{ Auth::user()->name }}">
                            <span class="ml-2 text-gray-700 font-medium hidden sm:block">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ml-2 text-gray-400 text-xs"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <a href="{{ route('dashboard.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile Settings
                                </a>
                                <a href="{{ route('dashboard.registrations') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-calendar-check mr-2"></i>My Registrations
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="md:hidden" x-data="{ open: false }">
            <div class="px-4 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900 block px-3 py-2 text-base font-medium {{ request()->routeIs('dashboard') ? 'text-blue-600' : '' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </a>
                <a href="{{ route('dashboard.registrations') }}" class="text-gray-600 hover:text-gray-900 block px-3 py-2 text-base font-medium {{ request()->routeIs('dashboard.registrations*') ? 'text-blue-600' : '' }}">
                    <i class="fas fa-calendar-check mr-2"></i>My Events
                </a>
                <a href="{{ route('events.index') }}" class="text-gray-600 hover:text-gray-900 block px-3 py-2 text-base font-medium">
                    <i class="fas fa-search mr-2"></i>Browse Events
                </a>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">{{ session('info') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} EventSphere. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    @yield('scripts')
</body>
</html>