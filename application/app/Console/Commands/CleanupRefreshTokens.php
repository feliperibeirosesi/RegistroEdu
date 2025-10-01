<?php

namespace App\Console\Commands;

use App\Models\RefreshToken;
use Illuminate\Console\Command;

class CleanupRefreshTokens extends Command
{
    protected $signature = 'tokens:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Clean up expired refresh tokens';

    public function handle()
    {
        $expiredCount = RefreshToken::expired()->count();

        if ($expiredCount === 0) {
            $this->info('No expired tokens found.');

            return;
        }

        if ($this->option('dry-run')) {
            $this->info("Would delete {$expiredCount} expired tokens.");

            return;
        }

        $deleted = RefreshToken::cleanupExpired();
        $this->info("Deleted {$deleted} expired refresh tokens.");
    }
}
