<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\DemoTestInquiry;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessDemoTest implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $inquiryId;
    protected $shouldFail;

    public function __construct($inquiryId)
    {
        $this->inquiryId = $inquiryId;
        $this->shouldFail = env('FAIL_INDIVIDUAL_JOBS', false);
    }

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        $inquiry = DemoTestInquiry::find($this->inquiryId);
        $payload = $inquiry->payload;
        $batchJobs = [];

        foreach ($payload as $object) {
            $batchJobs[] = new ProcessDemoTestObject($this->shouldFail, $this->inquiryId, $object);
        }

        Bus::batch($batchJobs)
            ->then(function (Batch $batch) use ($inquiry) {
                $inquiry->status = 'PROCESSED';
            })
            ->catch(function (Batch $batch, Throwable $e) use ($inquiry) {
                $inquiry->status = 'FAILED';
            })
            ->finally(function (Batch $batch) use ($inquiry) {
                if($batch->finished()) {
                    $inquiry->items_processed_count = $batch->totalJobs - $batch->failedJobs;
                    $inquiry->items_failed_count = $batch->failedJobs;
                    $inquiry->save();
                }
        })
            ->name($this->inquiryId)
            ->dispatch();

        Log::info('DemoTest processed for inquiry: ' . $this->inquiryId);
    }
}
