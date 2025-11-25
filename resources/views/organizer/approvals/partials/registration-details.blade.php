<div class="row">
    <div class="col-md-6">
        <h6 class="text-muted mb-3">User Information</h6>
        <dl class="row">
            <dt class="col-sm-4">Name:</dt>
            <dd class="col-sm-8">{{ $registration->user->name }}</dd>
            
            <dt class="col-sm-4">Email:</dt>
            <dd class="col-sm-8">{{ $registration->user->email }}</dd>
            
            @if($registration->user->phone)
            <dt class="col-sm-4">Phone:</dt>
            <dd class="col-sm-8">{{ $registration->user->phone }}</dd>
            @endif
            
            @if($registration->organization)
            <dt class="col-sm-4">Organization:</dt>
            <dd class="col-sm-8">{{ $registration->organization }}</dd>
            @endif
        </dl>
    </div>
    
    <div class="col-md-6">
        <h6 class="text-muted mb-3">Registration Details</h6>
        <dl class="row">
            <dt class="col-sm-4">Code:</dt>
            <dd class="col-sm-8">{{ $registration->registration_code ?? 'Not assigned' }}</dd>
            
            <dt class="col-sm-4">Role:</dt>
            <dd class="col-sm-8">
                @if($registration->role === 'both')
                    <span class="badge bg-primary">Participant & Jury</span>
                @else
                    <span class="badge {{ $registration->role === 'participant' ? 'bg-info' : 'bg-secondary' }}">
                        {{ ucfirst($registration->role) }}
                    </span>
                @endif
            </dd>
            
            <dt class="col-sm-4">Status:</dt>
            <dd class="col-sm-8">
                @switch($registration->approval_status)
                    @case('pending')
                        <span class="badge bg-warning">Pending Approval</span>
                        @break
                    @case('approved')
                        <span class="badge bg-success">Approved</span>
                        @break
                    @case('rejected')
                        <span class="badge bg-danger">Rejected</span>
                        @break
                @endswitch
            </dd>
            
            <dt class="col-sm-4">Registered:</dt>
            <dd class="col-sm-8">{{ $registration->registered_at->format('M j, Y g:i A') }}</dd>
        </dl>
    </div>
</div>

@if($registration->approved_at || $registration->rejected_at)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-muted mb-3">Approval History</h6>
        <div class="timeline">
            @if($registration->approved_at)
                <div class="timeline-item">
                    <div class="timeline-marker bg-success"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Approved</h6>
                        <p class="text-muted mb-0">{{ $registration->approved_at->format('M j, Y g:i A') }}</p>
                        @if($registration->approved_by)
                            <small class="text-muted">by Admin ID: {{ $registration->approved_by }}</small>
                        @endif
                    </div>
                </div>
            @endif
            
            @if($registration->rejected_at)
                <div class="timeline-item">
                    <div class="timeline-marker bg-danger"></div>
                    <div class="timeline-content">
                        <h6 class="mb-1">Rejected</h6>
                        <p class="text-muted mb-0">{{ $registration->rejected_at->format('M j, Y g:i A') }}</p>
                        @if($registration->rejected_reason)
                            <div class="mt-2">
                                <strong>Reason:</strong> {{ $registration->rejected_reason }}
                            </div>
                        @endif
                        @if($registration->approved_by)
                            <small class="text-muted">by Admin ID: {{ $registration->approved_by }}</small>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

@if($registration->payment_status !== 'pending' || $registration->amount_paid > 0)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-muted mb-3">Payment Information</h6>
        <dl class="row">
            <dt class="col-sm-3">Status:</dt>
            <dd class="col-sm-9">
                @if($registration->payment_status === 'completed')
                    <span class="badge bg-success">Paid</span>
                @else
                    <span class="badge bg-warning">{{ ucfirst($registration->payment_status) }}</span>
                @endif
            </dd>
            
            @if($registration->amount_paid > 0)
            <dt class="col-sm-3">Amount:</dt>
            <dd class="col-sm-9">${{ number_format($registration->amount_paid, 2) }}</dd>
            @endif
            
            @if($registration->payment_method)
            <dt class="col-sm-3">Method:</dt>
            <dd class="col-sm-9">{{ ucfirst($registration->payment_method) }}</dd>
            @endif
            
            @if($registration->payment_transaction_id)
            <dt class="col-sm-3">Transaction ID:</dt>
            <dd class="col-sm-9">{{ $registration->payment_transaction_id }}</dd>
            @endif
        </dl>
    </div>
</div>
@endif

@if($registration->special_requirements || $registration->dietary_restrictions || $registration->emergency_contact_name)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-muted mb-3">Additional Information</h6>
        
        @if($registration->special_requirements)
        <div class="mb-3">
            <strong>Special Requirements:</strong>
            <p class="text-muted mt-1">{{ $registration->special_requirements }}</p>
        </div>
        @endif
        
        @if($registration->dietary_restrictions)
        <div class="mb-3">
            <strong>Dietary Restrictions:</strong>
            <p class="text-muted mt-1">{{ $registration->dietary_restrictions }}</p>
        </div>
        @endif
        
        @if($registration->emergency_contact_name)
        <div class="mb-3">
            <strong>Emergency Contact:</strong>
            <p class="text-muted mt-1">
                {{ $registration->emergency_contact_name }}
                @if($registration->emergency_contact_phone)
                    - {{ $registration->emergency_contact_phone }}
                @endif
            </p>
        </div>
        @endif
    </div>
</div>
@endif

@if($registration->application_notes)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-muted mb-3">Application Notes</h6>
        <div class="bg-light p-3 rounded">
            {{ $registration->application_notes }}
        </div>
    </div>
</div>
@endif

@if($registration->admin_notes)
<div class="row mt-3">
    <div class="col-12">
        <h6 class="text-muted mb-3">Admin Notes</h6>
        <div class="bg-warning bg-soft p-3 rounded border border-warning">
            {{ $registration->admin_notes }}
        </div>
    </div>
</div>
@endif

<!-- Action Buttons -->
@if($registration->approval_status === 'pending')
<div class="row mt-4">
    <div class="col-12">
        <div class="d-flex gap-2 justify-content-end">
            <button type="button" class="btn btn-success" onclick="approveRegistration({{ $registration->id }})">
                <i class="ri-check-line me-1"></i>Approve
            </button>
            <button type="button" class="btn btn-danger" onclick="showRejectModal({{ $registration->id }})">
                <i class="ri-close-line me-1"></i>Reject
            </button>
        </div>
    </div>
</div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -30px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.timeline::before {
    content: '';
    position: absolute;
    left: -24px;
    top: 10px;
    bottom: 10px;
    width: 2px;
    background: #dee2e6;
}

.timeline-content h6 {
    margin-bottom: 5px;
}
</style>