<?php

namespace App\Enums;

enum EmailLogStatus: string
{
    case NEW = 'new';
    case PROCESSING = 'processing';
    case SENT = 'sent';
    case FAILED = 'failed';
}
