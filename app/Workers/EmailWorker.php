<?php

namespace App\Workers;

use App\Clients\EmailClientInterface;
use App\DTO\EmailDTO;
use App\Enums\EmailLogStatus;
use App\Repositories\EmailLogRepositoryInterface;

class EmailWorker implements EmailWorkerInterface
{
    /**
     * @var EmailClientInterface[]
     */
    private array $mailers;

    /**
     * @var EmailLogRepositoryInterface
     */
    private EmailLogRepositoryInterface $emailLogRepository;

    /**
     * @param  array  $mailers
     * @param  EmailLogRepositoryInterface  $emailLogRepository
     */
    public function __construct(array $mailers, EmailLogRepositoryInterface $emailLogRepository)
    {
        $this->mailers = $mailers;
        $this->emailLogRepository = $emailLogRepository;
    }

    /**
     * Send an email via the first available service.
     *
     * @param  EmailDTO  $emailDTO
     * @return bool
     */
    public function sendEmail(EmailDTO $emailDTO): bool
    {
        foreach ($this->mailers as $mailer) {
            if ($mailer->send($emailDTO)) {

                $this->emailLogRepository->update(
                    $emailDTO->id,
                    [
                        'status' => EmailLogStatus::SENT,
                    ]
                );

                return true;
            }
        }

        $this->emailLogRepository->update(
            $emailDTO->id,
            [
                'status' => EmailLogStatus::FAILED,
            ]
        );

        return false;
    }
}
