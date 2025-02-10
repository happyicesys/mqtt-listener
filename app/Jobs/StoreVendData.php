<?php

namespace App\Jobs;

use App\Models\VendData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreVendData implements ShouldQueue
{
    use Queueable;

    protected $vendCode;
    protected $topic;
    protected $input;

    /**
     * Create a new job instance.
     */
    public function __construct($vendCode, $topic, $input)
    {
        $this->vendCode = $vendCode;
        $this->topic = $topic;
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
            'topic' => $this->topic,
            'type' => $this->input['Type'],
        ]);
    }
}
