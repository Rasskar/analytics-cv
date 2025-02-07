<?php

namespace App\Modules\Analytics\Consumers;

use App\Modules\Analytics\Jobs\ProcessAnalyticQueueJob;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Channel\AbstractChannel;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class AnalyticsQueueConsumer
{
    /**
     * @var AMQPStreamConnection
     */
    private AMQPStreamConnection $connection;
    /**
     * @var AMQPChannel|AbstractChannel
     */
    private AMQPChannel|AbstractChannel $channel;

    /**
     * @var string|mixed
     */
    private string $queueName;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        Log::channel('analytics-queue')->info("AnalyticsQueueConsumer: Подключаемся к очереди");

        $this->connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'localhost'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );

        $this->queueName = env('RABBITMQ_QUEUE', 'analytics_queue');
        $this->channel = $this->connection->channel();
        $this->channel->queue_declare($this->queueName, false, true, false, false);
    }

    public function consume(): void
    {
        try {
            Log::channel('analytics-queue')->info("AnalyticsQueueConsumer: Начинаем слушать очередь");

            while (true) {
                $message = $this->channel->basic_get($this->queueName);

                if ($message) {
                    $messageBody = json_decode($message->body, true);

                    Log::channel('analytics-queue')->info('Получено сообщение:', ['data' => $messageBody]);

                    ProcessAnalyticQueueJob::dispatch($messageBody);

                    // Пока не подтверждаем обработку (сообщение остаётся в очереди)
                    // $this->channel->basic_ack($msg->delivery_info['delivery_tag']);
                }
            }
        } catch (\Exception $exception) {
            Log::channel('analytics-queue')->error('Ошибка', ['error' => $exception->getMessage()]);
        }
    }

    public function __destruct()
    {
        Log::channel('analytics-queue')->info('AnalyticsQueueConsumer: Закрываем соединение к очереди');
        $this->channel->close();
        $this->connection->close();
    }
}
