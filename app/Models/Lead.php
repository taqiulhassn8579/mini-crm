<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'assigned_to',
        'notes',
    ];

    protected $casts = [
        'assigned_to' => 'integer',
    ];

    /**
     * Get the user assigned to this lead
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to filter by assigned agent
     */
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('assigned_to', $agentId);
    }

    /**
     * Get fillable fields based on user role
     */
    public static function getFillableForRole($userRole)
    {
        if ($userRole === 'admin') {
            return [
                'name',
                'email',
                'phone',
                'status',
                'assigned_to',
                'notes',
            ];
        }
        
        // Agents can only edit these fields
        return [
            'name',
            'email',
            'phone',
            'status',
            'notes',
        ];
    }

    /**
     * Check if field is visible for user role
     */
    public static function isFieldVisible($fieldName, $userRole)
    {
        if ($userRole === 'admin') {
            return true;
        }
        
        // Hide assigned_to field for agents
        if ($fieldName === 'assigned_to') {
            return false;
        }
        
        return true;
    }
}
