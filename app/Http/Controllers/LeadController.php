<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadRequest;
use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LeadController extends Controller
{
    public function __construct()
    {
        // Authorization will be handled in individual methods
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if user can view leads
        if (!auth()->user()->can('viewAny', Lead::class)) {
            abort(403, 'Unauthorized action.');
        }

        $query = Lead::with('assignedUser');

        // Apply filters
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('agent')) {
            $query->byAgent($request->agent);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // If user is agent, only show their leads
        if (auth()->user()->isAgent()) {
            $query->where('assigned_to', auth()->id());
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $leads = $query->paginate(15);

        if ($request->expectsJson()) {
            return response()->json($leads);
        }

        $agents = User::where('role', 'agent')->get();
        return view('leads.index', compact('leads', 'agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user can create leads
        if (!auth()->user()->can('create', Lead::class)) {
            abort(403, 'Unauthorized action.');
        }

        $agents = User::where('role', 'agent')->get();
        return view('leads.create', compact('agents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeadRequest $request)
    {
        // Check if user can create leads
        if (!auth()->user()->can('create', Lead::class)) {
            abort(403, 'Unauthorized action.');
        }

        // Filter data based on user role
        $userRole = auth()->user()->role;
        $fillableFields = Lead::getFillableForRole($userRole);
        $data = array_intersect_key($request->validated(), array_flip($fillableFields));

        $lead = Lead::create($data);

        // Send notification if lead is assigned to an agent
        if ($lead->assigned_to) {
            $agent = User::find($lead->assigned_to);
            $agent->notify(new LeadAssignedNotification($lead));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Lead created successfully',
                'lead' => $lead->load('assignedUser')
            ], 201);
        }

        return redirect()->route('leads.index')->with('success', 'Lead created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        // Check if user can view this lead
        if (!auth()->user()->can('view', $lead)) {
            abort(403, 'Unauthorized action.');
        }

        $lead->load('assignedUser');

        if (request()->expectsJson()) {
            return response()->json($lead);
        }

        return view('leads.show', compact('lead'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        // Check if user can update this lead
        if (!auth()->user()->can('update', $lead)) {
            abort(403, 'Unauthorized action.');
        }

        $agents = User::where('role', 'agent')->get();
        return view('leads.edit', compact('lead', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadRequest $request, Lead $lead)
    {
        // Check if user can update this lead
        if (!auth()->user()->can('update', $lead)) {
            abort(403, 'Unauthorized action.');
        }

        // Filter data based on user role
        $userRole = auth()->user()->role;
        $fillableFields = Lead::getFillableForRole($userRole);
        $data = array_intersect_key($request->validated(), array_flip($fillableFields));

        $oldAssignedTo = $lead->assigned_to;
        $lead->update($data);

        // Send notification if lead assignment changed
        if ($lead->assigned_to && $lead->assigned_to !== $oldAssignedTo) {
            $agent = User::find($lead->assigned_to);
            $agent->notify(new LeadAssignedNotification($lead));
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Lead updated successfully',
                'lead' => $lead->load('assignedUser')
            ]);
        }

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        // Check if user can delete this lead
        if (!auth()->user()->can('delete', $lead)) {
            abort(403, 'Unauthorized action.');
        }

        $lead->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'Lead deleted successfully']);
        }

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully');
    }

    /**
     * API endpoint for leads with filters and pagination
     */
    public function apiIndex(Request $request): JsonResponse
    {
        // Check if user can view leads
        if (!auth()->user()->can('viewAny', Lead::class)) {
            abort(403, 'Unauthorized action.');
        }

        return $this->index($request);
    }

    /**
     * API endpoint for creating leads
     */
    public function apiStore(LeadRequest $request): JsonResponse
    {
        // Check if user can create leads
        if (!auth()->user()->can('create', Lead::class)) {
            abort(403, 'Unauthorized action.');
        }

        return $this->store($request);
    }

    /**
     * API endpoint for updating leads
     */
    public function apiUpdate(LeadRequest $request, Lead $lead): JsonResponse
    {
        // Check if user can update this lead
        if (!auth()->user()->can('update', $lead)) {
            abort(403, 'Unauthorized action.');
        }

        return $this->update($request, $lead);
    }

    /**
     * API endpoint for showing a lead
     */
    public function apiShow(Lead $lead): JsonResponse
    {
        // Check if user can view this lead
        if (!auth()->user()->can('view', $lead)) {
            abort(403, 'Unauthorized action.');
        }

        return $this->show($lead);
    }

    /**
     * API endpoint for deleting leads
     */
    public function apiDestroy(Lead $lead): JsonResponse
    {
        // Check if user can delete this lead
        if (!auth()->user()->can('delete', $lead)) {
            abort(403, 'Unauthorized action.');
        }

        return $this->destroy($lead);
    }
}
