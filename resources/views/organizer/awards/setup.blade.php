@extends('organizer.layouts.app')

@section('title', 'Award Setup - ' . $event->title)
@section('page-title', 'Award Setup')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-trophy text-amber-500 mr-2"></i>{{ $event->title }}
                </h2>
                <p class="text-gray-600">Configure awards and medals for your innovation event</p>
            </div>
            <a href="{{ route('organizer.events.show', $event) }}" class="bg-white text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Event
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Mandatory Awards Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">
            <i class="fas fa-medal mr-2 text-amber-500"></i>Standard Awards
        </h3>
        <p class="text-gray-600 mb-4">These awards are mandatory and can be given to multiple participants</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($awards->where('is_mandatory', true) as $award)
            <div class="border-2 rounded-lg p-6 hover:shadow-lg transition-shadow" style="border-color: {{ $award->color }};">
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full flex items-center justify-center text-white font-bold text-2xl mb-3" style="background-color: {{ $award->color }};">
                        {{ $award->rank }}
                    </div>
                    <h4 class="font-bold text-gray-800 text-lg mb-1">{{ $award->name }}</h4>
                    <p class="text-sm text-gray-600 mb-2">{{ $award->rank }}{{ $award->rank == 1 ? 'st' : ($award->rank == 2 ? 'nd' : 'rd') }} Place</p>
                    <p class="text-xs text-gray-500">Can be awarded to multiple participants</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Custom Awards Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-star mr-2 text-purple-500"></i>Custom Awards
                </h3>
                <p class="text-gray-600 text-sm">Add special awards for your event</p>
            </div>
            <button onclick="document.getElementById('addAwardModal').classList.remove('hidden')" 
                    class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-2 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all">
                <i class="fas fa-plus mr-2"></i>Add Custom Award
            </button>
        </div>

        @if($awards->where('is_mandatory', false)->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($awards->where('is_mandatory', false) as $award)
            <div class="border-2 border-gray-200 rounded-lg p-4 hover:border-purple-300 hover:shadow-md transition-all">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" 
                             style="background-color: {{ $award->color ?? '#6B7280' }};">
                            {{ $award->rank }}
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $award->name }}</h4>
                            @if($award->display_name)
                            <p class="text-xs text-gray-500">{{ $award->display_name }}</p>
                            @endif
                        </div>
                    </div>
                    <form action="{{ route('organizer.awards.delete', [$event, $award]) }}" method="POST" 
                          onsubmit="return confirm('Delete this award?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </form>
                </div>
                @if($award->description)
                <p class="text-sm text-gray-600 mt-2">{{ $award->description }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <i class="fas fa-star text-gray-400 text-4xl mb-4"></i>
            <p class="text-gray-500">No custom awards yet</p>
            <p class="text-sm text-gray-400 mb-4">Add special awards like "Best Innovation", "People's Choice", etc.</p>
            <button onclick="document.getElementById('addAwardModal').classList.remove('hidden')" 
                    class="bg-purple-600 text-white px-6 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Your First Custom Award
            </button>
        </div>
        @endif
    </div>

    <!-- Award Scope Information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
        <h3 class="text-lg font-bold text-blue-900 mb-3">
            <i class="fas fa-info-circle mr-2"></i>How Awards Work
        </h3>
        <div class="space-y-2 text-sm text-blue-800">
            <p><strong>✓ Multiple Winners:</strong> Each award (Gold, Silver, Bronze, or custom) can be given to multiple participants</p>
            <p><strong>✓ Flexible Scopes:</strong> When assigning awards in Evaluation Results, you can choose:</p>
            <ul class="list-disc ml-8 mt-2 space-y-1">
                <li><strong>Category + Theme:</strong> Award per specific category-theme combination</li>
                <li><strong>Category Only:</strong> Award per category (all themes together)</li>
                <li><strong>Theme Only:</strong> Award per theme (all categories together)</li>
                <li><strong>Overall:</strong> Award across all participants</li>
            </ul>
            <p class="mt-3"><strong>Example:</strong> You could award Gold Medal to the top student in "High School - AI", another Gold to top in "University - IoT", etc.</p>
        </div>
    </div>

    <!-- Next Steps -->
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-bold text-green-900 mb-3">
            <i class="fas fa-check-circle mr-2"></i>Next Steps
        </h3>
        <div class="space-y-2 text-sm text-green-800">
            <p>1. <strong>Configure your awards above</strong> (Gold, Silver, Bronze are already set)</p>
            <p>2. <strong>Add any custom awards</strong> you want (optional)</p>
            <p>3. <strong>Wait for jury to complete evaluations</strong></p>
            <p>4. <strong>Go to Evaluation Results</strong> to assign awards to participants</p>
        </div>
        <div class="mt-4">
            <a href="{{ route('organizer.evaluation-results.show-event', $event) }}" 
               class="inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-chart-line mr-2"></i>Go to Evaluation Results
            </a>
        </div>
    </div>
</div>

<!-- Add Custom Award Modal -->
<div id="addAwardModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-lg bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-900">Add Custom Award</h3>
            <button onclick="document.getElementById('addAwardModal').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form action="{{ route('organizer.awards.store', $event) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Award Name *</label>
                    <input type="text" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" 
                           placeholder="e.g., Best Innovation, People's Choice" required>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Display Name (Optional)</label>
                    <input type="text" name="display_name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" 
                           placeholder="Alternative name for display">
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Rank Position *</label>
                    <input type="number" name="rank" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" 
                           min="1" value="1" required>
                    <p class="text-xs text-gray-500 mt-1">Rank determines display order. Multiple awards can have the same rank.</p>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Badge Color</label>
                    <div class="flex items-center space-x-2">
                        <input type="color" name="color" id="colorPicker" class="h-10 w-20 border border-gray-300 rounded cursor-pointer" value="#6B46C1">
                        <span id="colorValue" class="text-sm text-gray-600">#6B46C1</span>
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Description (Optional)</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500" 
                              placeholder="What is this award for?"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" onclick="document.getElementById('addAwardModal').classList.add('hidden')" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-2 rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all">
                    <i class="fas fa-plus mr-2"></i>Add Award
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Color picker preview
document.getElementById('colorPicker')?.addEventListener('input', function(e) {
    document.getElementById('colorValue').textContent = e.target.value;
});
</script>
@endpush
@endsection
