<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class MakeUserAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:make-admin {identifier : The email or ID of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a user an admin by email or ID';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $identifier = $this->argument('identifier');
        
        // Try to find user by ID first, then by email
        $user = is_numeric($identifier) 
            ? User::find($identifier)
            : User::where('email', $identifier)->first();
        
        if (!$user) {
            $this->error("User not found: {$identifier}");
            return 1;
        }
        
        if ($user->is_admin == 1) {
            $this->info("User {$user->email} is already an admin.");
            return 0;
        }
        
        $user->is_admin = 1;
        $user->save();
        
        $this->info("✓ User {$user->email} (ID: {$user->id}) is now an admin!");
        $this->info("You can now access the admin panel at: /admin/dashboard");
        
        return 0;
    }
}
