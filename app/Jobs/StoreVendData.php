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
    protected $originalInput;
    protected $processedInput;

    /**
     * Create a new job instance.
     */
    public function __construct($vendCode, $topic, $originalInput, $processedInput)
    {
        $this->vendCode = $vendCode;
        $this->topic = $topic;
        $this->originalInput = $originalInput;
        $this->processedInput = $processedInput;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        VendData::create([
            'vend_code' => $this->vendCode,
            'value' => $this->processedInput,
            'raw' => $this->originalInput,
            'topic' => $this->topic,
            'type' => $this->processedInput['Type'],
        ]);
    }
}
