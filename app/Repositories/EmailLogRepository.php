<?php

namespace App\Repositories;

use App\Models\EmailLog;

class EmailLogRepository implements EmailLogRepositoryInterface
{
    /**
     * @param  array  $emailData
     * @return EmailLog
     */
    public function create(array $emailData): EmailLog
    {
        return EmailLog::create($emailData);
    }

    /**
     * @param  string  $emailId
     * @param $emailData
     * @return bool
     */
    public function update(string $emailId, $emailData): bool
    {
        if (empty($emailData)) {
            return true;
        }

        return EmailLog::where('email_id', $emailId)->first()->update($emailData);
    }
}
