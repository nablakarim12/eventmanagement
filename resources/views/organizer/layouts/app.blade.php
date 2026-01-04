<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Organizer Dashboard') - ConVex</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @yield('styles')
</head>
<body class="bg-gray-50" x-data="{ 
    sidebarOpen: true, 
    myEventsDropdownOpen: false,
    innovationDropdownOpen: {{ request()->input('type') === 'innovation' || request()->is('organizer/registrations*') && request()->input('type') === 'innovation' ? 'true' : 'false' }},
    conferenceDropdownOpen: {{ request()->input('type') === 'conference' || request()->is('organizer/registrations*') && request()->input('type') === 'conference' ? 'true' : 'false' }}
}" x-init="
    // Keep Innovation dropdown open if on any innovation-related page
    if (window.location.search.includes('type=innovation')) {
        innovationDropdownOpen = true;
    }
    // Keep Conference dropdown open if on any conference-related page
    if (window.location.search.includes('type=conference')) {
        conferenceDropdownOpen = true;
    }
">
    <div class="min-h-screen flex">
        <!-- Main Sidebar -->
        <div class="bg-white shadow-lg transition-all duration-300 ease-in-out w-64" 
             x-show="sidebarOpen">
            <div class="flex items-center justify-start h-16 px-4 border-b">
                <a href="{{ route('organizer.dashboard') }}" 
                   class="hover:opacity-80 transition-opacity ml-4">
                    <img src="{{ asset('assets/images/logomain.png') }}" alt="ConVex Logo" class="h-10 w-auto">
                </a>
            </div>

            <nav class="mt-5 px-2">
                <div class="space-y-1">
                    <!-- My Events -->
                    <a href="{{ route('organizer.events.index') }}" 
                       class="{{ request()->routeIs('organizer.events.index') ? 'bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-calendar-check mr-3 {{ request()->routeIs('organizer.events.index') ? 'text-indigo-500' : '' }}"></i>
                        <span>My Events</span>
                    </a>

                    <!-- Innovation Dropdown -->
                    <div>
                        <button @click="innovationDropdownOpen = !innovationDropdownOpen" 
                                class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center justify-between w-full px-2 py-2 text-sm font-medium rounded-md">
                            <div class="flex items-center">
                                <i class="fas fa-lightbulb mr-3"></i>
                                <span>Innovation</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform" 
                               :class="innovationDropdownOpen ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="innovationDropdownOpen" 
                             x-cloak 
                             class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('organizer.registrations.index') }}?type=innovation" 
                               class="{{ request()->routeIs('organizer.registrations.*') && request()->input('type') === 'innovation' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-users mr-2 text-xs {{ request()->routeIs('organizer.registrations.*') && request()->input('type') === 'innovation' ? 'text-indigo-600' : '' }}"></i>
                                Registration
                            </a>
                            <a href="{{ route('organizer.payment-verification.index', ['type' => 'innovation']) }}" 
                               class="{{ request()->routeIs('organizer.payment-verification.*') && request()->input('type') === 'innovation' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-credit-card mr-2 text-xs {{ request()->routeIs('organizer.payment-verification.*') && request()->input('type') === 'innovation' ? 'text-indigo-600' : '' }}"></i>
                                Payment
                            </a>
                            <a href="{{ route('organizer.jury-mapping.index') }}?type=innovation" 
                               class="{{ request()->routeIs('organizer.jury-mapping.*') && request()->input('type') === 'innovation' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-user-tie mr-2 text-xs {{ request()->routeIs('organizer.jury-mapping.*') && request()->input('type') === 'innovation' ? 'text-indigo-600' : '' }}"></i>
                                Jury Mapping
                            </a>
                            <a href="{{ route('organizer.attendance.index') }}?type=innovation" 
                               class="{{ request()->routeIs('organizer.attendance.*') && request()->input('type') === 'innovation' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-clipboard-check mr-2 text-xs {{ request()->routeIs('organizer.attendance.*') && request()->input('type') === 'innovation' ? 'text-indigo-600' : '' }}"></i>
                                Attendance & QR Codes
                            </a>
                            <a href="{{ route('organizer.evaluation-results.index') }}?type=innovation" 
                               class="{{ request()->routeIs('organizer.evaluation-results.*') && request()->input('type') === 'innovation' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-chart-bar mr-2 text-xs {{ request()->routeIs('organizer.evaluation-results.*') && request()->input('type') === 'innovation' ? 'text-indigo-600' : '' }}"></i>
                                Evaluation Result
                            </a>
                            <a href="{{ route('organizer.simple-certificates.index') }}?type=innovation" 
                               class="{{ request()->routeIs('organizer.simple-certificates.*') && request()->input('type') === 'innovation' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-certificate mr-2 text-xs {{ request()->routeIs('organizer.simple-certificates.*') && request()->input('type') === 'innovation' ? 'text-indigo-600' : '' }}"></i>
                                Certificate
                            </a>
                            <a href="{{ route('organizer.feedback.index') }}?type=innovation" 
                               class="{{ request()->routeIs('organizer.feedback.*') && request()->input('type') === 'innovation' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-comment-dots mr-2 text-xs {{ request()->routeIs('organizer.feedback.*') && request()->input('type') === 'innovation' ? 'text-indigo-600' : '' }}"></i>
                                Feedback
                            </a>
                        </div>
                    </div>

                    <!-- Conference Dropdown -->
                    <div>
                        <button @click="conferenceDropdownOpen = !conferenceDropdownOpen" 
                                class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center justify-between w-full px-2 py-2 text-sm font-medium rounded-md">
                            <div class="flex items-center">
                                <i class="fas fa-building mr-3"></i>
                                <span>Conference</span>
                            </div>
                            <i class="fas fa-chevron-down transition-transform" 
                               :class="conferenceDropdownOpen ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="conferenceDropdownOpen" 
                             x-cloak 
                             class="ml-8 mt-1 space-y-1">
                            <a href="{{ route('organizer.registrations.index') }}?type=conference" 
                               class="{{ request()->routeIs('organizer.registrations.*') && request()->input('type') === 'conference' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-users mr-2 text-xs {{ request()->routeIs('organizer.registrations.*') && request()->input('type') === 'conference' ? 'text-indigo-600' : '' }}"></i>
                                Registration
                            </a>
                            <a href="{{ route('organizer.jury-mapping.index') }}?type=conference" 
                               class="{{ request()->routeIs('organizer.jury-mapping.*') && request()->input('type') === 'conference' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-user-tie mr-2 text-xs {{ request()->routeIs('organizer.jury-mapping.*') && request()->input('type') === 'conference' ? 'text-indigo-600' : '' }}"></i>
                                Jury Mapping
                            </a>
                            <a href="{{ route('organizer.payment-verification.index', ['type' => 'conference']) }}" 
                               class="{{ request()->routeIs('organizer.payment-verification.*') && request()->input('type') === 'conference' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-credit-card mr-2 text-xs {{ request()->routeIs('organizer.payment-verification.*') && request()->input('type') === 'conference' ? 'text-indigo-600' : '' }}"></i>
                                Payment
                            </a>
                            <a href="{{ route('organizer.evaluation-results.index') }}?type=conference" 
                               class="{{ request()->routeIs('organizer.evaluation-results.*') && request()->input('type') === 'conference' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-chart-bar mr-2 text-xs {{ request()->routeIs('organizer.evaluation-results.*') && request()->input('type') === 'conference' ? 'text-indigo-600' : '' }}"></i>
                                Evaluation Result
                            </a>
                            <a href="{{ route('organizer.attendance.index') }}?type=conference" 
                               class="{{ request()->routeIs('organizer.attendance.*') && request()->input('type') === 'conference' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-clipboard-check mr-2 text-xs {{ request()->routeIs('organizer.attendance.*') && request()->input('type') === 'conference' ? 'text-indigo-600' : '' }}"></i>
                                Attendance & QR Codes
                            </a>
                            <a href="{{ route('organizer.simple-certificates.index') }}?type=conference" 
                               class="{{ request()->routeIs('organizer.simple-certificates.*') && request()->input('type') === 'conference' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-certificate mr-2 text-xs {{ request()->routeIs('organizer.simple-certificates.*') && request()->input('type') === 'conference' ? 'text-indigo-600' : '' }}"></i>
                                Certificates
                            </a>
                            <a href="{{ route('organizer.feedback.index') }}?type=conference" 
                               class="{{ request()->routeIs('organizer.feedback.*') && request()->input('type') === 'conference' ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600' }} hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-xs rounded-md">
                                <i class="fas fa-comment-dots mr-2 text-xs {{ request()->routeIs('organizer.feedback.*') && request()->input('type') === 'conference' ? 'text-indigo-600' : '' }}"></i>
                                Feedback
                            </a>
                        </div>
                    </div>
                    
                    <!-- Create Event -->
                    <a href="{{ route('organizer.events.create') }}" 
                       class="{{ request()->routeIs('organizer.events.create') ? 'bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-plus-circle mr-3 {{ request()->routeIs('organizer.events.create') ? 'text-indigo-500' : '' }}"></i>
                        <span>Create Event</span>
                    </a>
                    
                    <!-- Analytics & More Section -->
                    <div class="mt-4 mb-2">
                        <h3 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wide">Analytics & More</h3>
                    </div>
                    <a href="{{ route('organizer.analytics.index') }}" 
                       class="{{ request()->routeIs('organizer.analytics.*') ? 'bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-chart-line mr-3 {{ request()->routeIs('organizer.analytics.*') ? 'text-indigo-500' : '' }}"></i>
                        <span>Analytics</span>
                    </a>
                    <a href="#" 
                       class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-dollar-sign mr-3"></i>
                        <span>Revenue</span>
                    </a>
                    <a href="#" 
                       class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-envelope mr-3"></i>
                        <span>Communications</span>
                    </a>
                    <a href="#" 
                       class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-cog mr-3"></i>
                        <span>Settings</span>
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top navigation -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none mr-3" title="Toggle Sidebar">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h2 class="text-2xl font-bold text-gray-900">@yield('page-title', 'Organizer Dashboard')</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Home and Change Password Icons -->
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('organizer.dashboard') }}" class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors" title="Home">
                                <i class="fas fa-home text-lg"></i>
                            </a>
                            <a href="{{ route('organizer.change-password') }}" class="p-2 text-gray-500 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-colors" title="Change Password">
                                <i class="fas fa-key text-lg"></i>
                            </a>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="flex items-center space-x-2 text-sm font-medium text-gray-700 hover:text-gray-900">
                                <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-xs"></i>
                                </div>
                                <span>{{ Auth::guard('organizer')->user()->org_name }}</span>
                            </button>
                            <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Profile
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Settings
                                </a>
                                <hr class="my-1">
                                <form action="{{ route('organizer.logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
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
    </div>

    @yield('scripts')
    @stack('scripts')
</body>
</html>