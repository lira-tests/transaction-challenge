<?php

namespace App\Jobs;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Illuminate\Http\Request;

class NotifyJobQueue implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $timeout = 10;

    public int $maxExceptions = 3;

    public int $retryAfter = 5;

    protected array $data;

    protected NotificationService $notificationService;

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->notificationService = new NotificationService();
    }

    public function handle() {
        Log::info(implode(', ', $this->data));
        $this->notificationService->send($this->data);
    }
}
