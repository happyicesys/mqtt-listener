<?php

namespace App\Jobs;

use App\Models\VendData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreVendData implements ShouldQueue
{
    use Queueable;

    protected $vendCode;
    protected $input;

    /**
     * Create a new job instance.
     */
    public function __construct($vendCode, $input)
    {
        $this->vendCode = $vendCode;
        $this->input = $input;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        VendData::create([
            'vend_code' => $this->vendCode,
            'value' => $this->input,
            'type' => $this->input['Type'],
        ]);
    }
}
