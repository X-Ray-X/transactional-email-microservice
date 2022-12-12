<?php

namespace App\Workers;

interface EmailWorkerInterface
{
    /**
     * @param  array  $email
     * @return mixed
     */
    public function sendEmail(array $email): bool;
}
