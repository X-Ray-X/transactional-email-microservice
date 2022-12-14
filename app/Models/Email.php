<?php

namespace App\Models;

use App\Enums\EmailLogStatus;
use App\Repositories\EmailLogRepositoryInterface;
use Ramsey\Uuid\Uuid;

class Email
{
    private string $id;
    private array $from;
    private array $to;
    private string $subject;
    private string $htmlPart;
    private EmailLogRepositoryInterface $emailLogRepository;

    /**
     * @param  array  $from
     * @param  array  $to
     * @param  string  $subject
     * @param  string  $htmlPart
     */
    public function __construct(array $from, array $to, string $subject, string $htmlPart)
    {
        $this->id = Uuid::uuid4();
        $this->from = $from;
        $this->to = $to;
        $this->subject = $subject;
        $this->htmlPart = $htmlPart;

        $this->emailLogRepository = app()->make(EmailLogRepositoryInterface::class);

        $this->emailLogRepository->create([
            'email_id' => $this->id,
            'status' => EmailLogStatus::NEW,
        ]);
    }

    public function send(): array
    {
        $payload = [
            'id' => $this->id,
            'from' => $this->from,
            'to' => $this->to,
            'subject' => $this->subject,
            'htmlPart' => $this->htmlPart,
        ];

        \Amqp::publish('', json_encode($payload), ['queue' => 'email_queue',]);

        $this->emailLogRepository->update(
            $this->id,
            [
                'status' => EmailLogStatus::PROCESSING,
                'request' => json_encode($payload),
            ]
        );

        return $payload;
    }
}
