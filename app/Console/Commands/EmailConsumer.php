<?php

namespace App\Console\Commands;

use App\Workers\EmailWorkerInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EmailConsumer extends Command
{
    private EmailWorkerInterface $emailWorker;

    /**
     * @var string
     */
    private $queueName = 'email_queue';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consumes emails from the queue';

    /**
     * @param  EmailWorkerInterface  $emailWorker
     */
    public function __construct(EmailWorkerInterface $emailWorker)
    {
        parent::__construct();

        $this->emailWorker = $emailWorker;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        try {
            \Amqp::consume($this->queueName, function ($message, $resolver) {
                $messageData = json_decode($message->body, true);

                $this->emailWorker->sendEmail($messageData);

                $resolver->acknowledge($message);

                $this->info(sprintf(
                    'Email message acknowledged - ID: %s',
                    $messageData['id']
                ));
            });
        } catch (\Exception $exception) {
            Log::error(sprintf(
                'Email Consumer Error: %s',
                $exception->getMessage()
            ));
        }
    }
}
