<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Event Organizer') - EventSphere</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    @yield('styles')
</head>
<body class="bg-gray-100">
    <!-- Header/Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center space-x-2">
                        <img src="{{ asset('assets/images/eventsphere-icon.svg') }}" alt="EventSphere" class="h-6 w-6">
                        <a href="{{ route('event-organizer.dashboard') }}" class="text-xl font-bold text-gray-800">
                            EventSphere
                        </a>
                    </div>
                </div>

                <div class="flex items-center">
                    @auth('event-organizer')
                        <div class="ml-3 relative">
                            <div>
                                <span class="text-gray-700">{{ Auth::guard('event-organizer')->user()->org_name }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('event-organizer.logout') }}" class="ml-4">
                            @csrf
                            <button type="submit" class="text-gray-500 hover:text-gray-700">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-6">
        @yield('content')
    </main>

    @yield('scripts')
</body>
</html>