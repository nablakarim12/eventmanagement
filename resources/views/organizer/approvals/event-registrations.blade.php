@extends('organizer.layouts.app')

@section('title', 'Event Registrations - ' . $event->title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Event Registrations</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('organizer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('organizer.approvals.index') }}">Approvals</a></li>
                        <li class="breadcrumb-item active">{{ Str::limit($event->title, 30) }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="mb-2">{{ $event->title }}</h5>
                            <p class="text-muted mb-2">{{ Str::limit($event->description, 150) }}</p>
                            <div class="d-flex gap-3">
                                <small><i class="ri-calendar-line me-1"></i>{{ $event->start_date?->format('M j, Y') ?? 'Date TBA' }}</small>
                                <small><i class="ri-map-pin-line me-1"></i>{{ $event->venue_name ?? 'Venue TBA' }}</small>
                                @if($event->registration_deadline)
                                    <small><i class="ri-time-line me-1"></i>Deadline: {{ $event->registration_deadline->format('M j, Y') }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            @if($event->poster)
                                <img src="{{ asset('storage/events/posters/' . basename($event->poster)) }}" 
                                     alt="Event Poster" 
                                     class="img-thumbnail"
                                     style="height: 80px; width: auto;">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-primary bg-soft border-0">
                <div class="card-body text-center">
                    <h4 class="mb-1 text-primary">{{ $stats['total'] }}</h4>
                    <p class="text-muted mb-0">Total</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-warning bg-soft border-0">
                <div class="card-body text-center">
                    <h4 class="mb-1 text-warning">{{ $stats['pending'] }}</h4>
                    <p class="text-muted mb-0">Pending</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success bg-soft border-0">
                <div class="card-body text-center">
                    <h4 class="mb-1 text-success">{{ $stats['approved'] }}</h4>
                    <p class="text-muted mb-0">Approved</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-danger bg-soft border-0">
                <div class="card-body text-center">
                    <h4 class="mb-1 text-danger">{{ $stats['rejected'] }}</h4>
                    <p class="text-muted mb-0">Rejected</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-info bg-soft border-0">
                <div class="card-body text-center">
                    <h4 class="mb-1 text-info">{{ $stats['participants'] }}</h4>
                    <p class="text-muted mb-0">Participants</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-secondary bg-soft border-0">
                <div class="card-body text-center">
                    <h4 class="mb-1 text-secondary">{{ $stats['jury'] }}</h4>
                    <p class="text-muted mb-0">Jury</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Bulk Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="status" class="form-label">Approval Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>All Statuses</option>
                                <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="approved" {{ $status == 'approved' ? 'selected' : '' }}>Approved</option>
                                <option value="rejected" {{ $status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="roleFilter" class="form-label">Role</label>
                            <select name="role" id="roleFilter" class="form-select">
                                <option value="all" {{ $role == 'all' ? 'selected' : '' }}>All Roles</option>
                                <option value="participant" {{ $role == 'participant' ? 'selected' : '' }}>Participants</option>
                                <option value="jury" {{ $role == 'jury' ? 'selected' : '' }}>Jury</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary" onclick="applyFilters()">
                                    <i class="ri-search-line me-1"></i>Filter
                                </button>
                                <button type="button" class="btn btn-success" onclick="bulkApprove()" id="bulkApproveBtn" style="display: none;">
                                    <i class="ri-check-line me-1"></i>Approve Selected
                                </button>
                                <button type="button" class="btn btn-danger" onclick="showBulkRejectModal()" id="bulkRejectBtn" style="display: none;">
                                    <i class="ri-close-line me-1"></i>Reject Selected
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registrations Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($registrations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>Participant</th>
                                        <th>Role</th>
                                        <th>Registration Date</th>
                                        <th>Approval Status</th>
                                        <th>Payment</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($registrations as $registration)
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input registration-checkbox" 
                                                       value="{{ $registration->id }}" 
                                                       data-status="{{ $registration->approval_status }}">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar-sm">
                                                            <div class="avatar-title bg-light text-primary rounded-circle">
                                                                {{ substr($registration->user->name, 0, 1) }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">{{ $registration->user->name }}</h6>
                                                        <small class="text-muted">{{ $registration->user->email }}</small>
                                                        @if($registration->registration_code)
                                                            <div><small class="text-muted">Code: {{ $registration->registration_code }}</small></div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($registration->role === 'both')
                                                    <span class="badge bg-primary">Participant & Jury</span>
                                                @else
                                                    <span class="badge {{ $registration->role === 'participant' ? 'bg-info' : 'bg-secondary' }}">
                                                        {{ ucfirst($registration->role) }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $registration->registered_at->format('M j, Y g:i A') }}</small>
                                            </td>
                                            <td>
                                                @switch($registration->approval_status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">Pending</span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">Approved</span>
                                                        @if($registration->approved_at)
                                                            <small class="d-block text-muted">{{ $registration->approved_at->format('M j, g:i A') }}</small>
                                                        @endif
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @if($registration->rejected_at)
                                                            <small class="d-block text-muted">{{ $registration->rejected_at->format('M j, g:i A') }}</small>
                                                        @endif
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($registration->payment_status === 'completed')
                                                    <span class="badge bg-success">Paid</span>
                                                    @if($registration->amount_paid > 0)
                                                        <small class="d-block text-muted">${{ number_format($registration->amount_paid, 2) }}</small>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning">{{ ucfirst($registration->payment_status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="hstack gap-2">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" 
                                                            onclick="showRegistrationDetails({{ $registration->id }})"
                                                            data-bs-toggle="tooltip" title="View Details">
                                                        <i class="ri-eye-line"></i>
                                                    </button>
                                                    
                                                    @if($registration->approval_status === 'pending')
                                                        <button type="button" class="btn btn-success btn-sm" 
                                                                onclick="approveRegistration({{ $registration->id }})"
                                                                data-bs-toggle="tooltip" title="Approve">
                                                            <i class="ri-check-line"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                onclick="showRejectModal({{ $registration->id }})"
                                                                data-bs-toggle="tooltip" title="Reject">
                                                            <i class="ri-close-line"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($registrations->hasPages())
                            <div class="mt-3">
                                {{ $registrations->appends(request()->all())->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="ri-file-list-3-line display-4 text-muted mb-3"></i>
                            <h5 class="text-muted">No Registrations Found</h5>
                            <p class="text-muted">No registrations match the current filter criteria.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Registration Details Modal -->
<div class="modal fade" id="registrationDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registration Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="registrationDetailsContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Registration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejectReason" class="form-label">Rejection Reason (Optional)</label>
                        <textarea class="form-control" id="rejectReason" name="reason" rows="3" 
                                  placeholder="Provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Reject Modal -->
<div class="modal fade" id="bulkRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Reject Registrations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="bulkRejectForm">
                <div class="modal-body">
                    <p>You are about to reject <span id="bulkRejectCount">0</span> registrations.</p>
                    <div class="mb-3">
                        <label for="bulkRejectReason" class="form-label">Rejection Reason (Optional)</label>
                        <textarea class="form-control" id="bulkRejectReason" name="reason" rows="3" 
                                  placeholder="Provide a reason for rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedRegistrationId = null;

$(document).ready(function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle individual checkbox changes
    $('.registration-checkbox').change(function() {
        updateBulkActionButtons();
    });

    // Handle select all checkbox
    $('#selectAll').change(function() {
        $('.registration-checkbox').prop('checked', $(this).is(':checked'));
        updateBulkActionButtons();
    });

    // Handle reject form submission
    $('#rejectForm').submit(function(e) {
        e.preventDefault();
        
        if (!selectedRegistrationId) return;
        
        const reason = $('#rejectReason').val();
        rejectRegistration(selectedRegistrationId, reason);
    });

    // Handle bulk reject form submission
    $('#bulkRejectForm').submit(function(e) {
        e.preventDefault();
        
        const selectedIds = getSelectedRegistrationIds();
        const reason = $('#bulkRejectReason').val();
        
        if (selectedIds.length === 0) return;
        
        bulkRejectRegistrations(selectedIds, reason);
    });
});

function applyFilters() {
    const status = $('#status').val();
    const role = $('#roleFilter').val();
    
    const url = new URL(window.location.href);
    url.searchParams.set('status', status);
    url.searchParams.set('role', role);
    
    window.location.href = url.toString();
}

function updateBulkActionButtons() {
    const checkedBoxes = $('.registration-checkbox:checked');
    const pendingChecked = checkedBoxes.filter('[data-status="pending"]');
    
    if (pendingChecked.length > 0) {
        $('#bulkApproveBtn, #bulkRejectBtn').show();
    } else {
        $('#bulkApproveBtn, #bulkRejectBtn').hide();
    }
}

function getSelectedRegistrationIds() {
    return $('.registration-checkbox:checked').map(function() {
        return $(this).val();
    }).get();
}

function showRegistrationDetails(registrationId) {
    $.get(`/admin/approvals/registrations/${registrationId}`)
        .done(function(response) {
            $('#registrationDetailsContent').html(response.html);
            $('#registrationDetailsModal').modal('show');
        })
        .fail(function() {
            showAlert('error', 'Failed to load registration details');
        });
}

function approveRegistration(registrationId) {
    if (!confirm('Are you sure you want to approve this registration?')) return;
    
    $.post(`/admin/approvals/registrations/${registrationId}/approve`, {
        _token: '{{ csrf_token() }}'
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', response.message);
        }
    })
    .fail(function() {
        showAlert('error', 'Failed to approve registration');
    });
}

function showRejectModal(registrationId) {
    selectedRegistrationId = registrationId;
    $('#rejectReason').val('');
    $('#rejectModal').modal('show');
}

function rejectRegistration(registrationId, reason = '') {
    $.post(`/admin/approvals/registrations/${registrationId}/reject`, {
        _token: '{{ csrf_token() }}',
        reason: reason
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
            $('#rejectModal').modal('hide');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', response.message);
        }
    })
    .fail(function() {
        showAlert('error', 'Failed to reject registration');
    });
}

function bulkApprove() {
    const selectedIds = getSelectedRegistrationIds().filter((id, index, arr) => {
        const checkbox = $(`.registration-checkbox[value="${id}"]`);
        return checkbox.data('status') === 'pending';
    });
    
    if (selectedIds.length === 0) {
        showAlert('warning', 'Please select pending registrations to approve');
        return;
    }
    
    if (!confirm(`Are you sure you want to approve ${selectedIds.length} registrations?`)) return;
    
    $.post('/admin/approvals/bulk-approve', {
        _token: '{{ csrf_token() }}',
        registrations: selectedIds
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', response.message);
        }
    })
    .fail(function() {
        showAlert('error', 'Failed to approve registrations');
    });
}

function showBulkRejectModal() {
    const selectedIds = getSelectedRegistrationIds().filter((id, index, arr) => {
        const checkbox = $(`.registration-checkbox[value="${id}"]`);
        return checkbox.data('status') === 'pending';
    });
    
    if (selectedIds.length === 0) {
        showAlert('warning', 'Please select pending registrations to reject');
        return;
    }
    
    $('#bulkRejectCount').text(selectedIds.length);
    $('#bulkRejectReason').val('');
    $('#bulkRejectModal').modal('show');
}

function bulkRejectRegistrations(selectedIds, reason = '') {
    const pendingIds = selectedIds.filter((id) => {
        const checkbox = $(`.registration-checkbox[value="${id}"]`);
        return checkbox.data('status') === 'pending';
    });
    
    $.post('/admin/approvals/bulk-reject', {
        _token: '{{ csrf_token() }}',
        registrations: pendingIds,
        reason: reason
    })
    .done(function(response) {
        if (response.success) {
            showAlert('success', response.message);
            $('#bulkRejectModal').modal('hide');
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('error', response.message);
        }
    })
    .fail(function() {
        showAlert('error', 'Failed to reject registrations');
    });
}

function showAlert(type, message) {
    // Create alert element
    const alertClass = type === 'success' ? 'alert-success' : 
                     type === 'error' ? 'alert-danger' : 
                     type === 'warning' ? 'alert-warning' : 'alert-info';
    
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `);
    
    // Prepend to the container
    $('.container-fluid').prepend(alert);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alert.alert('close');
    }, 5000);
}
</script>
@endpush