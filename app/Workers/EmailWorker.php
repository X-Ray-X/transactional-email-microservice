<?php

namespace App\Workers;

use App\Clients\EmailClient;

class EmailWorker implements EmailWorkerInterface
{
    /**
     * @var EmailClient[]
     */
    private array $mailers;

    /**
     * @param array  $mailers
     */
    public function __construct(array $mailers)
    {
        $this->mailers = $mailers;
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
                return true;
            }
        }

        return false;
    }
}
