<?php

namespace App\Modules\Analytics\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAnalyticQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected array $message
    )
    {
    }

    public function handle(): void
    {
        if (isset($this->message['UserAnalyticQueueMessage'])) {
            Log::info('Обрабатываем UserAnalyticQueueMessage', $this->message['UserAnalyticQueueMessage']);
        } elseif (isset($this->message['CVAnalyticQueueMessage'])) {
            Log::info('Обрабатываем CVAnalyticQueueMessage', $this->message['CVAnalyticQueueMessage']);
        } elseif (isset($this->message['LanguageAnalyticQueueMessage'])) {
            Log::info('Обрабатываем LanguageAnalyticQueueMessage', $this->message['LanguageAnalyticQueueMessage']);
        } elseif (isset($this->message['SkillAnalyticQueueMessage'])) {
            Log::info('Обрабатываем SkillAnalyticQueueMessage', $this->message['SkillAnalyticQueueMessage']);
        } else {
            Log::warning('Неизвестное сообщение', ['message' => $this->message]);
        }
    }
}
