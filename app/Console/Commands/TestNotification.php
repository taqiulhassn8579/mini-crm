<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadAssignedNotification;
use Illuminate\Console\Command;

class TestNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification {--lead-id=} {--agent-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the lead assignment notification system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Lead Assignment Notification System...');

        // Get or create test data
        $leadId = $this->option('lead-id');
        $agentId = $this->option('agent-id');

        if (!$leadId) {
            // Create a test lead
            $lead = Lead::create([
                'name' => 'Test Lead ' . now()->format('Y-m-d H:i:s'),
                'email' => 'test@example.com',
                'phone' => '123-456-7890',
                'status' => 'new',
                'notes' => 'This is a test lead for notification testing',
            ]);
            $this->info("Created test lead: {$lead->name} (ID: {$lead->id})");
        } else {
            $lead = Lead::find($leadId);
            if (!$lead) {
                $this->error("Lead with ID {$leadId} not found!");
                return 1;
            }
            $this->info("Using existing lead: {$lead->name} (ID: {$lead->id})");
        }

        if (!$agentId) {
            // Find an agent user
            $agent = User::where('role', 'agent')->first();
            if (!$agent) {
                $this->error("No agent users found! Please create an agent user first.");
                return 1;
            }
            $this->info("Using agent: {$agent->name} (ID: {$agent->id})");
        } else {
            $agent = User::find($agentId);
            if (!$agent) {
                $this->error("User with ID {$agentId} not found!");
                return 1;
            }
            if ($agent->role !== 'agent') {
                $this->warn("User {$agent->name} is not an agent (role: {$agent->role})");
            }
            $this->info("Using user: {$agent->name} (ID: {$agent->id})");
        }

        // Assign the lead to the agent
        $lead->update(['assigned_to' => $agent->id]);
        $this->info("Assigned lead to agent");

        // Send the notification
        try {
            $agent->notify(new LeadAssignedNotification($lead));
            $this->info("✅ Notification sent successfully!");
            $this->info("Check the jobs table to see the queued notification");
            $this->info("Run 'php artisan queue:work' to process the queue");
        } catch (\Exception $e) {
            $this->error("❌ Error sending notification: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
