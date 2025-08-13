@extends('layouts.app')

@section('title', 'Lead Details - Mini CRM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Lead Details</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            @if(auth()->user()->isAdmin() || $lead->assigned_to === auth()->id())
            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Lead
            </a>
            @endif
            @if(auth()->user()->isAdmin())
            <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline"
                  onsubmit="return confirm('Are you sure you want to delete this lead?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger me-2">
                    <i class="fas fa-trash me-2"></i>Delete Lead
                </button>
            </form>
            @endif
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Leads
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Lead Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Lead Information</h6>
                    <span class="badge bg-{{ $lead->status === 'new' ? 'primary' : ($lead->status === 'contacted' ? 'warning' : 'success') }} fs-6">
                        {{ ucfirst($lead->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Full Name</label>
                            <p class="form-control-plaintext fw-bold">{{ $lead->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Email Address</label>
                            <p class="form-control-plaintext">
                                <a href="mailto:{{ $lead->email }}" class="text-decoration-none">{{ $lead->email }}</a>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Phone Number</label>
                            <p class="form-control-plaintext">
                                @if($lead->phone)
                                    <a href="tel:{{ $lead->phone }}" class="text-decoration-none">{{ $lead->phone }}</a>
                                @else
                                    <span class="text-muted">Not provided</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Assigned To</label>
                            <p class="form-control-plaintext">
                                @if($lead->assignedUser)
                                    <span class="badge bg-info">{{ $lead->assignedUser->name }}</span>
                                    <small class="text-muted d-block">{{ ucfirst($lead->assignedUser->role) }}</small>
                                @else
                                    <span class="text-muted">Unassigned</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($lead->notes)
                    <div class="mb-3">
                        <label class="form-label text-muted small">Notes</label>
                        <div class="form-control-plaintext bg-light p-3 rounded">
                            {{ $lead->notes }}
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Created</label>
                            <p class="form-control-plaintext">{{ $lead->created_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted small">Last Updated</label>
                            <p class="form-control-plaintext">{{ $lead->updated_at->format('F d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Status -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($lead->phone)
                        <a href="tel:{{ $lead->phone }}" class="btn btn-success">
                            <i class="fas fa-phone me-2"></i>Call Lead
                        </a>
                        @endif

                        <a href="mailto:{{ $lead->phone }}" class="btn btn-primary">
                            <i class="fas fa-envelope me-2"></i>Email Lead
                        </a>

                        @if(auth()->user()->isAdmin() || $lead->assigned_to === auth()->id())
                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit Lead
                        </a>
                        @endif

                        @if(auth()->user()->isAdmin())
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-2"></i>Delete Lead
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Status History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status History</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Lead Created</h6>
                                <p class="timeline-text text-muted">{{ $lead->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        @if($lead->status !== 'new')
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Status Changed</h6>
                                <p class="timeline-text text-muted">Changed to {{ ucfirst($lead->status) }}</p>
                            </div>
                        </div>
                        @endif

                        @if($lead->assigned_to)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <h6 class="timeline-title">Assigned to Agent</h6>
                                <p class="timeline-text text-muted">{{ $lead->assignedUser->name }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@if(auth()->user()->isAdmin())
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the lead "<strong>{{ $lead->name }}</strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Lead</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 3px #e9ecef;
}

.timeline-content {
    padding-left: 15px;
}

.timeline-title {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 600;
}

.timeline-text {
    margin: 0;
    font-size: 0.8rem;
}
</style>
@endsection
