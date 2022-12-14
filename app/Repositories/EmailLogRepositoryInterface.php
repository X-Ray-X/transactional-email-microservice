<?php

namespace App\Repositories;

use App\Models\EmailLog;

interface EmailLogRepositoryInterface
{
    /**
     * @param  array  $emailData
     * @return EmailLog
     */
    public function create(array $emailData): EmailLog;

    /**
     * @param  string  $emailId
     * @param $emailData
     * @return bool
     */
    public function update(string $emailId, $emailData): bool;
}
