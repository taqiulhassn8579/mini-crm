@extends('layouts.app')

@section('title', 'Leads - Mini CRM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Leads Management</h1>
        @if(auth()->user()->isAdmin())
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('leads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Lead
            </a>
        </div>
        @endif
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('leads.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Name, email, or phone">
                </div>

                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ request('status') === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                @if(auth()->user()->isAdmin())
                <div class="col-md-2">
                    <label for="agent" class="form-label">Agent</label>
                    <select class="form-select" id="agent" name="agent">
                        <option value="">All Agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('agent') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-2">
                    <label for="sort_by" class="form-label">Sort By</label>
                    <select class="form-select" id="sort_by" name="sort_by">
                        <option value="created_at" {{ request('sort_by') === 'created_at' ? 'selected' : '' }}>Created Date</option>
                        <option value="name" {{ request('sort_by') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="status" {{ request('sort_by') === 'status' ? 'selected' : '' }}>Status</option>
                        <option value="email" {{ request('sort_by') === 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="sort_direction" class="form-label">Order</label>
                    <select class="form-select" id="sort_direction" name="sort_direction">
                        <option value="desc" {{ request('sort_direction') === 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('sort_direction') === 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Leads Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Leads ({{ $leads->total() }} total)
            </h6>
        </div>
        <div class="card-body">
            @if($leads->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leads as $lead)
                            <tr>
                                <td>
                                    <strong>{{ $lead->name }}</strong>
                                    @if($lead->notes)
                                        <i class="fas fa-sticky-note text-muted ms-2" title="{{ Str::limit($lead->notes, 50) }}"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                                </td>
                                <td>
                                    @if($lead->phone)
                                        <a href="tel:{{ $lead->phone }}">{{ $lead->phone }}</a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $lead->status === 'new' ? 'primary' : ($lead->status === 'contacted' ? 'warning' : 'success') }}">
                                        {{ ucfirst($lead->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($lead->assignedUser)
                                        <span class="badge bg-info">{{ $lead->assignedUser->name }}</span>
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>{{ $lead->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->isAdmin() || $lead->assigned_to === auth()->id())
                                        <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                        @if(auth()->user()->isAdmin())
                                        <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this lead?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $leads->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                    <h5 class="text-gray-500">No leads found</h5>
                    <p class="text-gray-400">Try adjusting your search criteria or create a new lead.</p>
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('leads.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Your First Lead
                    </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-submit form when filters change
    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('#status, #agent, #sort_by, #sort_direction');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    });
</script>
@endsection
