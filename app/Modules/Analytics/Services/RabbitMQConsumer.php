<?php

namespace App\Modules\Analytics\Services;

use App\Modules\Analytics\Jobs\ProcessAnalyticQueueJob;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * Читаем очередь RabbitMq проекта ResumeGO и отправляем сообщения очереди на обработку
 */
class RabbitMQConsumer
{
    /**
     * @return void
     */
    public function consume(): void
    {
        try {
            Log::info("RabbitMQConsumer: Подключаемся к очереди");

            $connection = new AMQPStreamConnection(
                env('RABBITMQ_HOST', 'localhost'),
                env('RABBITMQ_PORT', 5672),
                env('RABBITMQ_USER', 'guest'),
                env('RABBITMQ_PASSWORD', 'guest')
            );

            $channel = $connection->channel();
            $queueName = env('RABBITMQ_QUEUE', 'default_queue');
            $messages = [];

            while (true) {
                $msg = $channel->basic_get($queueName);

                if (!$msg) {
                    break;
                }

                $messageBody = json_decode($msg->body, true);;
                $messages[] = $messageBody;

                Log::info('Получено сообщение', ['data' => $messageBody]);

                // Пока не отправляем ACK (оставляем сообщения в очереди)
                // $channel->basic_ack($msg->delivery_info['delivery_tag']);
            }

            Log::info("RabbitMQConsumer: Закрываем соединение к очереди");
            $channel->close();
            $connection->close();

            Log::info("Всего получено сообщений: " . count($messages));

            if (empty($messages)) {
                return;
            }

            ProcessAnalyticQueueJob::dispatch($messages);
        } catch (\Exception $e) {
            Log::error('Ошибка в RabbitMQConsumer', ['error' => $e->getMessage()]);
        }
    }
}
