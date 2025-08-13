<?php

// Simple test script to verify the application setup
require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Lead;
use App\Policies\LeadPolicy;

echo "Testing Mini CRM Application...\n\n";

try {
    // Test 1: Check if models can be instantiated
    echo "✅ Testing Models...\n";
    $user = new User();
    $lead = new Lead();
    echo "   Models loaded successfully\n\n";

    // Test 2: Check if policies can be instantiated
    echo "✅ Testing Policies...\n";
    $policy = new LeadPolicy();
    echo "   Policies loaded successfully\n\n";

    // Test 3: Check if controllers can be instantiated
    echo "✅ Testing Controllers...\n";
    $authController = new \App\Http\Controllers\AuthController();
    $leadController = new \App\Http\Controllers\LeadController();
    echo "   Controllers loaded successfully\n\n";

    // Test 4: Check if notifications can be instantiated
    echo "✅ Testing Notifications...\n";
    $notification = new \App\Notifications\LeadAssignedNotification($lead);
    echo "   Notifications loaded successfully\n\n";

    echo "🎉 All tests passed! The application is properly configured.\n";
    echo "\nNext steps:\n";
    echo "1. Run: php artisan migrate:fresh\n";
    echo "2. Run: php artisan db:seed --class=AdminUserSeeder\n";
    echo "3. Run: php artisan serve\n";
    echo "4. Visit: http://localhost:8000\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
