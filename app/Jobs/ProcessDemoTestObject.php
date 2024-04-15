<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DemoTest;
use Illuminate\Support\Facades\Log;

class ProcessDemoTestObject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    protected $object;
    protected $inquiryId;
    protected $shouldFail;
    protected $totalFailures;

    public function __construct($shouldFail, $inquiryId, $object)
    {
        $this->object = $object;
        $this->inquiryId = $inquiryId;
        $this->shouldFail = $shouldFail;
    }

    public function handle(): bool
    {
        try {
            $this->possibleFailure();
            $this->demoTestProcess($this->object);
            $this->delete();

            return true;
        } catch (\Exception $e) {
            if ($this->attempts() >= $this->tries) {
                $this->fail($e);
            }

            return false;
        } finally {
            Log::info('DemoTest processed for inquiry: ' . $this->inquiryId);
        }
    }

    public function failed(): void
    {
        Log::info('DemoTest processed failed: ' . $this->inquiryId);
    }

    public function retryAfter(): \Illuminate\Support\Carbon
    {
        return now()->addSeconds(
            (int)round(((2 ** $this->attempts()) - 1) / 2)
        );
    }

    protected function possibleFailure(): void
    {
        $failurePercentage = $this->totalFailures / 1 * 100;

        if ($this->shouldFail && $failurePercentage < 10) {
            $this->fail(new \Exception());
        }
    }

    protected function demoTestProcess($object): void
    {
        $demoTest = DemoTest::where('ref', $object['ref'])->first();

        if ($demoTest !== null) {
            $demoTest->update([
                'name' => $object['name'],
                'description' => $object['description'],
                'status' => 'UPDATE',
                'is_active' => false,
            ]);
        } else {
            DemoTest::create([
                'ref' => $object['ref'],
                'name' => $object['name'],
                'status' => 'NEW',
                'is_active' => false,
                'description' => $object['description'],
            ]);
        }
    }
}
