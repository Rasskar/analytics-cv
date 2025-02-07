<?php

namespace App\Console\Commands;

use App\Modules\Analytics\Consumers\AnalyticsQueueConsumer;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Команда для запуска слушателя очереди
 */
class AnalyticsQueueConsumeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'analytics:consume';

    /**
     * @var string
     */
    protected $description = 'Запускаем AnalyticsQueueConsumer для обработки сообщений';

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            Log::channel('analytics-queue-out')->info("Запускаем AnalyticsQueueConsumer");
            (new AnalyticsQueueConsumer())->consume();
        } catch (Exception $exception) {
            Log::channel('analytics-queue-err')->error('Ошибка при запуске AnalyticsQueueConsumer', [
                'error' => $exception->getMessage()
            ]);
        }
    }
}
