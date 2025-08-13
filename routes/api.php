<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LeadController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes (no authentication required)
Route::post('/login', [AuthController::class, 'apiLogin']);

// Protected API routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // User management
    Route::get('/user', function (Request $request) {
        return response()->json([
            'user' => $request->user(),
            'role' => $request->user()->role,
            'permissions' => [
                'can_create_leads' => $request->user()->can('create', \App\Models\Lead::class),
                'can_delete_leads' => $request->user()->can('delete', \App\Models\Lead::class),
                'can_assign_leads' => $request->user()->can('assign', \App\Models\Lead::class),
            ]
        ]);
    });

    // Authentication
    Route::post('/logout', [AuthController::class, 'apiLogout']);

    // Dashboard
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();

        if ($user->isAdmin()) {
            // Admin sees all leads
            $totalLeads = \App\Models\Lead::count();
            $newLeads = \App\Models\Lead::where('status', 'new')->count();
            $contactedLeads = \App\Models\Lead::where('status', 'contacted')->count();
            $closedLeads = \App\Models\Lead::where('status', 'closed')->count();
            $recentLeads = \App\Models\Lead::with('assignedUser')
                ->latest()
                ->take(5)
                ->get();
        } else {
            // Agent sees only assigned leads
            $totalLeads = \App\Models\Lead::where('assigned_to', $user->id)->count();
            $newLeads = \App\Models\Lead::where('assigned_to', $user->id)->where('status', 'new')->count();
            $contactedLeads = \App\Models\Lead::where('assigned_to', $user->id)->where('status', 'contacted')->count();
            $closedLeads = \App\Models\Lead::where('assigned_to', $user->id)->where('status', 'closed')->count();
            $recentLeads = \App\Models\Lead::with('assignedUser')
                ->where('assigned_to', $user->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return response()->json([
            'stats' => [
                'total_leads' => $totalLeads,
                'new_leads' => $newLeads,
                'contacted_leads' => $contactedLeads,
                'closed_leads' => $closedLeads,
            ],
            'recent_leads' => $recentLeads,
            'user_role' => $user->role,
            'user_name' => $user->name,
        ]);
    });

    // Lead management
    Route::get('/leads', [LeadController::class, 'apiIndex']);
    Route::post('/leads', [LeadController::class, 'apiStore']);
    Route::get('/leads/{lead}', [LeadController::class, 'apiShow']);
    Route::put('/leads/{lead}', [LeadController::class, 'apiUpdate']);
    Route::delete('/leads/{lead}', [LeadController::class, 'apiDestroy']);
});
