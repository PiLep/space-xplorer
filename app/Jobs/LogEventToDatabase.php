<?php

namespace App\Jobs;

use App\Models\EventLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class LogEventToDatabase implements ShouldQueue, ShouldQueueAfterCommit
{
    use Queueable;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 10;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $eventType,
        public ?string $userId,
        public array $eventData,
        public ?string $ipAddress,
        public ?string $userAgent,
        public ?string $sessionId
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            EventLog::create([
                'event_type' => $this->eventType,
                'user_id' => $this->normalizeUserId($this->userId),
                'event_data' => $this->eventData,
                'ip_address' => $this->ipAddress,
                'user_agent' => $this->userAgent,
                'session_id' => $this->sessionId,
            ]);
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas l'application
            Log::error('Failed to log event to database', [
                'event_type' => $this->eventType,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw pour que le job soit marqué comme failed et retenté
            throw $e;
        }
    }

    /**
     * Normalize user ID: convert empty strings to null.
     */
    protected function normalizeUserId(?string $userId): ?string
    {
        return ($userId === null || $userId === '') ? null : $userId;
    }
}
