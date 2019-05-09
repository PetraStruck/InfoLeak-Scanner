<?php

namespace App\Jobs;

use App\InfoLeakScan;
use App\Http\Requests\ScanStartRequest;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

class LeakJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $request;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($request)
    {
        $this->request = new ScanStartRequest($request);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info('Starting Scan Job for ' . $this->request->get('url'));
        Log::info('Queue jobs remaining ' . Queue::size($this->queue));

        // NOTE(ya): Default user agent.
        $agent  = file_get_contents(app_path() . "/Libs/default_UA");

        $scan = new InfoLeakScan(
            $this->request->get('url'),
            0,
            $this->request->get('callbackurls', []),
            $this->request->get('userAgent', $agent)
        );

        $scan->scan();
    }

    /**
     * The job failed to process.
     * This will never be called - for now
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        foreach ($this->request->get('callbackurls', []) as $url) {
            Log::info(
                'Job failed: ' . $url . ', error code: ' . json_encode($exception->getMessage())
            );
            try {
                $client = new Client;
                $client->post(
                    $url,
                    [
                        'http_errors' => false,
                        'timeout' => 60,
                        'json' => [
                            'name'         => 'INFOLEAK',
                            'version'      => file_get_contents(base_path('VERSION')),
                            'hasError'     => true,
                            'errorMessage' => $exception->getMessage(),
                            'score'        => 0
                        ],
                    ]
                );
            } catch (\Exception $e) {
                Log::warning('Could not send the failed report to the following callback url: ' . $url);
            }
        }
    }
}