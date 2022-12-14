<?php

namespace App\Workers;

use App\Clients\EmailClient;
use App\Repositories\EmailLogRepositoryInterface;

class EmailWorker implements EmailWorkerInterface
{
    /**
     * @var EmailClient[]
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
     * @param array  $email
     * @return bool
     */
    public function sendEmail(array $email): bool
    {
        foreach ($this->mailers as $mailer) {
            if ($mailer->send($email)) {

                $this->emailLogRepository->update(
                    $email['id'],
                    [
                        'status' => 'SENT',
                    ]
                );

                return true;
            }
        }

        $this->emailLogRepository->update(
            $email['id'],
            [
                'status' => 'FAILED',
            ]
        );

        return false;
    }
}
