<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DemoTest;
use App\Models\DemoTestInquiry;
use Illuminate\Support\Facades\Log;

class ProcessDemoTest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    protected $inquiryId;
    protected $shouldFail;
    protected $totalJobs;
    protected $totalFailures;

    public function __construct($inquiryId)
    {
        $this->inquiryId = $inquiryId;
        $this->shouldFail = env('FAIL_INDIVIDUAL_JOBS', false);
    }

    public function handle()
    {
        $inquiry = DemoTestInquiry::find($this->inquiryId);

        $this->totalJobs = $inquiry->itemsTotalCount;
        $this->totalFailures = $inquiry->itemsTotalCount;

        $payload = $inquiry->payload;

        $itemsProcessedCount = 0;
        $itemsFailedCount = 0;

        foreach ($payload as $object) {
            $success = $this->processDemoTestObject($object);

            if ($success) {
                $itemsProcessedCount++;
            } else {
                $itemsFailedCount++;
            }
        }

        if (($itemsProcessedCount + $itemsFailedCount) === $inquiry->items_total_count) {
            $inquiry->status = 'PROCESSED';
        } else {
            $inquiry->status = 'FAILED';
        }

        $inquiry->items_processed_count = $itemsProcessedCount;
        $inquiry->items_failed_count = $itemsFailedCount;
        $inquiry->save();

        Log::info('DemoTest processed for inquiry: ' . $this->inquiryId);
    }

    public function failed()
    {
        Log::info('DemoTest processed failed: ' . $this->inquiryId);
    }

    public function retryAfter()
    {
        return now()->addSeconds(
            (int)round(((2 ** $this->attempts()) - 1) / 2)
        );
    }

    protected function processDemoTestObject($object)
    {
        try {
            $this->possibleFailure();
            $this->demoTestProcessin($object);
            $this->delete();

            return true;
        } catch (\Exception $e) {
            if ($this->attempts() >= $this->tries) {
                $this->fail($e);
            }

            return false;
        }
    }

    protected function possibleFailure() {
        $failurePercentage = $this->totalFailures / 1 * 100;

        if ($this->shouldFail && $failurePercentage < 10) {
            $this->fail(new \Exception());
        }
    }

    protected function demoTestProcessin($object)
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
