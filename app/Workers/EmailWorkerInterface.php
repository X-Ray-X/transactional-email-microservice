<?php

namespace App\Workers;

use App\DTO\EmailDTO;

interface EmailWorkerInterface
{
    /**
     * @param  EmailDTO  $emailDTO
     * @return mixed
     */
    public function sendEmail(EmailDTO $emailDTO): bool;
}
