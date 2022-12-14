<?php

namespace App\Workers;

use App\Clients\EmailClient;
use App\Models\EmailLog;

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
        /** @var EmailLog $emailLog */
        $emailLog = EmailLog::where('email_id', $email['id'])->first();

        foreach ($this->mailers as $mailer) {
            if ($mailer->send($email)) {

                $emailLog->update([
                    'status' => 'SENT',
                ]);

                return true;
            }
        }

        $emailLog->update([
            'status' => 'FAILED',
        ]);

        return false;
    }
}
