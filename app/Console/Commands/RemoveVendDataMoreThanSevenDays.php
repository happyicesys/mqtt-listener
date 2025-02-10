<?php

namespace App\Console\Commands;

use App\Models\VendData;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RemoveVendDataMoreThanSevenDays extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:vend-data-more-than-seven-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // delete vend data older than 7 days
        $vendData = VendData::where('created_at', '<', Carbon::now()->subDays(7))->delete();
    }
}
