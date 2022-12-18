<?php

namespace App\Clients;

use App\DTO\EmailDTO;

interface EmailClientInterface
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
     * @param  EmailDTO  $emailDTO
     * @return bool
     */
    public function send(EmailDTO $emailDTO): bool;
}
