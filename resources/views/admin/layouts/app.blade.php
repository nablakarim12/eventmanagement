<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - ConVex</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js for analytics -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @yield('styles')
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-white shadow-lg text-gray-900 w-64 flex flex-col">
            <!-- Logo -->
            <div class="flex items-center justify-start h-16 px-4 border-b">
                <a href="{{ route('admin.dashboard') }}" 
                   class="hover:opacity-80 transition-opacity ml-4">
                    <img src="{{ asset('assets/images/logomain.png') }}" alt="ConVex Logo" class="h-10 w-auto">
                </a>
            </div>

            <!-- Admin Badge -->
            <div class="px-6 py-3 bg-indigo-50 border-b border-indigo-100">
                <div class="flex items-center justify-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-600 text-white">
                        <svg class="mr-1.5 h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Administrator Account
                    </span>
                </div>
            </div>

            <!-- Manage Account Section -->
            <div class="px-2 py-5 border-b border-gray-200">
                <h2 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Account Management</h2>
                <nav class="space-y-1">
                    <a href="{{ route('admin.categories.index') }}" 
                       class="{{ request()->routeIs('admin.categories.*') ? 'bg-indigo-50 text-indigo-700 border-r-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.categories.*') ? 'text-indigo-500' : '' }} group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Event Categories
                    </a>
                </nav>
            </div>

            <!-- Manage Approval Account Section -->
            <div class="px-2 py-5 border-b border-gray-200">
                <h2 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">User Management</h2>
                <nav class="space-y-1">
                    @if(Route::has('admin.organizers.index'))
                    <a href="{{ route('admin.organizers.index') }}" 
                       class="{{ request()->routeIs('admin.organizers.*') ? 'bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.organizers.*') ? 'text-indigo-500' : '' }} group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="flex-1">Event Organizers</span>
                        @php
                            $pendingOrganizers = \App\Models\EventOrganizer::where('status', 'pending')->count();
                        @endphp
                        @if($pendingOrganizers > 0)
                        <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingOrganizers }}</span>
                        @endif
                    </a>
                    @endif

                    @if(Route::has('admin.approvals.index'))
                    <a href="{{ route('admin.approvals.index') }}" 
                       class="{{ request()->routeIs('admin.approvals.*') ? 'bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.approvals.*') ? 'text-indigo-500' : '' }} group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="flex-1">Registration Approvals</span>
                        @php
                            $pendingApprovals = \App\Models\EventRegistration::where('approval_status', 'pending')->count();
                        @endphp
                        @if($pendingApprovals > 0)
                        <span class="bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingApprovals }}</span>
                        @endif
                    </a>
                    @endif

                    <a href="#" 
                       class="text-gray-600 hover:bg-gray-50 hover:text-gray-900 group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Event Participants
                    </a>
                </nav>
            </div>

            <!-- Advanced Features Section -->
            <div class="px-2 py-5 border-b border-gray-200">
                <h2 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Advanced Features</h2>
                <nav class="space-y-1">
                    <a href="{{ route('admin.materials.index') }}" 
                       class="{{ request()->routeIs('admin.materials.*') ? 'bg-indigo-50 text-indigo-700 border-r-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-file-alt mr-3 {{ request()->routeIs('admin.materials.*') ? 'text-indigo-500' : '' }} group-hover:text-indigo-500"></i>
                        Event Materials
                    </a>
                    
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="{{ request()->routeIs('admin.attendance.*') ? 'bg-indigo-50 text-indigo-700 border-r-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-clipboard-check mr-3 {{ request()->routeIs('admin.attendance.*') ? 'text-indigo-500' : '' }} group-hover:text-indigo-500"></i>
                        Attendance Management
                    </a>
                    
                    <a href="{{ route('admin.qr-codes.index') }}" 
                       class="{{ request()->routeIs('admin.qr-codes.*') ? 'bg-indigo-50 text-indigo-700 border-r-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-qrcode mr-3 {{ request()->routeIs('admin.qr-codes.*') ? 'text-indigo-500' : '' }} group-hover:text-indigo-500"></i>
                        QR Code Management
                    </a>
                    
                    <a href="{{ route('admin.certificates.index') }}" 
                       class="{{ request()->routeIs('admin.certificates.*') ? 'bg-indigo-50 text-indigo-700 border-r-4 border-indigo-500' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-certificate mr-3 {{ request()->routeIs('admin.certificates.*') ? 'text-indigo-500' : '' }} group-hover:text-indigo-500"></i>
                        Certificate Management
                    </a>
                </nav>
            </div>

            <!-- QR Scanner Section -->
            <div class="px-2 py-5 border-b border-gray-200">
                <h2 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">QR Scanner</h2>
                <nav class="space-y-1">
                    <a href="{{ route('admin.qr.scanner') }}" 
                       class="{{ request()->routeIs('admin.qr.scanner') ? 'bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <i class="fas fa-camera mr-3 {{ request()->routeIs('admin.qr.scanner') ? 'text-indigo-500' : '' }}"></i>
                        QR Code Scanner
                    </a>
                </nav>
            </div>

            <!-- Generate Reports Section -->
            <div class="px-2 py-5">
                <h2 class="px-2 text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Reports & Analytics</h2>
                <nav class="space-y-1">
                    <a href="{{ route('admin.reports.index') }}" 
                       class="{{ request()->routeIs('admin.reports.*') ? 'bg-indigo-50 border-r-4 border-indigo-500 text-indigo-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('admin.reports.*') ? 'text-indigo-500' : '' }} group-hover:text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        System Reports
                    </a>
                </nav>
            </div>

            <!-- User Info & Logout -->
            <div class="mt-auto px-2 py-3 border-t border-gray-200">
                <div class="flex items-center mb-2 px-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-sm font-semibold text-indigo-600">A</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Administrator</p>
                        <p class="text-xs text-gray-500">System Admin</p>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST" class="px-2">
                    @csrf
                    <button type="submit" class="w-full px-2 py-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-md flex items-center justify-center font-medium">
                        <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Navigation -->
            <nav class="bg-white shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex items-center">
                            <h2 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @yield('scripts')
</body>
</html>