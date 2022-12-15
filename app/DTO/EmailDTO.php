<?php

namespace App\DTO;

use Spatie\DataTransferObject\DataTransferObject;

class EmailDTO extends DataTransferObject
{
    public string $id;
    public array $from = [];
    public array $to = [];
    public string $subject;
    public string $htmlPart;

    public function setFrom(string $email, string $name): EmailDTO
    {
        $this->from = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }

    public function setTo(string $email, string $name): EmailDTO
    {
        $this->to = [
            'email' => $email,
            'name' => $name,
        ];

        return $this;
    }
}
