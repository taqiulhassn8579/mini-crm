<?php

/**
 * Demo Script: Lead Assignment Notification System
 *
 * This script demonstrates how the notification system works
 * Run this after setting up the database and creating users
 */

require_once 'vendor/autoload.php';

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;

echo "🎯 Mini CRM - Notification System Demo\n";
echo "=====================================\n\n";

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
    echo "   Admin: {$admin->name} ({$admin->email})\n";
    echo "   Agent: {$agent->name} ({$agent->email})\n\n";

    // Create a demo lead
    echo "📝 Creating demo lead...\n";
    $lead = Lead::create([
        'name' => 'John Smith',
        'email' => 'john.smith@example.com',
        'phone' => '555-123-4567',
        'status' => 'new',
        'notes' => 'Interested in premium package. Follow up required.',
        'assigned_to' => $agent->id,
    ]);

    echo "   Lead created: {$lead->name} (ID: {$lead->id})\n";
    echo "   Assigned to: {$agent->name}\n\n";

    // Send notification
    echo "📧 Sending notification...\n";
    $agent->notify(new LeadAssignedNotification($lead));
    echo "   ✅ Notification queued successfully!\n\n";

    // Show what happens next
    echo "🔄 Next Steps:\n";
    echo "   1. Check the jobs table: SELECT * FROM jobs;\n";
    echo "   2. Process the queue: php artisan queue:work --once\n";
    echo "   3. Check the logs: tail -f storage/logs/laravel.log\n\n";

    // Show notification details
    echo "📋 Notification Details:\n";
    echo "   - Type: Lead Assignment Notification\n";
    echo "   - Recipient: {$agent->name} ({$agent->email})\n";
    echo "   - Lead: {$lead->name} - {$lead->email}\n";
    echo "   - Status: {$lead->status}\n";
    echo "   - Queue: database\n";
    echo "   - ShouldQueue: Yes (for performance)\n\n";

    echo "🎉 Demo completed successfully!\n";
    echo "   The notification system is working correctly.\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
