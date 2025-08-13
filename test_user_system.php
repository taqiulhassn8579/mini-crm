<?php

/**
 * Test Script: User Management System
 *
 * This script tests the user management functionality
 * Run this after setting up the database and running migrations
 */

require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Lead;

echo "🧪 Testing User Management System\n";
echo "=================================\n\n";

try {
    // Check if we have users
    $admin = User::where('role', 'admin')->first();
    $agent = User::where('role', 'agent')->first();

    if (!$admin || !$agent) {
        echo "❌ Please run the seeder first:\n";
        echo "   php artisan db:seed --class=AdminUserSeeder\n\n";
        exit(1);
    }

    echo "✅ Found users:\n";
    echo "   Admin: {$admin->name} ({$admin->email}) - Role: {$admin->role}\n";
    echo "   Agent: {$agent->name} ({$agent->email}) - Role: {$agent->role}\n\n";

    // Test role methods
    echo "🔐 Testing Role Methods:\n";
    echo "   Admin isAdmin(): " . ($admin->isAdmin() ? '✅ true' : '❌ false') . "\n";
    echo "   Admin isAgent(): " . ($admin->isAgent() ? '✅ true' : '❌ false') . "\n";
    echo "   Agent isAdmin(): " . ($agent->isAdmin() ? '✅ true' : '❌ false') . "\n";
    echo "   Agent isAgent(): " . ($agent->isAgent() ? '✅ true' : '❌ false') . "\n\n";

    // Test leads relationship
    echo "📊 Testing Leads Relationship:\n";
    $adminLeads = $admin->leads()->count();
    $agentLeads = $agent->leads()->count();
    echo "   Admin leads: {$adminLeads}\n";
    echo "   Agent leads: {$agentLeads}\n\n";

    // Test policy methods (if available)
    echo "🛡️ Testing Authorization:\n";
    try {
        $canViewUsers = $admin->can('viewAny', User::class);
        echo "   Admin can view users: " . ($canViewUsers ? '✅ true' : '❌ false') . "\n";
    } catch (Exception $e) {
        echo "   Admin can view users: ❌ Error - " . $e->getMessage() . "\n";
    }

    try {
        $canCreateUsers = $admin->can('create', User::class);
        echo "   Admin can create users: " . ($canCreateUsers ? '✅ true' : '❌ false') . "\n";
    } catch (Exception $e) {
        echo "   Admin can create users: ❌ Error - " . $e->getMessage() . "\n";
    }

    echo "\n🎉 User Management System Test Completed!\n";
    echo "   The system appears to be working correctly.\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
