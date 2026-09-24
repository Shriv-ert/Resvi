<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Illuminate\Console\Command;

class SyncReservationStatusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reservations:sync-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize automatic time-based reservation statuses (expired, ongoing, completed)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Synchronizing reservation statuses...');
        Reservation::syncTimeBasedStatuses();
        $this->info('Reservation statuses synchronized successfully.');

        return self::SUCCESS;
    }
}
