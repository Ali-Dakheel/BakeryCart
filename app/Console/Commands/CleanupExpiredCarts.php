<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Cart;
use Illuminate\Console\Command;

final class CleanupExpiredCarts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'carts:cleanup';

    /**
     * The console command description.
     */
    protected $description = 'Delete expired guest carts';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleted = Cart::whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();

        $this->info("Deleted {$deleted} expired carts.");

        return Command::SUCCESS;
    }
}
