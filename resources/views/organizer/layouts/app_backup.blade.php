<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Organizer Dashboard') - EventSphere</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @yield('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-6 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <img src="{{ asset('assets/images/eventsphere-icon.svg') }}" alt="EventSphere" class="h-8 w-8">
                            <h1 class="text-2xl font-bold text-gray-800">EventSphere - Organizer</h1>
                        </div>
                        <nav class="hidden lg:flex space-x-6">
                            <a href="{{ route('organizer.dashboard') }}" 
                               class="text-gray-600 hover:text-gray-900 {{ request()->routeIs('organizer.dashboard') ? 'text-blue-600 font-semibold' : '' }}">
                                Dashboard
                            </a>
                            <a href="{{ route('organizer.events.index') }}" 
                               class="text-gray-600 hover:text-gray-900 {{ request()->routeIs('organizer.events.*') ? 'text-blue-600 font-semibold' : '' }}">
                                My Events
                            </a>
                            <!-- Advanced Features Dropdown -->
                            <div class="relative group">
                                <button class="text-gray-600 hover:text-gray-900 flex items-center space-x-1 {{ request()->routeIs(['organizer.materials.*', 'organizer.attendance.*', 'organizer.qr-codes.*', 'organizer.certificates.*']) ? 'text-blue-600 font-semibold' : '' }}">
                                    <span>Advanced</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute top-full left-0 mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-200 invisible group-hover:visible group-hover:opacity-100 opacity-0 transition-all duration-200 z-50">
                                    <div class="py-2">
                                        <a href="{{ route('organizer.materials.index') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('organizer.materials.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                            Event Materials
                                        </a>
                                        <a href="{{ route('organizer.attendance.index') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('organizer.attendance.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                            </svg>
                                            Attendance
                                        </a>
                                        <a href="{{ route('organizer.qr-codes.index') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('organizer.qr-codes.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 16h4.01M12 8h4.01M12 8V4.01"/>
                                            </svg>
                                            QR Codes
                                        </a>
                                        <a href="{{ route('organizer.certificates.index') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('organizer.certificates.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            Certificates
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="{{ route('organizer.attendance.scanner') }}" 
                                           class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                            </svg>
                                            QR Scanner
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">
                            Welcome, <strong>{{ Auth::guard('organizer')->user()->org_name }}</strong>
                        </span>
                        
                        <form action="{{ route('organizer.logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                    class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto">
            @if(session('success'))
                <div class="mx-6 mt-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex">
                        <div class="text-green-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-green-800 font-medium">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="mx-6 mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex">
                        <div class="text-red-600">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-red-800 font-medium">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>