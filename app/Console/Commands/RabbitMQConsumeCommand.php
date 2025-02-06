<?php

namespace App\Console\Commands;

use App\Modules\Analytics\Services\RabbitMQConsumer;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Команда для запуска слушателя очереди
 */
class RabbitMQConsumeCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'rabbitmq:consume';

    /**
     * @var string
     */
    protected $description = 'Запускает RabbitMQ Consumer для обработки сообщений';

    /**
     * @return void
     */
    public function handle(): void
    {
        try {
            Log::info('Запускаем RabbitMQ Consumer...');
            (new RabbitMQConsumer())->consume();
        } catch (Exception $exception) {
            Log::error('Ошибка при запуске RabbitMQ Consumer', ['error' => $exception->getMessage()]);
        }
    }
}
