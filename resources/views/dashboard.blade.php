@extends('layouts.app')

@section('title', 'Dashboard - Mini CRM')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            @if(auth()->user()->isAdmin())
            <a href="{{ route('leads.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Lead
            </a>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Leads
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Lead::count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                New Leads
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Lead::where('status', 'new')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Contacted
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Lead::where('status', 'contacted')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-phone fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Closed
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Lead::where('status', 'closed')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Leads -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Leads</h6>
                    <a href="{{ route('leads.index') }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @php
                        $recentLeads = auth()->user()->isAdmin()
                            ? \App\Models\Lead::with('assignedUser')->latest()->take(5)->get()
                            : \App\Models\Lead::where('assigned_to', auth()->id())->latest()->take(5)->get();
                    @endphp

                    @if($recentLeads->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Assigned To</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLeads as $lead)
                                    <tr>
                                        <td>{{ $lead->name }}</td>
                                        <td>{{ $lead->email }}</td>
                                        <td>
                                            <span class="badge bg-{{ $lead->status === 'new' ? 'primary' : ($lead->status === 'contacted' ? 'warning' : 'success') }}">
                                                {{ ucfirst($lead->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($lead->assignedUser)
                                                {{ $lead->assignedUser->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>{{ $lead->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->user()->isAdmin() || $lead->assigned_to === auth()->id())
                                            <a href="{{ route('leads.edit', $lead) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No leads found.</p>
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
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('leads.index') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-list me-2"></i>View All Leads
                            </a>
                        </div>
                        @if(auth()->user()->isAdmin())
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('leads.create') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-plus me-2"></i>Create Lead
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('leads.index') }}?status=new" class="btn btn-outline-info w-100">
                                <i class="fas fa-star me-2"></i>New Leads
                            </a>
                        </div>
                        @endif
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('leads.index') }}?status=contacted" class="btn btn-outline-warning w-100">
                                <i class="fas fa-phone me-2"></i>Contacted Leads
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
