<?php

namespace App\Policies;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeadPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Both admins and agents can view leads list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Lead $lead): bool
    {
        // Admins can view all leads, agents can only view their assigned leads
        return $user->isAdmin() || $lead->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin(); // Only admins can create leads
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Lead $lead): bool
    {
        // Admins can update all leads, agents can only update their assigned leads
        return $user->isAdmin() || $lead->assigned_to === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Lead $lead): bool
    {
        return $user->isAdmin(); // Only admins can delete leads
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Lead $lead): bool
    {
        return $user->isAdmin(); // Only admins can restore leads
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Lead $lead): bool
    {
        return $user->isAdmin(); // Only admins can permanently delete leads
    }

    /**
     * Determine whether the user can assign leads to agents.
     */
    public function assign(User $user): bool
    {
        return $user->isAdmin(); // Only admins can assign leads
    }
}
