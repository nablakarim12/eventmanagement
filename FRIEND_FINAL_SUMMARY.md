# FINAL SUMMARY FOR YOUR FRIEND

## TL;DR - What Your Friend MUST Know

### 🎯 Critical Facts:

1. **Paper Submission is ONLY for Academic Conferences**
   - Innovation Competitions: ❌ NO paper submission
   - Academic Conferences: ✅ YES paper submission required

2. **Role Names Differ by Event Type**
   - Innovation Competition: `participant`, `jury`, `both`
   - Academic Conference: `participant`, `reviewer`, `both`

3. **Same Database Fields for Both**
   - Both use same `role` field
   - Both use same `jury_*` qualification fields
   - Field names say "jury" but apply to reviewers too

---

## Forms Your Friend Must Build

### ✅ Form 1: Event Registration (Required for ALL Events)

**Show for:** Innovation Competitions AND Academic Conferences

**Fields:**
- Basic info (name, email, phone)
- Role selection (participant/jury/reviewer/both)
- **Conditional:** Qualification fields (if jury/reviewer/both selected)

**Dynamic Role Options:**
```javascript
if (eventType === 'Innovation Competition') {
    roleOptions = [
        'participant' => 'Participant (Present Ideas)',
        'jury' => 'Jury (Judge Presentations)',
        'both' => 'Both (Present & Judge)'
    ];
}

if (eventType === 'Academic Conference') {
    roleOptions = [
        'participant' => 'Participant (Submit & Present Paper)',
        'reviewer' => 'Reviewer (Review Papers)',
        'both' => 'Both (Submit & Review)'
    ];
}
```

---

### ✅ Form 2: Paper Submission (ONLY for Academic Conferences)

**Show for:** Academic Conferences ONLY  
**Hide for:** Innovation Competitions completely

**Access Control:**
```php
// Only show if ALL conditions met:
if ($event->category->name === 'Academic Conference' &&
    $registration->approval_status === 'approved' &&
    in_array($registration->role, ['participant', 'both'])) {
    // Show paper submission form
} else {
    // Hide completely (don't even show disabled button)
}
```

**Fields:**
- Title (required)
- Abstract (required, min 200 words)
- Keywords (required, min 3)
- PDF upload (required, max 10MB)
- Authors (dynamic, min 1, with corresponding author)

---

## Dashboard Views Your Friend Must Build

### 📊 Dashboard 1: My Registrations (for ALL event types)

```
┌─────────────────────────────────────┐
│ MY EVENT REGISTRATIONS              │
├─────────────────────────────────────┤
│                                     │
│ Innovation Competition:             │
│ ├─ Smart City Challenge            │
│ │   Role: Jury                      │
│ │   Status: ✓ Approved              │
│ │   Check-In: Not yet               │
│ │   [View Details] [QR Code]        │
│ │                                   │
│ └─ NO "Submit Paper" button         │
│                                     │
│ Academic Conference:                │
│ ├─ AI Research Symposium            │
│ │   Role: Both                      │
│ │   Status: ✓ Approved              │
│ │   Check-In: ✓ Checked In          │
│ │   [View Details] [QR Code]        │
│ │   [Submit Paper] ← Shows here!    │
│                                     │
└─────────────────────────────────────┘
```

---

### 📊 Dashboard 2: My Papers (ONLY for Academic Conferences)

**Show ONLY if:**
- Event is Academic Conference
- User role is 'participant' or 'both'

```
┌─────────────────────────────────────┐
│ MY SUBMITTED PAPERS                 │
├─────────────────────────────────────┤
│ AI Research Symposium               │
│ ├─ Paper: PAPER-ABC123              │
│ │   Title: "ML in Healthcare"       │
│ │   Status: Under Review            │
│ │   Reviews: 2/3 completed          │
│ │   Score: 8.5/10                   │
│ │   [View] [Download PDF]           │
│                                     │
│ [+ Submit New Paper]                │
└─────────────────────────────────────┘
```

**Do NOT show for Innovation Competitions!**

---

### 📊 Dashboard 3: Review Dashboard (for Jury/Reviewers AFTER check-in)

**Show ONLY if:**
- User role is 'jury' or 'reviewer' or 'both'
- User is checked in (`checked_in_at IS NOT NULL`)

#### For Innovation Competitions:
```
┌─────────────────────────────────────┐
│ JURY ASSIGNMENTS                    │
├─────────────────────────────────────┤
│ Smart City Challenge                │
│ ├─ You are assigned as Jury         │
│ │   Check-In: ✓ Checked In          │
│ │                                   │
│ │   Presentations to Judge: TBD     │
│ │   (Assignments made during event) │
│ │                                   │
│ │ Note: You will judge live         │
│ │ presentations during the event.   │
└─────────────────────────────────────┘
```

#### For Academic Conferences:
```
┌─────────────────────────────────────┐
│ REVIEW DASHBOARD                    │
├─────────────────────────────────────┤
│ AI Research Symposium               │
│ ├─ You are assigned as Reviewer     │
│ │   Check-In: ✓ Checked In          │
│ │                                   │
│ │ Assigned Papers:                  │
│ │ ├─ PAPER-ABC123                   │
│ │ │   "ML in Healthcare"            │
│ │ │   Status: ✓ Reviewed            │
│ │ │   [View Review]                 │
│ │                                   │
│ │ ├─ PAPER-DEF456                   │
│ │ │   "Deep Learning Methods"       │
│ │ │   Status: Pending Review        │
│ │ │   [Review Now]                  │
│ │                                   │
│ │ Review Deadline: Nov 30, 2025     │
└─────────────────────────────────────┘
```

---

## Code Example: Complete Implementation

```php
// ===== In Registration Controller =====

public function showRegistrationForm(Event $event)
{
    // Determine role options based on event type
    if ($event->category->name === 'Innovation Competition') {
        $roleOptions = [
            'participant' => 'Participant (Present Ideas)',
            'jury' => 'Jury (Judge Presentations)',
            'both' => 'Both (Present & Judge)',
        ];
        $evaluatorLabel = 'Jury';
    } else if ($event->category->name === 'Academic Conference') {
        $roleOptions = [
            'participant' => 'Participant (Submit & Present Paper)',
            'reviewer' => 'Reviewer (Review Papers)',
            'both' => 'Both (Submit & Review)',
        ];
        $evaluatorLabel = 'Reviewer';
    }
    
    return view('registration.form', compact('event', 'roleOptions', 'evaluatorLabel'));
}

public function storeRegistration(Request $request, Event $event)
{
    // Validate role
    $validRoles = ['participant', 'both'];
    
    if ($event->category->name === 'Innovation Competition') {
        $validRoles[] = 'jury';
    } else if ($event->category->name === 'Academic Conference') {
        $validRoles[] = 'reviewer';
    }
    
    $rules = [
        'role' => 'required|in:' . implode(',', $validRoles),
    ];
    
    // Add qualification validation for jury/reviewer
    if (in_array($request->role, ['jury', 'reviewer', 'both'])) {
        $rules = array_merge($rules, [
            'jury_qualification_summary' => 'required|min:100|max:1000',
            'jury_institution' => 'required|min:3|max:255',
            'jury_position' => 'required|min:3|max:255',
            'jury_years_experience' => 'required|integer|min:1',
            'jury_expertise_areas' => 'required|min:20|max:500',
        ]);
    }
    
    $validated = $request->validate($rules);
    
    // Create registration
    $registration = EventRegistration::create([
        'event_id' => $event->id,
        'user_id' => auth()->id(),
        'role' => $validated['role'],
        'approval_status' => 'pending',
        'registration_code' => 'REG-' . strtoupper(Str::random(8)),
        // Include qualification fields if present
        'jury_qualification_summary' => $validated['jury_qualification_summary'] ?? null,
        'jury_institution' => $validated['jury_institution'] ?? null,
        'jury_position' => $validated['jury_position'] ?? null,
        'jury_years_experience' => $validated['jury_years_experience'] ?? null,
        'jury_expertise_areas' => $validated['jury_expertise_areas'] ?? null,
    ]);
    
    return redirect()->route('registrations.show', $registration);
}

// ===== In Dashboard Controller =====

public function myRegistrations()
{
    $registrations = EventRegistration::where('user_id', auth()->id())
        ->with('event.category')
        ->get();
    
    // Group by event type
    $innovationRegs = $registrations->filter(function ($reg) {
        return $reg->event->category->name === 'Innovation Competition';
    });
    
    $conferenceRegs = $registrations->filter(function ($reg) {
        return $reg->event->category->name === 'Academic Conference';
    });
    
    return view('dashboard.registrations', compact('innovationRegs', 'conferenceRegs'));
}

public function myPapers()
{
    // Only get papers from Academic Conference events
    $papers = PaperSubmission::where('user_id', auth()->id())
        ->whereHas('event', function ($query) {
            $query->whereHas('category', function ($q) {
                $q->where('name', 'Academic Conference');
            });
        })
        ->with('event', 'authors')
        ->get();
    
    return view('dashboard.papers', compact('papers'));
}

public function reviewDashboard()
{
    $user = auth()->user();
    
    // Get all registrations where user is jury/reviewer and checked in
    $evaluatorRegs = EventRegistration::where('user_id', $user->id)
        ->whereIn('role', ['jury', 'reviewer', 'both'])
        ->whereNotNull('checked_in_at')
        ->where('approval_status', 'approved')
        ->with('event.category', 'juryAssignments.paperSubmission')
        ->get();
    
    // Separate by event type
    $innovationAssignments = [];
    $conferenceAssignments = [];
    
    foreach ($evaluatorRegs as $reg) {
        if ($reg->event->category->name === 'Innovation Competition') {
            $innovationAssignments[] = $reg;
        } else if ($reg->event->category->name === 'Academic Conference') {
            $conferenceAssignments[] = $reg;
        }
    }
    
    return view('dashboard.reviews', compact('innovationAssignments', 'conferenceAssignments'));
}

// ===== In Paper Controller =====

public function create(Event $event)
{
    // CRITICAL: Check if event supports paper submission
    if ($event->category->name !== 'Academic Conference') {
        return redirect()->back()->with('error', 
            'Paper submission is only available for Academic Conference events.');
    }
    
    // Check user registration
    $registration = EventRegistration::where('event_id', $event->id)
        ->where('user_id', auth()->id())
        ->where('approval_status', 'approved')
        ->whereIn('role', ['participant', 'both'])
        ->first();
    
    if (!$registration) {
        return redirect()->back()->with('error', 
            'You must be registered and approved as a participant to submit papers.');
    }
    
    return view('papers.create', compact('event', 'registration'));
}
```

---

## View Template Example

```blade
{{-- resources/views/registration/form.blade.php --}}

<form method="POST" action="{{ route('events.register', $event) }}">
    @csrf
    
    <h2>Register for {{ $event->title }}</h2>
    
    {{-- Basic Info --}}
    <div>
        <label>Name</label>
        <input type="text" value="{{ auth()->user()->name }}" readonly>
    </div>
    
    {{-- Role Selection --}}
    <div>
        <label>Select Your Role *</label>
        <select name="role" id="role" required>
            <option value="">-- Choose Role --</option>
            @foreach($roleOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </select>
    </div>
    
    {{-- Qualification Fields (shown conditionally) --}}
    <div id="qualifications" style="display: none;">
        <h3>{{ $evaluatorLabel }} Qualifications</h3>
        
        @if($event->category->name === 'Innovation Competition')
            <p>As a jury member, you will evaluate presentations during the event.</p>
        @else
            <p>As a reviewer, you will evaluate submitted papers before the conference.</p>
        @endif
        
        <div>
            <label>Qualification Summary *</label>
            <textarea name="jury_qualification_summary" rows="5"></textarea>
        </div>
        
        <div>
            <label>Institution *</label>
            <input type="text" name="jury_institution">
        </div>
        
        {{-- More qualification fields... --}}
    </div>
    
    <button type="submit">Complete Registration</button>
</form>

<script>
document.getElementById('role').addEventListener('change', function() {
    const qualDiv = document.getElementById('qualifications');
    
    // Show qualifications for jury/reviewer/both
    @if($event->category->name === 'Innovation Competition')
        if (this.value === 'jury' || this.value === 'both') {
            qualDiv.style.display = 'block';
        } else {
            qualDiv.style.display = 'none';
        }
    @else
        if (this.value === 'reviewer' || this.value === 'both') {
            qualDiv.style.display = 'block';
        } else {
            qualDiv.style.display = 'none';
        }
    @endif
});
</script>
```

```blade
{{-- resources/views/dashboard/registrations.blade.php --}}

<h1>My Event Registrations</h1>

@if($innovationRegs->count() > 0)
    <h2>Innovation Competitions</h2>
    @foreach($innovationRegs as $reg)
        <div class="registration-card">
            <h3>{{ $reg->event->title }}</h3>
            <p>Role: {{ ucfirst($reg->role) }}</p>
            <p>Status: {{ ucfirst($reg->approval_status) }}</p>
            
            {{-- NO paper submission button --}}
            <a href="{{ route('registrations.show', $reg) }}">View Details</a>
        </div>
    @endforeach
@endif

@if($conferenceRegs->count() > 0)
    <h2>Academic Conferences</h2>
    @foreach($conferenceRegs as $reg)
        <div class="registration-card">
            <h3>{{ $reg->event->title }}</h3>
            <p>Role: {{ ucfirst($reg->role) }}</p>
            <p>Status: {{ ucfirst($reg->approval_status) }}</p>
            
            {{-- Show paper submission if participant/both --}}
            @if(in_array($reg->role, ['participant', 'both']) && $reg->approval_status === 'approved')
                <a href="{{ route('papers.create', $reg->event) }}" class="btn-primary">
                    Submit Paper
                </a>
            @endif
            
            <a href="{{ route('registrations.show', $reg) }}">View Details</a>
        </div>
    @endforeach
@endif
```

---

## Testing Checklist for Your Friend

### ✅ Innovation Competition Testing:
- [ ] Registration shows "Jury" role option
- [ ] Jury qualification fields appear when jury/both selected
- [ ] Paper submission form is NOT available anywhere
- [ ] "Submit Paper" button does NOT show in dashboard
- [ ] Review dashboard shows "Judge presentations at event" message
- [ ] No paper list in user dashboard

### ✅ Academic Conference Testing:
- [ ] Registration shows "Reviewer" role option
- [ ] Reviewer qualification fields appear when reviewer/both selected
- [ ] Paper submission form IS available for participants
- [ ] "Submit Paper" button shows in dashboard
- [ ] Review dashboard shows list of assigned papers
- [ ] Paper list shows submitted papers with status

### ✅ Both Event Types:
- [ ] QR code displays correctly
- [ ] Check-in status updates properly
- [ ] Role badges display correctly
- [ ] Approval workflow works
- [ ] Email notifications sent

---

## Final Reminders

1. **Always check event category** before showing paper-related features
2. **Use correct terminology**: jury (innovation) vs reviewer (conference)
3. **Same qualification fields** for both roles
4. **Paper submission ONLY for conferences**, never for innovations
5. **Check-in required** before evaluation for both types
6. **Test both event types** thoroughly before deployment

---

Your friend now has everything needed to build the forms correctly! 🚀
