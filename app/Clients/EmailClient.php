<?php

namespace App\Clients;

interface EmailClient
{
    /**
     * Get connector client instance.
     *
     * @return mixed
     */
    public function getClient();

    /**
     * Send an email message.
     *
     * @param  array  $email
     * @return bool
     */
    public function send(array $email): bool;
}
