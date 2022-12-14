<?php

namespace App\Models;

use App\DTO\EmailDTO;
use App\Enums\EmailLogStatus;
use App\Repositories\EmailLogRepositoryInterface;
use Ramsey\Uuid\Uuid;

class Email
{
    private EmailLogRepositoryInterface $emailLogRepository;
    private EmailDTO $emailDTO;

    /**
     * @param  EmailLogRepositoryInterface  $emailLogRepository
     */
    public function __construct(EmailLogRepositoryInterface $emailLogRepository)
    {
        $this->emailLogRepository = $emailLogRepository;
    }

    /**
     * @param  array  $from
     * @param  array  $to
     * @param  string  $subject
     * @param  string  $htmlPart
     * @return $this
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function create(array $from, array $to, string $subject, string $htmlPart): Email
    {
        $this->setEmailDTO($from, $to, $subject, $htmlPart);

        $this->emailLogRepository->create([
            'email_id' => $this->emailDTO->id,
            'status' => EmailLogStatus::NEW,
        ]);

        return $this;
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

    /**
     * @param  array  $from
     * @param  array  $to
     * @param  string  $subject
     * @param  string  $htmlPart
     * @return $this
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    public function setEmailDTO(array $from, array $to, string $subject, string $htmlPart): Email
    {
        $this->emailDTO = new EmailDTO([
            'id' => Uuid::uuid4(),
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'htmlPart' => $htmlPart,
        ]);

        return $this;
    }

    /**
     * @return EmailDTO
     */
    public function getEmailDTO(): EmailDTO
    {
        return $this->emailDTO;
    }
}
