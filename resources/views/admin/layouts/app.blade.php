<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') - EventSphere</title>
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
        <div class="bg-gray-800 text-white w-80 py-6 flex flex-col">
            <!-- Logo -->
            <div class="px-8 pb-6 border-b border-gray-700">
                <div class="flex items-center space-x-2 mb-1">
                    <img src="{{ asset('assets/images/eventsphere-icon.svg') }}" alt="EventSphere" class="h-8 w-8 brightness-0 invert">
                    <h1 class="text-2xl font-bold">EventSphere Admin</h1>
                </div>
                <p class="text-sm text-gray-400 mt-1">Management System</p>
            </div>

            <!-- Manage Account Section -->
            <div class="px-8 py-6 border-b border-gray-700">
                <h2 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wider">Account Management</h2>
                <nav class="space-y-3">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <svg class="mr-4 h-5 w-5 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.categories.index') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <svg class="mr-4 h-5 w-5 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Event Categories
                    </a>
                </nav>
            </div>

            <!-- Manage Approval Account Section -->
            <div class="px-8 py-6 border-b border-gray-700">
                <h2 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wider">User Management</h2>
                <nav class="space-y-3">
                    @if(Route::has('admin.organizers.index'))
                    <a href="{{ route('admin.organizers.index') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <svg class="mr-4 h-5 w-5 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <svg class="mr-4 h-5 w-5 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <svg class="mr-4 h-5 w-5 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Event Participants
                    </a>
                </nav>
            </div>

            <!-- Advanced Features Section -->
            <div class="px-8 py-6 border-b border-gray-700">
                <h2 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wider">Advanced Features</h2>
                <nav class="space-y-3">
                    <a href="{{ route('admin.materials.index') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <i class="fas fa-file-alt mr-4 h-5 w-5 text-gray-400 group-hover:text-white"></i>
                        Event Materials
                    </a>
                    
                    <a href="{{ route('admin.attendance.index') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <i class="fas fa-clipboard-check mr-4 h-5 w-5 text-gray-400 group-hover:text-white"></i>
                        Attendance Management
                    </a>
                    
                    <a href="{{ route('admin.qr-codes.index') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <i class="fas fa-qrcode mr-4 h-5 w-5 text-gray-400 group-hover:text-white"></i>
                        QR Code Management
                    </a>
                    
                    <a href="{{ route('admin.certificates.index') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <i class="fas fa-certificate mr-4 h-5 w-5 text-gray-400 group-hover:text-white"></i>
                        Certificate Management
                    </a>
                </nav>
            </div>

            <!-- QR Scanner Section -->
            <div class="px-8 py-6 border-b border-gray-700">
                <h2 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wider">QR Scanner</h2>
                <nav class="space-y-3">
                    <a href="{{ route('admin.qr.scanner') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <i class="fas fa-camera mr-4 h-5 w-5 text-gray-400 group-hover:text-white"></i>
                        QR Code Scanner
                    </a>
                </nav>
            </div>

            <!-- Generate Reports Section -->
            <div class="px-8 py-6">
                <h2 class="text-sm font-semibold text-gray-300 mb-4 uppercase tracking-wider">Reports & Analytics</h2>
                <nav class="space-y-3">
                    <a href="{{ route('admin.reports.index') }}" 
                       class="flex items-center px-4 py-3 text-base text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg group transition-all duration-200">
                        <svg class="mr-4 h-5 w-5 text-gray-400 group-hover:text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        System Reports
                    </a>
                </nav>
            </div>

            <!-- User Info & Logout -->
            <div class="mt-auto px-8 py-6 border-t border-gray-700">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-gray-600 rounded-full flex items-center justify-center mr-3">
                        <span class="text-sm font-semibold">A</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium">Administrator</p>
                        <p class="text-xs text-gray-400">System Admin</p>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full px-4 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg transition-all duration-200 flex items-center justify-center">
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