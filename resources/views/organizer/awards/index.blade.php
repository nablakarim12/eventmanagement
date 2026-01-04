@extends('organizer.layouts.app')

@section('title', 'Award Management - ' . $event->title)
@section('page-title', 'Award Management')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Event Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $event->title }}</h2>
        <p class="text-gray-600">Manage awards and rankings for innovation participants</p>
    </div>

    <!-- Awards Setup -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Award Templates</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            @foreach($awards->where('is_mandatory', true) as $award)
            <div class="border-2 rounded-lg p-4" style="border-color: {{ $award->color }};">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold" style="background-color: {{ $award->color }};">
                        {{ $award->rank }}
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800">{{ $award->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $award->rank }}{{ $award->rank == 1 ? 'st' : ($award->rank == 2 ? 'nd' : 'rd') }} Place</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Custom Awards -->
        @if($awards->where('is_mandatory', false)->count() > 0)
        <div class="mb-4">
            <h4 class="font-semibold text-gray-700 mb-3">Custom Awards</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($awards->where('is_mandatory', false) as $award)
                <div class="border rounded-lg p-3 flex items-center justify-between">
                    <div>
                        <span class="font-semibold">{{ $award->name }}</span>
                        <span class="text-sm text-gray-600 ml-2">({{ $award->rank }}{{ $award->rank == 1 ? 'st' : ($award->rank == 2 ? 'nd' : ($award->rank == 3 ? 'rd' : 'th')) }} Place)</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Add Custom Award Button -->
        <button onclick="document.getElementById('addAwardModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            <i class="fas fa-plus mr-2"></i>Add Custom Award
        </button>
    </div>

    <!-- Ranking Scope Selection -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Auto-Suggest Rankings</h3>
        <p class="text-gray-600 mb-4">Select how you want to rank participants:</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <button onclick="autoSuggestRankings('category_theme')" class="border-2 border-blue-500 rounded-lg p-4 hover:bg-blue-50 text-left">
                <h4 class="font-bold text-gray-800 mb-2">Category + Theme</h4>
                <p class="text-sm text-gray-600">Rank within each category-theme combination</p>
                <p class="text-xs text-gray-500 mt-1">Example: "High School - AI" separate from "High School - IoT"</p>
            </button>

            <button onclick="autoSuggestRankings('category')" class="border-2 border-green-500 rounded-lg p-4 hover:bg-green-50 text-left">
                <h4 class="font-bold text-gray-800 mb-2">Category Only</h4>
                <p class="text-sm text-gray-600">Rank all themes within each category together</p>
                <p class="text-xs text-gray-500 mt-1">Example: All "High School" participants together</p>
            </button>

            <button onclick="autoSuggestRankings('theme')" class="border-2 border-purple-500 rounded-lg p-4 hover:bg-purple-50 text-left">
                <h4 class="font-bold text-gray-800 mb-2">Theme Only</h4>
                <p class="text-sm text-gray-600">Rank all categories within each theme together</p>
                <p class="text-xs text-gray-500 mt-1">Example: All "AI" participants across all categories</p>
            </button>

            <button onclick="autoSuggestRankings('overall')" class="border-2 border-red-500 rounded-lg p-4 hover:bg-red-50 text-left">
                <h4 class="font-bold text-gray-800 mb-2">Overall Ranking</h4>
                <p class="text-sm text-gray-600">Rank all participants together</p>
                <p class="text-xs text-gray-500 mt-1">One big competition across everything</p>
            </button>
        </div>

        <div id="suggestedRankings" class="hidden mt-6">
            <!-- Rankings will be displayed here -->
        </div>
    </div>

    <!-- Current Award Assignments -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Assigned Awards</h3>
            <div class="space-x-2">
                <form action="{{ route('organizer.awards.publish', $event) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700" onclick="return confirm('Publish all awards? Participants will be able to see their results.')">
                        <i class="fas fa-check mr-2"></i>Publish Awards
                    </button>
                </form>
                <form action="{{ route('organizer.awards.unpublish', $event) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        <i class="fas fa-eye-slash mr-2"></i>Unpublish
                    </button>
                </form>
            </div>
        </div>

        @if($participantAwards->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Participant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Award</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($participantAwards as $pa)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $pa->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $pa->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pa->category ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($pa->final_score, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded text-white text-sm" style="background-color: {{ $pa->award->color }};">
                                {{ $pa->award->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $pa->rank }}{{ $pa->rank == 1 ? 'st' : ($pa->rank == 2 ? 'nd' : ($pa->rank == 3 ? 'rd' : 'th')) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($pa->is_published)
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Published</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-8">No awards assigned yet. Use auto-suggest or manually assign awards.</p>
        @endif
    </div>
</div>

<!-- Add Award Modal -->
<div id="addAwardModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Add Custom Award</h3>
        <form action="{{ route('organizer.awards.store', $event) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Award Name</label>
                <input type="text" name="name" class="w-full px-3 py-2 border rounded" placeholder="e.g., Best Innovation" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Display Name (Optional)</label>
                <input type="text" name="display_name" class="w-full px-3 py-2 border rounded" placeholder="Alternative name">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Rank Position</label>
                <input type="number" name="rank" class="w-full px-3 py-2 border rounded" min="4" value="4" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Color</label>
                <input type="color" name="color" class="w-full px-3 py-2 border rounded" value="#4F46E5">
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                <textarea name="description" class="w-full px-3 py-2 border rounded" rows="3"></textarea>
            </div>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('addAwardModal').classList.add('hidden')" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Add Award</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function autoSuggestRankings(scope) {
    fetch(`{{ route('organizer.awards.auto-suggest', $event) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ ranking_scope: scope })
    })
    .then(response => response.json())
    .then(data => {
        displaySuggestedRankings(data.rankings, data.scope);
    })
    .catch(error => console.error('Error:', error));
}

function displaySuggestedRankings(rankings, scope) {
    const container = document.getElementById('suggestedRankings');
    container.classList.remove('hidden');
    
    let html = '<h4 class="font-bold text-lg mb-4">Suggested Rankings (' + scope.replace('_', ' + ') + ')</h4>';
    html += '<div class="space-y-6">';
    
    for (const [group, participants] of Object.entries(rankings)) {
        html += '<div class="border rounded-lg p-4">';
        html += '<h5 class="font-semibold mb-3 text-gray-700">' + group + '</h5>';
        html += '<div class="space-y-2">';
        
        participants.forEach((p, index) => {
            const medalColor = index === 0 ? '#FFD700' : (index === 1 ? '#C0C0C0' : (index === 2 ? '#CD7F32' : '#6B7280'));
            html += `
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold" style="background-color: ${medalColor}">
                            ${p.rank}
                        </div>
                        <div>
                            <div class="font-medium">${p.name}</div>
                            <div class="text-sm text-gray-600">Score: ${p.score}</div>
                        </div>
                    </div>
                    <button onclick="assignAward(${p.user_id}, ${p.registration_id}, ${p.rank}, '${scope}', ${p.score}, '${p.category}', '${p.theme}')" 
                            class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                        Assign
                    </button>
                </div>
            `;
        });
        
        html += '</div></div>';
    }
    
    html += '</div>';
    container.innerHTML = html;
}

function assignAward(userId, registrationId, rank, scope, score, category, theme) {
    // Determine award based on rank
    const awardId = rank; // Will need to map to actual award IDs
    
    fetch(`{{ route('organizer.awards.assign', $event) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            user_id: userId,
            registration_id: registrationId,
            award_id: awardId,
            final_score: score,
            category: category,
            theme: theme,
            ranking_scope: scope,
            rank: rank
        })
    })
    .then(response => response.json())
    .then(data => {
        location.reload();
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endpush
@endsection
