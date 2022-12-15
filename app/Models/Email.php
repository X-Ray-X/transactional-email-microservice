<?php

namespace App\Models;

use App\DTO\EmailDTO;
use App\Enums\EmailLogStatus;
use App\Repositories\EmailLogRepositoryInterface;
use Ramsey\Uuid\Uuid;

class Email
{
    private EmailDTO $emailDTO;
    private EmailLogRepositoryInterface $emailLogRepository;

    /**
     * @param  array  $from
     * @param  array  $to
     * @param  string  $subject
     * @param  string  $htmlPart
     */
    public function __construct(array $from, array $to, string $subject, string $htmlPart)
    {
        $this->emailDTO = new EmailDTO([
            'id' => Uuid::uuid4(),
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'htmlPart' => $htmlPart,
        ]);

        $this->emailLogRepository = app()->make(EmailLogRepositoryInterface::class);

        $this->emailLogRepository->create([
            'email_id' => $this->emailDTO->id,
            'status' => EmailLogStatus::NEW,
        ]);
    }

    /**
     * @return array
     */
    public function send(): array
    {
        $payload = $this->emailDTO->toArray();

        \Amqp::publish('', json_encode($payload), ['queue' => 'email_queue',]);

        $this->emailLogRepository->update(
            $this->emailDTO->id,
            [
                'status' => EmailLogStatus::PROCESSING,
                'request' => json_encode($payload),
            ]
        );

        return $payload;
    }
}
