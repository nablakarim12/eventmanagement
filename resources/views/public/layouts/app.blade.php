<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Events') - EventSphere</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @yield('styles')
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo and Main Navigation -->
                <div class="flex items-center">
                    <a href="{{ route('events.index') }}" class="flex items-center space-x-2">
                        <img src="{{ asset('assets/images/eventsphere-icon.svg') }}" alt="EventSphere" class="h-8 w-8">
                        <span class="text-2xl font-bold text-gray-800">EventSphere</span>
                    </a>
                    
                    <div class="hidden md:ml-8 md:flex md:space-x-8">
                        <a href="{{ route('events.index') }}" 
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium {{ request()->routeIs('events.index') ? 'text-blue-600 border-b-2 border-blue-600' : '' }}">
                            All Events
                        </a>
                        <a href="{{ route('events.category', 'academic-conference') }}" 
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                            Academic Conferences
                        </a>
                        <a href="{{ route('events.category', 'innovation-competition') }}" 
                           class="text-gray-700 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                            Innovation Competitions
                        </a>
                    </div>
                </div>

                <!-- Search and Auth Links -->
                <div class="flex items-center space-x-4">
                    <!-- Quick Search -->
                    <div class="hidden lg:block relative">
                        <input type="search" 
                               id="quickSearch" 
                               placeholder="Search events..." 
                               class="w-64 px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                        <div id="searchResults" class="absolute top-12 left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg hidden z-50 max-h-64 overflow-y-auto"></div>
                    </div>

                    <!-- Auth Links -->
                    <div class="flex items-center space-x-2 text-sm">
                        <a href="{{ route('organizer.login') }}" 
                           class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md font-medium">
                            Organizer Login
                        </a>
                        <a href="{{ route('admin.login') }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 font-medium">
                            Admin
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button class="md:hidden p-2 rounded-md text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div class="md:hidden bg-gray-50 border-t border-gray-200">
            <div class="px-4 py-3 space-y-2">
                <a href="{{ route('events.index') }}" 
                   class="block px-3 py-2 text-gray-700 hover:text-blue-600 font-medium">
                    All Events
                </a>
                <a href="{{ route('events.category', 'academic-conference') }}" 
                   class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                    Academic Conferences
                </a>
                <a href="{{ route('events.category', 'innovation-competition') }}" 
                   class="block px-3 py-2 text-gray-700 hover:text-blue-600">
                    Innovation Competitions
                </a>
                <!-- Mobile Search -->
                <div class="pt-2">
                    <input type="search" 
                           placeholder="Search events..." 
                           class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <img src="{{ asset('assets/images/eventsphere-icon.svg') }}" alt="EventSphere" class="h-8 w-8 brightness-0 invert">
                        <span class="text-2xl font-bold">EventSphere</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Smart Conference and Innovation Event System. Connecting organizers, 
                        researchers, and innovators through seamless event management.
                    </p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('events.index') }}" class="text-gray-400 hover:text-white">Browse Events</a></li>
                        <li><a href="{{ route('organizer.register') }}" class="text-gray-400 hover:text-white">Become Organizer</a></li>
                        <li><a href="{{ route('organizer.login') }}" class="text-gray-400 hover:text-white">Organizer Portal</a></li>
                        <li><a href="{{ route('admin.login') }}" class="text-gray-400 hover:text-white">Admin Panel</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <p class="text-gray-400">
                        Email: info@eventsphere.com<br>
                        Phone: (123) 456-7890
                    </p>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center">
                <p class="text-gray-400">&copy; {{ date('Y') }} EventSphere. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Quick Search Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('quickSearch');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();

                    if (query.length < 2) {
                        searchResults.classList.add('hidden');
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        fetch(`{{ route('events.search') }}?q=${encodeURIComponent(query)}`)
                            .then(response => response.json())
                            .then(data => {
                                displaySearchResults(data.results);
                            })
                            .catch(error => console.error('Search error:', error));
                    }, 300);
                });

                // Hide results when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                        searchResults.classList.add('hidden');
                    }
                });
            }

            function displaySearchResults(results) {
                if (results.length === 0) {
                    searchResults.innerHTML = '<div class="p-4 text-gray-500 text-sm">No events found</div>';
                } else {
                    searchResults.innerHTML = results.map(event => `
                        <a href="/events/${event.slug}" class="block p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                            <div class="font-medium text-gray-900">${event.title}</div>
                            <div class="text-sm text-gray-600">${event.category} • ${event.date} • ${event.city}</div>
                        </a>
                    `).join('');
                }
                searchResults.classList.remove('hidden');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>